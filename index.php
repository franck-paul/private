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

if (!defined('DC_CONTEXT_ADMIN')) {exit;}

// Getting current settings
$page_title           = __('Private mode');
$s                    = &$core->blog->settings->private;
$private_flag         = (boolean) $s->private_flag;
$private_conauto_flag = (boolean) $s->private_conauto_flag;
$message              = $s->message;
$feed                 = $core->blog->url . $core->url->getURLFor('feed', 'atom');
$comments_feed        = $core->blog->url . $core->url->getURLFor('feed', 'atom/comments');
$redirect_url         = $s->redirect_url;
// editeur pour le message
$post_editor = $core->auth->getOption('editor');
$new_feeds   = $admin_post_behavior   = '';

$img       = '<img alt="%1$s" title="%1$s" src="index.php?pf=private/%2$s" />';
$img_title = ($private_flag) ? sprintf($img, __('Protected'), 'icon-alt.png') : sprintf($img, __('Non protected'), 'icon.png');

if ($post_editor) {
    $admin_post_behavior = $core->callBehavior(
        'adminPostEditor',
        $post_editor['xhtml'],
        'private_page_message',
        array('#private_page_message')
    );
}

if (!empty($_POST['saveconfig'])) {
    try {
        $private_flag         = (empty($_POST['private_flag'])) ? false : true;
        $private_conauto_flag = (empty($_POST['private_conauto_flag'])) ? false : true;
        $message              = $_POST['private_page_message'];
        $redirect_url         = $_POST['redirect_url'];

        $s->put('private_flag', $private_flag, 'boolean', 'Private mode activation flag');
        $s->put('private_conauto_flag', $private_conauto_flag, 'boolean', 'Private mode automatic connection option');
        $s->put('message', $message, 'string', 'Private mode public welcome message');
        $s->put('redirect_url', $redirect_url, 'string', 'Private mode redirect URL after disconnection');

        if (!empty($_POST['blog_private_pwd'])) {
            if ($_POST['blog_private_pwd'] != $_POST['blog_private_pwd_c']) {
                $core->error->add(__("Passwords don't match"));
            } else {
                $blog_private_pwd = md5($_POST['blog_private_pwd']);
                $s->put('blog_private_pwd', $blog_private_pwd, 'string', 'Private blog password');
            }
        }
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }

    if (!$core->error->flag()) {
        $core->blog->triggerBlog();
        dcPage::addSuccessNotice(__('Configuration successfully updated.'));
        http::redirect($p_url);
    }
}

if ($s->blog_private_pwd === null) {
    dcPage::addWarningNotice(__('No password set.'));
}

if ($s->private_flag === true) {
    $new_feeds =
    '<h3 class="vertical-separator pretty-title">' . __('Syndication') . '</h3>
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
dcPage::jsLoad('js/jquery/jquery.pwstrength.js') .
'<script type="text/javascript">' . "\n" .
"//<![CDATA[\n" .
"\$(function() {\n" .
"   \$('#blog_private_pwd').pwstrength({texts: ['" .
sprintf(__('Password strength: %s'), __('very weak')) . "', '" .
sprintf(__('Password strength: %s'), __('weak')) . "', '" .
sprintf(__('Password strength: %s'), __('mediocre')) . "', '" .
sprintf(__('Password strength: %s'), __('strong')) . "', '" .
sprintf(__('Password strength: %s'), __('very strong')) . "']});\n" .
    "});" .
    "\n//]]>\n" .
    "</script>\n"; ?>
</head>
<body>
<?php

echo dcPage::breadcrumb(
    array(
        html::escapeHTML($core->blog->name)                                 => '',
        '<span class="page-title">' . $page_title . '</span> ' . $img_title => ''
    )) .
dcPage::notices();

echo
'<div id="private_options">
<form method="post" action="' . $p_url . '">
<p>' .
form::checkbox('private_flag', 1, $private_flag) .
'<label class="classic" for="private_flag"> ' .
__('Enable private mode') . '</label>
</p>';

echo
'<h4 class="vertical-separator pretty-title">' . __('Change blog password') . '</h4>' .
'<div class="pw-table">' .
'<p class="pw-cell"><label for="new_pwd">' . __('New password:') . '</label>' .
form::password('blog_private_pwd', 20, 255, '', '', '', false, ' data-indicator="pwindicator" ') . '</p>' .
'<div id="pwindicator">' .
'    <div class="bar"></div>' .
'    <p class="label no-margin"></p>' .
'</div>' .
'</div>' .
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
'<p>' . form::hidden(array('p'), 'private') .
$core->formNonce() .
'<input type="submit" name="saveconfig" value="' . __('Save') . '" />
</p>
</form>
</div>';

dcPage::helpBlock('privatemode');
?>
</body>
</html>
