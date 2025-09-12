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

use Dotclear\App;

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:PrivateMsg value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function PrivateMsg(
        string $_message_,
        array $_params_,
        string $_tag_,
    ): void {
        echo App::frontend()->context()::global_filters(
            $_message_,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:PrivateReqPage value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function PrivateReqPage(
        array $_params_,
        string $_tag_,
    ): void {
        echo isset($_SERVER['REQUEST_URI']) ? \Dotclear\Helper\Html\Html::escapeHTML($_SERVER['REQUEST_URI']) : App::blog()->url();
    }
}
