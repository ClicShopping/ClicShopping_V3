/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

$(function(){

  $('#dob').datepicker({
    format: 'dd/mm/yyyy',
    /*        dateFormat: '<?php echo JQUERY_DATEPICKER_FORMAT; ?>',*/
    changeMonth: true,
    changeYear: true,
    yearRange: '-100:+0'
  });

  var startDate = new Date(20,1,2012);
  var endDate = new Date(25,1,2012);
// disabling dates
  var nowTemp = new Date();
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

  var checkin = $('#dob').datepicker({
    onRender: function(date) {
      return date.valueOf() < now.valueOf() ? 'disabled' : '';
    }
  }).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.date.valueOf()) {
      var newDate = new Date(ev.date)
      newDate.setDate(newDate.getDate() + 1);
      checkout.setValue(newDate);
    }
    checkin.hide();

  }).on('changeDate', function(ev) {
    checkout.hide();
  }).data('datepicker');
})
