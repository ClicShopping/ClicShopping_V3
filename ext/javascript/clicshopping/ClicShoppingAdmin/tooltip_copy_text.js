function copyHtmlCode() {
  var htmlCode = document.getElementById('html-code');
  htmlCode.select();
  document.execCommand('copy');
}

// initialize tooltips
   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
   var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
   return new bootstrap.Tooltip(tooltipTriggerEl);
});
