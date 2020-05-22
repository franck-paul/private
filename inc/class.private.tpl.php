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

class tplPrivate
{
    public static function PrivateMsg($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$GLOBALS[\'core\']->blog->settings->private->message') . '; ?>';
    }

    public static function PrivateReqPage($attr)
    {
        return '<?php echo(isset($_SERVER[\'REQUEST_URI\'])
            ? html::escapeHTML($_SERVER[\'REQUEST_URI\'])
            : $core->blog->url); ?>';
    }
}
