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
class behaviorsPrivate
{
    public static function publicPrivateFormBeforeContent()
    {
        echo dcCore::app()->blog->settings->private->message;
    }

    public static function publicPrivateFormAfterContent()
    {
        echo '<p><label class="classic">' .
        form::checkbox(['pass_remember'], 1) . ' ' .
        __('Enable automatic connection') . '</label></p>';
    }
}
