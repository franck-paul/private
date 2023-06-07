/*global $, jsToolBar, dotclear */
'use strict';

$(() => {
  // Password strength
  const opts = dotclear.getData('pwstrength');
  dotclear.passwordStrength(opts);

  // HTML text editor
  if (typeof jsToolBar === 'function') {
    $('#private_page_message').each(function () {
      dotclear.tbWidgetText = new jsToolBar(this);
      dotclear.tbWidgetText.context = 'private';
      dotclear.tbWidgetText.draw('xhtml');
    });
  }
});
