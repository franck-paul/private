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

if (!defined('DC_RC_PATH')) {return;}

$core->tpl->addValue('PrivateReqPage', array('tplPrivate', 'PrivateReqPage'));
$core->tpl->addValue('PrivateMsg', array('tplPrivate', 'PrivateMsg'));

$s = $core->blog->settings->private;

if ($s->private_flag) {
    $core->addBehavior('publicBeforeDocument', array('urlPrivate', 'privateHandler'));
}

if ($s->private_conauto_flag) {
    $core->addBehavior('publicPrivateFormAfterContent', array('behaviorsPrivate', 'publicPrivateFormAfterContent'));
}

$core->addBehavior('publicPrivateFormBeforeContent', array('behaviorsPrivate', 'publicPrivateFormBeforeContent'));
