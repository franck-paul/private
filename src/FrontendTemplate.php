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

use dcCore;
use Dotclear\Helper\Html\Html;

class FrontendTemplate
{
    public static function PrivateMsg($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->settings->' . My::id() . '->message') . '; ?>';
    }

    public static function PrivateReqPage()
    {
        return '<?php echo(isset($_SERVER[\'REQUEST_URI\'])
            ? ' . Html::class . '::escapeHTML($_SERVER[\'REQUEST_URI\'])
            : dcCore::app()->blog->url); ?>';
    }
}
