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
if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->tpl->addValue('PrivateReqPage', [tplPrivate::class, 'PrivateReqPage']);
dcCore::app()->tpl->addValue('PrivateMsg', [tplPrivate::class, 'PrivateMsg']);

$s = dcCore::app()->blog->settings->private;

if ($s->private_flag) {
    dcCore::app()->addBehavior('publicBeforeDocumentV2', [urlPrivate::class, 'privateHandler']);
}

if ($s->private_conauto_flag) {
    dcCore::app()->addBehavior('publicPrivateFormAfterContent', [behaviorsPrivate::class, 'publicPrivateFormAfterContent']);
}

dcCore::app()->addBehavior('publicPrivateFormBeforeContent', [behaviorsPrivate::class, 'publicPrivateFormBeforeContent']);
