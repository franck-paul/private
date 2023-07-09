<?php
/**
 * @brief private, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\private;

use ArrayObject;
use context;
use dcCore;
use dcPublic;
use dcUrlHandlers;
use Dotclear\Database\Session;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\UrlHandler;

class FrontendUrl extends dcUrlHandlers
{
    public static function feedXslt($args)
    {
        self::serveDocument('rss2.xsl', 'text/xml');
    }

    public static function publicFeed($args)
    {
        #Don't reinvent the wheel - take a look to dcUrlHandlers/feed
        $type = null;
        $mime = 'application/xml';

        if (preg_match('#^(atom|rss2)$#', (string) $args, $m)) {
            # Atom or RSS2 ?
            $type = $m[0];
        }

        $tpl = $type == '' ? 'atom' : $type;
        $tpl .= '-pv.xml';

        if ($type == 'atom') {
            $mime = 'application/atom+xml';
        }

        header('X-Robots-Tag: ' . context::robotsPolicy(dcCore::app()->blog->settings->system->robots_policy, ''));
        dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/' . dcPublic::TPL_ROOT);
        self::serveDocument($tpl, $mime);
    }

    public static function privateHandler()
    {
        $settings = dcCore::app()->blog->settings->get(My::id());

        // New temporary UrlHandlers
        $urlp       = new UrlHandler();
        $urlp->mode = dcCore::app()->url->mode;
        $urlp->registerDefault(function () {});
        foreach (dcCore::app()->url->getTypes() as $k => $v) {
            $urlp->register($k, $v['url'], $v['representation'], function () {});
        }

        // Find type
        $urlp->getDocument();
        $type = $urlp->type;
        unset($urlp);

        // Looking for a new template (private.html)
        $tplset = dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(__DIR__ . '/../' . dcPublic::TPL_ROOT . '/' . $tplset)) {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), My::path() . '/' . dcPublic::TPL_ROOT . '/' . $tplset);
        } else {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), My::path() . '/' . dcPublic::TPL_ROOT . '/' . DC_DEFAULT_TPLSET);
        }

        // Load password from configuration
        $password = $settings->blog_private_pwd;

        // Define allowed url->type
        $allowed_types = new ArrayObject(
            [
                'feed', 'xslt', 'tag_feed', 'pubfeed', 'spamfeed',
                'hamfeed', 'trackback', 'preview', 'pagespreview', 'contactme',
                'xmlrpc', 'try',
            ]
        );
        dcCore::app()->callBehavior('initPrivateMode', $allowed_types);

        #Generic behavior
        dcCore::app()->callBehavior('initPrivateHandler', dcCore::app());

        #Let's go : define a new session and start it
        $session = new Session(
            dcCore::app()->con,
            dcCore::app()->prefix . dcCore::SESSION_TABLE_NAME,
            'dc_privateblog_sess_' . dcCore::app()->blog->id,
            '/'
        );
        $session->start();

        if (in_array($type, (array) $allowed_types)) {
            return;
        }
        #Add cookie test (automatic login)
        $cookiepass = 'dc_privateblog_cookie_' . dcCore::app()->blog->id;

        if (!empty($_COOKIE[$cookiepass])) {
            $cookiepassvalue = (($_COOKIE[$cookiepass]) == $password);
        } else {
            $cookiepassvalue = false;
        }

        #Let's rumble session, cookies & conf :)
        if (!isset($_SESSION['sess_blog_private']) || $_SESSION['sess_blog_private'] == '') {
            if ($cookiepassvalue) {
                $_SESSION['sess_blog_private'] = $_COOKIE[$cookiepass];

                return;
            }

            if (!empty($_POST['private_pass'])) {
                if (md5($_POST['private_pass']) == $password) {
                    $_SESSION['sess_blog_private'] = md5($_POST['private_pass']);

                    if (!empty($_POST['pass_remember'])) {
                        setcookie($cookiepass, md5($_POST['private_pass']), ['expires' => time() + 31_536_000, 'path' => '/']);
                    }

                    return;
                }
                dcCore::app()->ctx->form_error = __('Wrong password');
            }
            $session->destroy();
            self::serveDocument('private.html', 'text/html', false);
            # --BEHAVIOR-- publicAfterDocument
            dcCore::app()->callBehavior('publicAfterDocumentV2');
            exit;
        } elseif ($_SESSION['sess_blog_private'] != $password) {
            $session->destroy();
            self::serveDocument('private.html', 'text/html', false);
            # --BEHAVIOR-- publicAfterDocument
            dcCore::app()->callBehavior('publicAfterDocumentV2');
            exit;
        } elseif (isset($_POST['blogout'])) {
            $session->destroy();
            setcookie($cookiepass, 'ciao', ['expires' => time() - 86400, 'path' => '/']);
            // Redirection ??
            if ($settings->redirect_url != '') {
                Http::redirect($settings->redirect_url);
            } else {
                dcCore::app()->ctx->form_error = __('You are now disconnected.');
                self::serveDocument('private.html', 'text/html', false);
                # --BEHAVIOR-- publicAfterDocument
                dcCore::app()->callBehavior('publicAfterDocumentV2');
                exit;
            }
        }
    }
}
