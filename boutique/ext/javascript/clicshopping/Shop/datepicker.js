$(function() {
  $('#datepicker_start').datepicker({
    dateFormat: 'mm/dd/yy',
    changeMonth: true,
    changeYear: true,
    yearRange: '-100:+0'
  });
});

$(function() {
  $('#datepicker_expire').datepicker({
    dateFormat: 'mm/dd/yyyy',
    changeMonth: true,
    changeYear: true,
    yearRange: '-100:+0'
  });
});
