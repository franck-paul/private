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
use Dotclear\App;
use Dotclear\Core\Frontend\Ctx;
use Dotclear\Core\Frontend\Url;
use Dotclear\Database\Session;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\UrlHandler;

class FrontendUrl extends Url
{
    /**
     * @param      null|string  $args   The arguments
     */
    public static function feedXslt(?string $args): void
    {
        self::serveDocument('rss2.xsl', 'text/xml');
    }

    /**
     * @param      null|string  $args   The arguments
     */
    public static function publicFeed(?string $args): void
    {
        #Don't reinvent the wheel - take a look to Url/feed
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

        header('X-Robots-Tag: ' . Ctx::robotsPolicy(App::blog()->settings()->system->robots_policy, ''));
        App::frontend()->template()->appendPath(My::tplPath());
        self::serveDocument($tpl, $mime);
    }

    public static function privateHandler(): string
    {
        $settings = My::settings();

        // New temporary UrlHandlers
        $urlp = new UrlHandler(App::url()->getMode());
        $urlp->registerDefault(static function () {
        });
        foreach (App::url()->getTypes() as $k => $v) {
            $urlp->register($k, $v['url'], $v['representation'], static function () {
            });
        }

        // Find type
        $urlp->getDocument();
        $type = $urlp->getType();
        unset($urlp);

        // Looking for a new template (private.html)
        App::frontend()->template()->appendPath(My::tplPath());

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
        App::behavior()->callBehavior('initPrivateMode', $allowed_types);

        #Generic behavior
        App::behavior()->callBehavior('initPrivateHandler');

        #Let's go : define a new session and start it
        $session = new Session(
            App::con(),
            App::con()->prefix() . App::session()::SESSION_TABLE_NAME,
            'dc_privateblog_sess_' . App::blog()->id(),
            '/'
        );
        $session->start();

        if (in_array($type, (array) $allowed_types)) {
            return '';
        }

        #Add cookie test (automatic login)
        $cookiepass = 'dc_privateblog_cookie_' . App::blog()->id();

        $cookiepassvalue = empty($_COOKIE[$cookiepass]) ? false : ($_COOKIE[$cookiepass]) == $password;

        #Let's rumble session, cookies & conf :)
        if (!isset($_SESSION['sess_blog_private']) || $_SESSION['sess_blog_private'] == '') {
            if ($cookiepassvalue) {
                $_SESSION['sess_blog_private'] = $_COOKIE[$cookiepass];

                return '';
            }

            if (!empty($_POST['private_pass'])) {
                if (md5((string) $_POST['private_pass']) == $password) {
                    $_SESSION['sess_blog_private'] = md5((string) $_POST['private_pass']);

                    if (!empty($_POST['pass_remember'])) {
                        setcookie($cookiepass, md5((string) $_POST['private_pass']), [
                            'expires' => time() + 31_536_000,
                            'path'    => '/',
                        ]);
                    }

                    return '';
                }

                App::frontend()->context()->form_error = __('Wrong password');
            }

            $session->destroy();
            self::serveDocument('private.html', 'text/html', false);
            # --BEHAVIOR-- publicAfterDocument
            App::behavior()->callBehavior('publicAfterDocumentV2');
            exit;
        } elseif ($_SESSION['sess_blog_private'] != $password) {
            $session->destroy();
            self::serveDocument('private.html', 'text/html', false);
            # --BEHAVIOR-- publicAfterDocument
            App::behavior()->callBehavior('publicAfterDocumentV2');
            exit;
        } elseif (isset($_POST['blogout'])) {
            $session->destroy();
            setcookie($cookiepass, 'ciao', ['expires' => time() - 86400, 'path' => '/']);
            // Redirection ??
            if ($settings->redirect_url != '') {
                Http::redirect($settings->redirect_url);
            } else {
                App::frontend()->context()->form_error = __('You are now disconnected.');
                self::serveDocument('private.html', 'text/html', false);
                # --BEHAVIOR-- publicAfterDocument
                App::behavior()->callBehavior('publicAfterDocumentV2');
                exit;
            }
        }

        return '';
    }
}
