/*global $, dotclear */
'use strict';

$(() => {
  // Password strength
  const opts = dotclear.getData('pwstrength');
  dotclear.passwordStrength(opts);
});
