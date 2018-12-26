<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>

<!--date -->
<script>
  $(function () {
    $('#customers_dob').datepicker(
        {
          format: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_format'); ?>",
          todayBtn: "linked",
          todayHighlight: true,
          autoclose: true,
          language: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_language'); ?>"
        }
    );

    $('#schdate').datepicker(
        {
          format: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_format'); ?>",
          todayBtn: "linked",
          todayHighlight: true,
          autoclose: true,
          language: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_language'); ?>"
        }
    );

    $('#expdate').datepicker(
        {
          format: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_format'); ?>",
          todayBtn: "linked",
          todayHighlight: true,
          autoclose: true,
          language: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_language'); ?>"
        }
    );

    $('#products_date_available').datepicker({
      format: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_format'); ?>",
      todayBtn: "linked",
      todayHighlight: true,
      autoclose: true,
      language: "<?php echo CLICSHOPPING::getDef('jquery_datepicker_language'); ?>"
    });

// disabling dates
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    /* categories.php */
    var checkin = $('#products_date_available').datepicker({
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

    /* products favorites start date  && specials*/
    var checkin = $('#schdate').datepicker({
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


    /* products favorites end date && specials*/
    var checkin = $('#expdate').datepicker({
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

  });
</script>