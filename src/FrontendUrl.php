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

        // New temporary Url handler wich void any known URL
        $url_handler = new UrlHandler(App::url()->getMode());
        $url_handler->registerDefault(static function (): void {});
        foreach (App::url()->getTypes() as $k => $v) {
            $url_handler->register($k, $v['url'], $v['representation'], static function (): void {});
        }

        // Find type
        $url_handler->getDocument();
        $type = $url_handler->getType();
        unset($url_handler);

        // Looking for a new template (private.html)
        App::frontend()->template()->appendPath(My::tplPath());

        // Load password from configuration
        $password = $settings->blog_private_pwd;

        // Define allowed url->type
        $allowed_types = new ArrayObject(['feed', 'xslt', 'tag_feed', 'pubfeed', 'spamfeed', 'hamfeed', 'trackback', 'preview', 'pagespreview', 'contactme', 'xmlrpc', 'try']);
        App::behavior()->callBehavior('initPrivateMode', $allowed_types);

        // Generic behavior
        App::behavior()->callBehavior('initPrivateHandler');

        if (in_array($type, (array) $allowed_types)) {
            return '';
        }

        // Add cookie test (automatic login)
        $cookiepass = 'dc_privateblog_cookie_' . App::blog()->id();

        if (isset($_POST['blogout'])) {
            // Disconnect from private blog
            App::session()->destroy();
            setcookie(
                $cookiepass,
                'ciao',
                ['expires' => time() - 86_400, 'path' => '/'],
            );
            // Redirection if set or back to password form
            if ($settings->redirect_url !== '') {
                Http::redirect($settings->redirect_url);
            } else {
                App::frontend()->context()->form_error = __('You are now disconnected.');
                self::redirectToPasswordForm(false);
            }
        }

        // Let's rumble session, cookies & conf :)
        if (App::session()->get('dc_private_blog') == '') {
            // Is any cookie with correct password?
            $cookiepassvalue = isset($_COOKIE[$cookiepass]) && ($_COOKIE[$cookiepass] === $password);
            if ($cookiepassvalue) {
                // Restor cookie in session and everything if fine
                App::session()->set('dc_private_blog', $_COOKIE[$cookiepass]);

                return '';
            }

            if (!empty($_POST['private_pass'])) {
                if (md5((string) $_POST['private_pass']) === $password) {
                    // The given password is ok, store it in session (as md5)
                    App::session()->set('dc_private_blog', md5((string) $_POST['private_pass']));

                    if (!empty($_POST['pass_remember'])) {
                        // Auto-login is requested, create a cookie with the given password (as md5)
                        setcookie(
                            $cookiepass,
                            md5((string) $_POST['private_pass']),
                            ['expires' => time() + 31_536_000, 'path' => '/'],
                        );
                    }

                    // Everything is fine
                    return '';
                }

                App::frontend()->context()->form_error = __('Wrong password');
            }

            // Password given is empty or incorrect, back to the password form
            self::redirectToPasswordForm();
        } elseif (App::session()->get('dc_private_blog') != $password) {
            // A session exists but without the correct password, back to the password form
            self::redirectToPasswordForm();
        }

        // Everything is fine
        return '';
    }

    /**
     * Destroy the session if requested and redirect to password form
     */
    protected static function redirectToPasswordForm(bool $destroy_session = true): never
    {
        if ($destroy_session) {
            App::session()->destroy();
        }
        self::serveDocument('private.html', 'text/html', false);
        # --BEHAVIOR-- publicAfterDocument
        App::behavior()->callBehavior('publicAfterDocumentV2');
        exit;
    }
}
