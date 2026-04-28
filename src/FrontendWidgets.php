<?php

/**
 * @brief private, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\private;

use Dotclear\App;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\None;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    public static function widgetLogout(WidgetsElement $w): string
    {
        if (My::settings()->private_flag) {
            if ($w->offline) {
                return '';
            }

            if (($w->homeonly == 1 && !App::url()->isHome(App::url()->getType())) || ($w->homeonly == 2 && App::url()->isHome(App::url()->getType()))) {
                return '';
            }

            $text  = is_string($text = $w->get('text')) ? $text : '';
            $label = is_string($label = $w->get('label')) ? $label : '';

            $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') .
            (new Set())
                ->items([
                    $text !== '' ?
                        (new Note())
                            ->text($text) :
                        (new None()),
                    (new Form('form_blogout'))
                        ->method('post')
                        ->action(App::blog()->url())
                        ->fields([
                            (new Para())
                                ->class('buttons')
                                ->items([
                                    (new Hidden('blogout', '1')),
                                    (new Submit('submit_blogout', Html::escapeHTML($label)))
                                        ->class(['logout', 'submit']),
                                ]),
                        ]),
                ])
            ->render();

            return $w->renderDiv((bool) $w->content_only, 'blogout ' . $w->class, '', $res);
        }

        return '';
    }
}
