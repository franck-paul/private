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

$GLOBALS['__autoload']['behaviorsPrivate'] = dirname(__FILE__) . '/inc/class.private.behaviors.php';
$GLOBALS['__autoload']['tplPrivate']       = dirname(__FILE__) . '/inc/class.private.tpl.php';
$GLOBALS['__autoload']['widgetsPrivate']   = dirname(__FILE__) . '/inc/class.private.widgets.php';
$GLOBALS['__autoload']['urlPrivate']       = dirname(__FILE__) . '/inc/class.private.urlhandlers.php';

require dirname(__FILE__) . '/_widgets.php';

$core->blog->settings->addNamespace('private');

#Rewrite Feeds with new URL and representation
$feeds_url = new ArrayObject(['feed', 'tag_feed']);
$core->callBehavior('initFeedsPrivateMode', $feeds_url);

if ($core->blog->settings->private->private_flag) {
    $privatefeed = $core->blog->settings->private->blog_private_pwd;

    #Obfuscate all feeds URL
    foreach ($core->url->getTypes() as $k => $v) {
        if (in_array($k, (array) $feeds_url)) {
            $core->url->register(
                $k,
                sprintf('%s/%s', $privatefeed, $v['url']),
                sprintf('^%s/%s/(.+)$', $privatefeed, $v['url']),
                $v['handler']
            );
        }
    }

    $core->url->register('pubfeed',
        'feed',
        '^feed/(.+)$',
        ['urlPrivate', 'publicFeed']
    );

    #Trick..
    $core->url->register('xslt', 'feed/rss2/xslt', '^feed/rss2/xslt$', ['urlPrivate', 'feedXslt']);
}
