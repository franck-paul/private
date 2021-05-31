/*global $, dotclear */
'use strict';

$(function () {
  // Password strength
  const opts = dotclear.getData('pwstrength');
  dotclear.passwordStrength(opts);
});
