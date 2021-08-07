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

class widgetsPrivate
{
    public static function widgetLogout($w)
    {
        if ($GLOBALS['core']->blog->settings->private->private_flag) {
            if ($w->offline) {
                return;
            }

            if (($w->homeonly == 1 && !$core->url->isHome($core->url->type)) || ($w->homeonly == 2 && $core->url->isHome($core->url->type))) {
                return;
            }

            $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '') .
            '<p>' . $w->text . '</p>' .
            '<form action="' . $GLOBALS['core']->blog->url . '" method="post">' .
            '<p class="buttons">' .
            '<input type="hidden" name="blogout" id="blogout" value="" />' .
            '<input type="submit" value="' . html::escapeHTML($w->label) . '" class="logout" /></p>' .
                '</form>';

            return $w->renderDiv($w->content_only, 'blogout ' . $w->class, '', $res);
        }
    }
}
