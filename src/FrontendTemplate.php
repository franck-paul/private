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
use Dotclear\Helper\Html\Html;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function PrivateMsg(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::blog()->settings()->' . My::id() . '->message') . ' ?>';
    }

    public static function PrivateReqPage(): string
    {
        return '<?= (isset($_SERVER[\'REQUEST_URI\']) ? ' . Html::class . '::escapeHTML($_SERVER[\'REQUEST_URI\']) : App::blog()->url()) ?>';
    }
}
