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

$core->addBehavior('initWidgets', array('privateWidgets', 'initWidgets'));

class privateWidgets
{
    public static function initWidgets($w)
    {
        $w->create('privateblog', __('PrivateMode'), array('widgetsPrivate', 'widgetLogout'), null, __('Blog logout'))
            ->setting('title', __('Title:'), __('Blog logout'))
            ->setting('text', __('Text:'), '', 'textarea')
            ->setting('label', __('Button:'), __('Disconnect'))
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}
