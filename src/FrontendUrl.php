<?php

/**
 * @brief private, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\private;

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Frontend\Ctx;
use Dotclear\Core\Url;
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

        $robots_policy = is_string($robots_policy = App::blog()->settings()->system->robots_policy) ? $robots_policy : '';

        header('X-Robots-Tag: ' . Ctx::robotsPolicy($robots_policy, ''));
        App::frontend()->template()->appendPath(My::tplPath());
        self::serveDocument($tpl, $mime);
    }

    public static function privateHandler(): string
    {
        $settings = My::settings();

        // New temporary Url handler wich void any known URL
        $url_handler = new UrlHandler(App::url()->getMode());
        $url_handler->registerDefault(static function (): void {
        });
        foreach (App::url()->getTypes() as $k => $v) {
            $url_handler->register($k, $v['url'], $v['representation'], static function (): void {
            });
        }

        // Find type
        $url_handler->getDocument();
        $type = $url_handler->getType();
        unset($url_handler);

        // Looking for a new template (private.html)
        App::frontend()->template()->appendPath(My::tplPath());

        // Load password from configuration
        $password = is_string($password = $settings->blog_private_pwd) ? $password : '';

        // Define allowed url->type
        $allowed_types = new ArrayObject(['feed', 'xslt', 'tag_feed', 'pubfeed', 'spamfeed', 'hamfeed', 'trackback', 'preview', 'pagespreview', 'contactme', 'xmlrpc']);
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
            if (App::session()->exists()) {
                App::session()->destroy();
            }
            setcookie(
                $cookiepass,
                'ciao',
                ['expires' => time() - 86_400, 'path' => '/'],
            );
            // Redirection if set or back to password form
            $redirect_url = is_string($redirect_url = $settings->redirect_url) ? $redirect_url : '';
            if ($redirect_url !== '') {
                Http::redirect($redirect_url);
            } else {
                App::frontend()->context()->form_error = __('You are now disconnected.');
                self::redirectToPasswordForm(false);
            }
        }

        // Let's rumble session, cookies & conf :)
        if (App::session()->get('dc_private_blog') == '') {
            // Is any cookie with correct password?
            $cookiepassvalue = isset($_COOKIE[$cookiepass]) && is_string($_COOKIE[$cookiepass]) && password_verify($password, $_COOKIE[$cookiepass]);
            if ($cookiepassvalue) {
                // Restore cookie in session and everything if fine
                App::session()->set('dc_private_blog', $_COOKIE[$cookiepass]);

                return '';
            }

            if (!empty($_POST['private_pass'])) {
                $private_pass = is_string($private_pass = $_POST['private_pass']) ? $private_pass : '';
                if (password_verify($private_pass, $password)) {
                    // The given password is ok, store it in session
                    App::session()->set('dc_private_blog', App::auth()->crypt($private_pass));

                    if (!empty($_POST['pass_remember'])) {
                        // Auto-login is requested, create a cookie with the given password
                        setcookie(
                            $cookiepass,
                            //(string) App::auth()->crypt((string) $_POST['private_pass']),
                            (string) App::auth()->crypt($password),
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
        } else {
            $private_blog = is_string($private_blog = App::session()->get('dc_private_blog')) ? $private_blog : '';
            if (!password_verify($password, $private_blog)) {
                // A session exists but without the correct password, back to the password form
                self::redirectToPasswordForm();
            }
        }

        // Everything is fine
        return '';
    }

    /**
     * Destroy the session if requested and redirect to password form
     */
    protected static function redirectToPasswordForm(bool $destroy_session = true): never
    {
        if ($destroy_session && App::session()->exists()) {
            App::session()->destroy();
        }

        self::serveDocument('private.html', 'text/html', false);
        # --BEHAVIOR-- publicAfterDocument
        App::behavior()->callBehavior('publicAfterDocumentV2');
        exit;
    }
}
