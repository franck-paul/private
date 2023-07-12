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
use dcNamespace;
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Password;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Textarea;
use Dotclear\Helper\Html\Form\Url;
use Dotclear\Helper\Html\Html;
use Exception;

class Manage extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::MANAGE);

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        if (!empty($_POST['saveconfig'])) {
            try {
                if (!empty($_POST['private_flag']) && empty($_POST['blog_private_pwd']) && empty($settings->blog_private_pwd)) {
                    dcPage::addErrorNotice(__('No password set.'));
                    dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
                }

                $private_flag         = (empty($_POST['private_flag'])) ? false : true;
                $private_conauto_flag = (empty($_POST['private_conauto_flag'])) ? false : true;
                $message              = $_POST['private_page_message'];
                $redirect_url         = $_POST['redirect_url'];

                $settings->put('private_flag', $private_flag, dcNamespace::NS_BOOL, 'Private mode activation flag');
                $settings->put('private_conauto_flag', $private_conauto_flag, dcNamespace::NS_BOOL, 'Private mode automatic connection option');
                $settings->put('message', $message, dcNamespace::NS_STRING, 'Private mode public welcome message');
                $settings->put('redirect_url', $redirect_url, dcNamespace::NS_STRING, 'Private mode redirect URL after disconnection');

                if (!empty($_POST['blog_private_pwd'])) {
                    if ($_POST['blog_private_pwd'] != $_POST['blog_private_pwd_c']) {
                        dcCore::app()->error->add(__("Passwords don't match"));
                    } else {
                        $blog_private_pwd = md5($_POST['blog_private_pwd']);
                        $settings->put('blog_private_pwd', $blog_private_pwd, dcNamespace::NS_STRING, 'Private blog password');
                    }
                }

                dcCore::app()->blog->triggerBlog();
                dcPage::addSuccessNotice(__('Configuration successfully updated.'));
                dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        // Getting current settings
        $private_flag         = (bool) $settings->private_flag;
        $private_conauto_flag = (bool) $settings->private_conauto_flag;
        $message              = $settings->message;
        $feed                 = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('feed', 'atom');
        $comments_feed        = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('feed', 'atom/comments');
        $redirect_url         = $settings->redirect_url;
        $new_feeds            = '';
        $admin_post_behavior  = '';

        $img       = '<img alt="%1$s" title="%1$s" src="%2$s" />';
        $img_title = ($private_flag) ? sprintf($img, __('Protected'), dcPage::getPF(My::id() . '/icon-alt.svg')) : sprintf($img, __('Non protected'), dcPage::getPF(My::id() . '/icon.svg'));

        if ($settings->blog_private_pwd === null) {
            dcPage::addWarningNotice(__('No password set.'));
        }

        if ($settings->private_flag === true) {
            $new_feeds = '<h3 class="vertical-separator pretty-title">' . __('Syndication') . '</h3>' . "\n" .
            '<p class="warning">' . __('Feeds have changed, new are displayed below.') . '</p>' . "\n" .
            '<ul class="nice">' . "\n" .
            '<li class="feed"><a href="' . $feed . '">' . __('Entries feed') . '</a></li>' . "\n" .
            '<li class="feed"><a href="' . $comments_feed . '">' . __('Comments feed') . '</a></li>' . "\n" .
            '</ul>' . "\n";
        }

        $head = $admin_post_behavior .
            dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
            dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js') .
            dcPage::jsJson('pwstrength', [
                'min' => sprintf(__('Password strength: %s'), __('weak')),
                'avg' => sprintf(__('Password strength: %s'), __('medium')),
                'max' => sprintf(__('Password strength: %s'), __('strong')),
            ]) .
            dcPage::jsLoad('js/pwstrength.js') .
            dcPage::jsModuleLoad(My::id() . '/js/admin.js', dcCore::app()->getVersion(My::id()));

        $rich_editor = dcCore::app()->auth->getOption('editor');
        $rte_flag    = true;
        $rte_flags   = @dcCore::app()->auth->user_prefs->interface->rte_flags;
        if (is_array($rte_flags) && in_array('private', $rte_flags)) {
            $rte_flag = $rte_flags['private'];
        }
        if ($rte_flag) {
            $head .= dcCore::app()->callBehavior(
                'adminPostEditor',
                $rich_editor['xhtml'],
                'private_page_message',
                ['#private_page_message'],
                'xhtml'
            );
        }

        dcPage::openModule(__('Private mode'), $head);

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('Private mode') . $img_title             => '',
            ]
        );
        echo dcPage::notices();

        // Form
        echo (new Form('private'))
            ->action(dcCore::app()->admin->getPageURL())
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
                            ->lang(dcCore::app()->auth->getInfo('user_lang'))
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
                        (new Text(null, $new_feeds)),
                    ]),
                (new Para())->items([
                    (new Submit(['saveconfig']))
                        ->value(__('Save')),
                    dcCore::app()->formNonce(false),
                ]),
            ])
        ->render();

        dcPage::helpBlock('privatemode');

        dcPage::closeModule();
    }
}
