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
use Dotclear\Helper\Process\TraitProcess;

class Prepend
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::PREPEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = My::settings();

        // Rewrite Feeds with new URL and representation
        $feeds_url = new ArrayObject(['feed', 'tag_feed']);
        App::behavior()->callBehavior('initFeedsPrivateMode', $feeds_url);

        if (App::blog()->isDefined() && $settings->private_flag) {
            $privatefeed = $settings->blog_private_pwd;
            // Obfuscate all feeds URL
            foreach (App::url()->getTypes() as $k => $v) {
                if (in_array($k, (array) $feeds_url)) {
                    App::url()->register(
                        $k,
                        sprintf('%s/%s', $privatefeed, $v['url']),
                        sprintf('^%s/%s/(.+)$', $privatefeed, $v['url']),
                        $v['handler']
                    );
                }
            }

            App::url()->register('pubfeed', 'feed', '^feed/(.+)$', FrontendUrl::publicFeed(...));
            App::url()->register('xslt', 'feed/rss2/xslt', '^feed/rss2/xslt$', FrontendUrl::feedXslt(...));
        }

        return true;
    }
}
