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

use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Para;

class FrontendBehaviors
{
    public static function publicPrivateFormBeforeContent()
    {
        echo My::settings()->message;
    }

    public static function publicPrivateFormAfterContent()
    {
        echo (new Para())
        ->items([
            (new Checkbox('pass_remember'))
                ->value(1)
                ->label((new Label(__('Enable automatic connection'), Label::INSIDE_TEXT_AFTER))),
        ])
        ->render();
    }
}
