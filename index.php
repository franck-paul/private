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
if (!defined('DC_CONTEXT_ADMIN')) {
    exit;
}

// Getting current settings
$page_title           = __('Private mode');
$settings             = dcCore::app()->blog->settings->private;
$private_flag         = (bool) $settings->private_flag;
$private_conauto_flag = (bool) $settings->private_conauto_flag;
$message              = $settings->message;
$feed                 = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('feed', 'atom');
$comments_feed        = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('feed', 'atom/comments');
$redirect_url         = $settings->redirect_url;
// editeur pour le message
$post_editor = dcCore::app()->auth->getOption('editor');
$new_feeds   = $admin_post_behavior = '';

$img       = '<img alt="%1$s" title="%1$s" src="index.php?pf=private/%2$s" />';
$img_title = ($private_flag) ? sprintf($img, __('Protected'), 'icon-alt.png') : sprintf($img, __('Non protected'), 'icon.png');

if ($post_editor) {
    $admin_post_behavior = dcCore::app()->callBehavior(
        'adminPostEditor',
        $post_editor['xhtml'],
        'private_page_message',
        ['#private_page_message']
    );
}

if (!empty($_POST['saveconfig'])) {
    if (!empty($_POST['private_flag']) && empty($_POST['blog_private_pwd']) && empty($settings->blog_private_pwd)) {
        dcPage::addErrorNotice(__('No password set.'));
        http::redirect(dcCore::app()->admin->getPageURL());
    }

    try {
        $private_flag         = (empty($_POST['private_flag'])) ? false : true;
        $private_conauto_flag = (empty($_POST['private_conauto_flag'])) ? false : true;
        $message              = $_POST['private_page_message'];
        $redirect_url         = $_POST['redirect_url'];

        $settings->put('private_flag', $private_flag, 'boolean', 'Private mode activation flag');
        $settings->put('private_conauto_flag', $private_conauto_flag, 'boolean', 'Private mode automatic connection option');
        $settings->put('message', $message, 'string', 'Private mode public welcome message');
        $settings->put('redirect_url', $redirect_url, 'string', 'Private mode redirect URL after disconnection');

        if (!empty($_POST['blog_private_pwd'])) {
            if ($_POST['blog_private_pwd'] != $_POST['blog_private_pwd_c']) {
                dcCore::app()->error->add(__("Passwords don't match"));
            } else {
                $blog_private_pwd = md5($_POST['blog_private_pwd']);
                $settings->put('blog_private_pwd', $blog_private_pwd, 'string', 'Private blog password');
            }
        }
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }

    if (!dcCore::app()->error->flag()) {
        dcCore::app()->blog->triggerBlog();
        dcPage::addSuccessNotice(__('Configuration successfully updated.'));
        http::redirect(dcCore::app()->admin->getPageURL());
    }
}

if ($settings->blog_private_pwd === null) {
    dcPage::addWarningNotice(__('No password set.'));
}

if ($settings->private_flag === true) {
    $new_feeds = '<h3 class="vertical-separator pretty-title">' . __('Syndication') . '</h3>
    <p class="warning">' . __('Feeds have changed, new are displayed below.') . '</p>
    <ul class="nice">
    <li class="feed"><a href="' . $feed . '">' . __('Entries feed') . '</a></li>
    <li class="feed"><a href="' . $comments_feed . '">' . __('Comments feed') . '</a></li>
    </ul>';
}

?>
<html>
<head>
<title><?php echo $page_title; ?></title>
    <?php echo dcPage::jsToolBar() .
$admin_post_behavior .
dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js') .
dcPage::jsJson('pwstrength', [
    'min' => sprintf(__('Password strength: %s'), __('weak')),
    'avg' => sprintf(__('Password strength: %s'), __('medium')),
    'max' => sprintf(__('Password strength: %s'), __('strong')),
]) .
dcPage::jsLoad('js/pwstrength.js') .
dcPage::jsModuleLoad('private/js/admin.js', dcCore::app()->getVersion('private'));
?>
</head>
<body>
<?php

echo dcPage::breadcrumb(
    [
        html::escapeHTML(dcCore::app()->blog->name)                        => '',
        '<span class="page-title">' . $page_title . '</span>' . $img_title => '',
    ]
) .
dcPage::notices();

echo
'<div id="private_options">
<form method="post" action="' . dcCore::app()->admin->getPageURL() . '">
<p>' .
form::checkbox('private_flag', 1, $private_flag) .
'<label class="classic" for="private_flag"> ' .
__('Enable private mode') . '</label>
</p>';

echo
'<h4 class="vertical-separator pretty-title">' . __('Change blog password') . '</h4>' .
'<p><label for="new_pwd">' . __('New password:') . '</label>' .
form::password('blog_private_pwd', 20, 255, '', 'pw-strength') . '</p>' .
'<p><label for="blog_private_pwd_c">' . __('Confirm password:') . '</label> ' .
form::password('blog_private_pwd_c', 20, 255) .
'</p>
<p class="area"><label>' .
__('Message:') . '</label>' .
form::textarea('private_page_message', 30, 7, html::escapeHTML($message), 'maximal') .
'</p>
<p>' .
form::checkbox('private_conauto_flag', 1, $private_conauto_flag) .
'<label class="classic" for="private_conauto_flag">' . __('Propose automatic connection to visitors') . '</label>
</p>
<p><label for="redirect_url">' .
__('Redirect URL after disconnection:') . '</label> ' .
form::field('redirect_url', 50, 255, html::escapeHTML($redirect_url)) .
'</p>' .
$new_feeds .
'<p>' . form::hidden(['p'], 'private') .
dcCore::app()->formNonce() .
'<input type="submit" name="saveconfig" value="' . __('Save') . '" />
</p>
</form>
</div>';

dcPage::helpBlock('privatemode');
?>
</body>
</html>
