<?php
/**
 * @brief PrivateMode, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Osku and contributors
 *
 * @copyright Osku
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('DC_RC_PATH')) {return;}

class urlPrivate extends dcUrlHandlers
{
    public static function feedXslt($args)
    {
        self::serveDocument('rss2.xsl', 'text/xml');
    }

    public static function publicFeed($args)
    {
        #Don't reinvent the wheel - take a look to dcUrlHandlers/feed
        global $core, $_ctx;

        $type   = null;
        $params = array();
        $mime   = 'application/xml';

        if (preg_match('#^(atom|rss2)$#', $args, $m)) {
            # Atom or RSS2 ?
            $type = $m[0];
        }

        $tpl = $type == '' ? 'atom' : $type;
        $tpl .= '-pv.xml';

        if ($type == 'atom') {
            $mime = 'application/atom+xml';
        }

        header('X-Robots-Tag: ' . context::robotsPolicy($core->blog->settings->system->robots_policy, ''));
        $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__) . '/default-templates');
        self::serveDocument($tpl, $mime);

        return;
    }

    public static function callbackfoo($args)
    {
        #Woohoo :)
        return;
    }

    public static function privateHandler($args)
    {
        global $core, $_ctx;

        #New temporary urlHandlers
        $urlp       = new urlHandler();
        $urlp->mode = $core->url->mode;
        $urlp->registerDefault(array('urlPrivate', 'callbackfoo'));
        foreach ($core->url->getTypes() as $k => $v) {
            $urlp->register($k, $v['url'], $v['representation'], array('urlPrivate', 'callbackfoo'));
        }

        #Find type
        $urlp->getDocument();
        $type = $urlp->type;
        unset($urlp);

        #Looking for a new template (private.html)
        $tplset = $core->themes->moduleInfo($core->blog->settings->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(dirname(__FILE__) . '/../default-templates/' . $tplset)) {
            $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__) . '/../default-templates/' . $tplset);
        } else {
            $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__) . '/../default-templates/' . DC_DEFAULT_TPLSET);
        }

        #Load password from configuration
        $password = $core->blog->settings->private->blog_private_pwd;

        #Define allowed url->type
        $allowed_types = new ArrayObject(
            array(
                'feed', 'xslt', 'tag_feed', 'pubfeed', 'spamfeed',
                'hamfeed', 'trackback', 'preview', 'pagespreview', 'contactme',
                'rsd', 'xmlrpc'
            )
        );
        $core->callBehavior('initPrivateMode', $allowed_types);

        #Generic behavior
        $core->callBehavior('initPrivateHandler', $core);

        #Let's go : define a new session and start it
        if (!isset($session)) {
            $session = new sessionDB(
                $core->con,
                $core->prefix . 'session',
                'dc_privateblog_sess_' . $core->blog->id,
                '/'
            );
            $session->start();
        }

        if (in_array($type, (array) $allowed_types)) {
            return;
        } else {
            #Add cookie test (automatic login)
            $cookiepass = "dc_privateblog_cookie_" . $core->blog->id;

            if (!empty($_COOKIE[$cookiepass])) {
                $cookiepassvalue = (($_COOKIE[$cookiepass]) == $password);
            } else {
                $cookiepassvalue = false;
            }

            #Let's rumble session, cookies & conf :)
            if (!isset($_SESSION['sess_blog_private']) || $_SESSION['sess_blog_private'] == "") {
                if ($cookiepassvalue != false) {
                    $_SESSION['sess_blog_private'] = $_COOKIE[$cookiepass];
                    return;
                }
                if (!empty($_POST['private_pass'])) {
                    if (md5($_POST['private_pass']) == $password) {
                        $_SESSION['sess_blog_private'] = md5($_POST['private_pass']);

                        if (!empty($_POST['pass_remember'])) {
                            setcookie($cookiepass, md5($_POST['private_pass']), time() + 31536000, '/');
                        }
                        return;
                    }
                    $_ctx->form_error = __('Wrong password');
                }
                $session->destroy();
                self::serveDocument('private.html', 'text/html', false);
                # --BEHAVIOR-- publicAfterDocument
                $core->callBehavior('publicAfterDocument', $core);
                exit;
            } elseif ($_SESSION['sess_blog_private'] != $password) {
                $session->destroy();
                self::serveDocument('private.html', 'text/html', false);
                # --BEHAVIOR-- publicAfterDocument
                $core->callBehavior('publicAfterDocument', $core);
                exit;
            } elseif (isset($_POST['blogout'])) {
                $session->destroy();
                setcookie($cookiepass, 'ciao', time() - 86400, '/');
                // Redirection ??
                if ($core->blog->settings->private->redirect_url != '') {
                    http::redirect($core->blog->settings->private->redirect_url);
                } else {
                    $_ctx->form_error = __('You are now disconnected.');
                    self::serveDocument('private.html', 'text/html', false);
                    # --BEHAVIOR-- publicAfterDocument
                    $core->callBehavior('publicAfterDocument', $core);
                    exit;
                }
            }
            return;
        }
    }
}
