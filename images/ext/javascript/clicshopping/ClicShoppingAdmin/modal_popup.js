/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

$(document).ready(function () {
  $("#myModal").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$(document).ready(function () {
  $("#myModal1").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$(document).ready(function () {
  $("#myModal2").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$(document).ready(function () {
  $("#myModal3").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$(document).ready(function () {
  $("#myModal4").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$(document).ready(function () {
  $("#myModal5").on("show.bs.modal", function (e) {
    const link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});