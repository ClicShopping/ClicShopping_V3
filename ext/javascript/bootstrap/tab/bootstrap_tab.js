/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

$(document).ready(function () {

  if (location.hash !== '') {
    $('a[href="' + location.hash + '"]').tab('show');
  }

  $("a[data-bs-toggle='tab']").on("shown.bs.tab", function (e) {
    var hash = $(e.target).prop("href");
    if (hash.substr(0,1) == "#") {
      var position = $(window).scrollTop();
      location.replace("#" + hash.substr(1));
      $(window).scrollTop(position);
    }
  });
});

