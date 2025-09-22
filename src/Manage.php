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
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\None;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Password;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Textarea;
use Dotclear\Helper\Html\Form\Ul;
use Dotclear\Helper\Html\Form\Url;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Manage
{
    use TraitProcess;

    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = My::settings();

        if (!empty($_POST['saveconfig'])) {
            try {
                if (!empty($_POST['private_flag']) && empty($_POST['blog_private_pwd']) && empty($settings->blog_private_pwd)) {
                    App::backend()->notices()->addErrorNotice(__('No password set.'));
                    My::redirect();
                }

                $private_flag         = !empty($_POST['private_flag']);
                $private_conauto_flag = !empty($_POST['private_conauto_flag']);
                $message              = $_POST['private_page_message'];
                $redirect_url         = $_POST['redirect_url'];

                $settings->put('private_flag', $private_flag, App::blogWorkspace()::NS_BOOL, 'Private mode activation flag');
                $settings->put('private_conauto_flag', $private_conauto_flag, App::blogWorkspace()::NS_BOOL, 'Private mode automatic connection option');
                $settings->put('message', $message, App::blogWorkspace()::NS_STRING, 'Private mode public welcome message');
                $settings->put('redirect_url', $redirect_url, App::blogWorkspace()::NS_STRING, 'Private mode redirect URL after disconnection');

                if (!empty($_POST['blog_private_pwd'])) {
                    if ($_POST['blog_private_pwd'] != $_POST['blog_private_pwd_c']) {
                        App::error()->add(__("Passwords don't match"));
                    } else {
                        $blog_private_pwd = App::auth()->crypt((string) $_POST['blog_private_pwd']);
                        $settings->put('blog_private_pwd', $blog_private_pwd, App::blogWorkspace()::NS_STRING, 'Private blog password');
                    }
                }

                App::blog()->triggerBlog();
                App::backend()->notices()->addSuccessNotice(__('Configuration successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $settings = My::settings();

        // Getting current settings
        $private_flag         = (bool) $settings->private_flag;
        $private_conauto_flag = (bool) $settings->private_conauto_flag;
        $message              = $settings->message;
        $feed                 = App::blog()->url() . App::url()->getURLFor('feed', 'atom');
        $comments_feed        = App::blog()->url() . App::url()->getURLFor('feed', 'atom/comments');
        $redirect_url         = $settings->redirect_url;
        $new_feeds            = (new None());
        $admin_post_behavior  = '';

        $img_title = (new Img($private_flag ? App::backend()->page()->getPF(My::id() . '/icon-alt.svg') : App::backend()->page()->getPF(My::id() . '/icon.svg')))
            ->alt($private_flag ? __('Protected') : __('Non protected'))
            ->title($private_flag ? __('Protected') : __('Non protected'))
            ->class('private-state')
        ->render();

        if ($settings->blog_private_pwd === null) {
            App::backend()->notices()->addWarningNotice(__('No password set.'));
        }

        if ($settings->private_flag === true) {
            $new_feeds = (new Set())
                ->items([
                    (new Fieldset())
                        ->legend(new Legend(__('Syndication')))
                        ->fields([
                            (new Note())
                                ->class('warning')
                                ->text(__('Feeds have changed, new are displayed below.')),
                            (new Ul())
                                ->class('nice')
                                ->items([
                                    (new Li())
                                        ->class('feed')
                                        ->items([
                                            (new Link())
                                                ->href($feed)
                                                ->text(__('Entries feed')),
                                        ]),
                                    (new Li())
                                        ->class('feed')
                                        ->items([
                                            (new Link())
                                                ->href($comments_feed)
                                                ->text(__('Comments feed')),
                                        ]),
                                ]),
                        ]),
                ]);
        }

        $head = $admin_post_behavior .
            App::backend()->page()->jsLoad('js/jquery/jquery-ui.custom.js') .
            App::backend()->page()->jsLoad('js/jquery/jquery.ui.touch-punch.js') .
            App::backend()->page()->jsJson('pwstrength', [
                'min' => sprintf(__('Password strength: %s'), __('weak')),
                'avg' => sprintf(__('Password strength: %s'), __('medium')),
                'max' => sprintf(__('Password strength: %s'), __('strong')),
            ]) .
            App::backend()->page()->jsLoad('js/pwstrength.js') .
            My::cssLoad('admin.css') .
            My::jsLoad('admin.js');

        $rich_editor = App::auth()->getOption('editor');
        $rte_flag    = true;
        $rte_flags   = @App::auth()->prefs()->interface->rte_flags;
        if (is_array($rte_flags) && in_array('private', $rte_flags)) {
            $rte_flag = $rte_flags['private'];
        }

        if ($rte_flag) {
            $head .= App::behavior()->callBehavior(
                'adminPostEditor',
                $rich_editor['xhtml'],
                'private_page_message',
                ['#private_page_message'],
                'xhtml'
            );
        }

        App::backend()->page()->openModule(My::name(), $head);

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Private mode') . $img_title       => '',
            ]
        );
        echo App::backend()->notices()->getNotices();

        // Form
        echo (new Form('private'))
            ->action(App::backend()->getPageURL())
            ->method('post')
            ->fields([
                (new Para())->items([
                    (new Checkbox('private_flag', $private_flag))
                        ->value(1)
                        ->label((new Label(__('Enable private mode'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Fieldset('password'))
                    ->legend(new Legend(__('Password')))
                    ->fields([
                        (new Para())->items([
                            (new Password('blog_private_pwd'))
                            ->class('pw-strength')
                            ->size(20)
                            ->maxlength(255)
                            ->label((new Label(__('New password:'), Label::OUTSIDE_LABEL_BEFORE))),
                        ]),
                        (new Para())->items([
                            (new Password('blog_private_pwd_c'))
                            ->size(20)
                            ->maxlength(255)
                            ->label((new Label(__('Confirm password:'), Label::OUTSIDE_LABEL_BEFORE))),
                        ]),
                    ]),
                (new Fieldset('options'))
                    ->legend(new Legend(__('Options')))
                    ->fields([
                        (new Para())->items([
                            (new Textarea('private_page_message', Html::escapeHTML($message)))
                            ->cols(60)
                            ->rows(10)
                            ->lang(App::auth()->getInfo('user_lang'))
                            ->spellcheck(true)
                            ->label((new Label(__('Message:'), Label::OUTSIDE_LABEL_BEFORE))),
                        ]),
                        (new Para())->items([
                            (new Checkbox('private_conauto_flag', $private_conauto_flag))
                                ->value(1)
                                ->label((new Label(__('Propose automatic connection to visitors'), Label::INSIDE_TEXT_AFTER))),
                        ]),
                        (new Para())->items([
                            (new Url('redirect_url'))
                            ->value(Html::escapeHTML($redirect_url))
                            ->size(50)
                            ->maxlength(255)
                            ->label((new Label(__('Redirect URL after disconnection:'), Label::OUTSIDE_LABEL_BEFORE))),
                        ]),
                    ]),
                $new_feeds,
                (new Para())->items([
                    (new Submit(['saveconfig']))
                        ->value(__('Save')),
                    ...My::hiddenFields(),
                ]),
            ])
        ->render();

        App::backend()->page()->helpBlock('privatemode');

        App::backend()->page()->closeModule();
    }
}
