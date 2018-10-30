$('body').on('click', '[data-toggle="modal"]', function(){
  $($(this).data("target")+' .modal-body').load($(this).data("remote"));
});
