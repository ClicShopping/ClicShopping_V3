$( document ).ready(function() {
  $("#myModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$( document ).ready(function() {
  $("#myModal1").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});

$( document ).ready(function() {
  $("#myModal2").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });
});
