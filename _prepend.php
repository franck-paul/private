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
if (!defined('DC_RC_PATH')) {
    return;
}

Clearbricks::lib()->autoload([
    'behaviorsPrivate' => __DIR__ . '/inc/class.private.behaviors.php',
    'tplPrivate'       => __DIR__ . '/inc/class.private.tpl.php',
    'widgetsPrivate'   => __DIR__ . '/inc/class.private.widgets.php',
    'urlPrivate'       => __DIR__ . '/inc/class.private.urlhandlers.php',
]);

require_once __DIR__ . '/_widgets.php';

#Rewrite Feeds with new URL and representation
$feeds_url = new ArrayObject(['feed', 'tag_feed']);
dcCore::app()->callBehavior('initFeedsPrivateMode', $feeds_url);

if (dcCore::app()->blog->settings->private->private_flag) {
    $privatefeed = dcCore::app()->blog->settings->private->blog_private_pwd;

    #Obfuscate all feeds URL
    foreach (dcCore::app()->url->getTypes() as $k => $v) {
        if (in_array($k, (array) $feeds_url)) {
            dcCore::app()->url->register(
                $k,
                sprintf('%s/%s', $privatefeed, $v['url']),
                sprintf('^%s/%s/(.+)$', $privatefeed, $v['url']),
                $v['handler']
            );
        }
    }

    dcCore::app()->url->register(
        'pubfeed',
        'feed',
        '^feed/(.+)$',
        ['urlPrivate', 'publicFeed']
    );

    #Trick..
    dcCore::app()->url->register('xslt', 'feed/rss2/xslt', '^feed/rss2/xslt$', ['urlPrivate', 'feedXslt']);
}
