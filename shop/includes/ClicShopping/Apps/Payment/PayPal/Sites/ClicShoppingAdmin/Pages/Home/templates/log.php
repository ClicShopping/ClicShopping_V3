<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  require_once(__DIR__ . '/template_top.php');

  $Qlog = $CLICSHOPPING_PayPal->db->prepare('select SQL_CALC_FOUND_ROWS l.id,
                                                                  l.customers_id,
                                                                  l.module,
                                                                  l.action,
                                                                  l.result,
                                                                  l.ip_address,
                                                                  unix_timestamp(l.date_added) as date_added,
                                                                  c.customers_firstname,
                                                                  c.customers_lastname
                                                                  from :table_clicshopping_app_paypal_log l left join :table_customers c on (l.customers_id = c.customers_id)
                                                                  order by l.date_added desc
                                                                  limit :page_set_offset,
                                                                        :page_set_max_results
                                              ');

  $Qlog->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qlog->execute();
?>

  <div class="text-md-right">
    <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_dialog_delete'), null, '#', 'danger', ['params' => 'data-button="delLogs"']); ?>
  </div>
  <div class="separator"></div>
  <table id="ppTableLog" class="table table-hover">
    <thead>
    <tr class="dataTableHeadingRow">
      <th colspan="2"><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_action'); ?></th>
      <th><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_ip'); ?></th>
      <th><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_customer'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_date'); ?></th>
      <th class="action"></th>
    </tr>
    </thead>
    <tbody>

<?php
      if ($Qlog->getPageSetTotalRows() > 0) {
        while ($Qlog->fetch()) {
          $customers_name = null;

          if ($Qlog->valueInt('customers_id') > 0) {
            $customers_name = trim($Qlog->value('customers_firstname') . ' ' . $Qlog->value('customers_lastname'));

            if (empty($customers_name)) {
              $customers_name = '- ? -';
            }
          }
?>

          <tr>
            <td class="text-md-center" style="width: 30px;"><span
                class="label <?php echo ($Qlog->valueInt('result') === 1) ? 'label-success' : 'label-danger'; ?>"><?php echo $Qlog->value('module'); ?></span>
            </td>
            <td><?php echo $Qlog->value('action'); ?></td>
            <td><?php echo long2ip($Qlog->value('ip_address')); ?></td>
            <td><?php echo (!empty($customers_name)) ? HTML::outputProtected($customers_name) : '<i>' . $CLICSHOPPING_PayPal->getDef('guest') . '</i>'; ?></td>
            <td
              class="text-md-center"><?php echo date(CLICSHOPPING::getDef('php_date_time_format'), $Qlog->value('date_added')); ?></td>
            <td class="text-md-center"><a
                href="<?php echo $CLICSHOPPING_PayPal->link('Log&View&page=' . (isset($_GET['page']) ? $_GET['page'] : 1) . '&lID=' . $Qlog->valueInt('id')); ?>"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', CLICSHOPPING::getDef('image_edit')); ?></a>
            </td>
          </tr>

<?php
        }
      } else {
?>

        <tr>
          <td colspan="6"><?php echo $CLICSHOPPING_PayPal->getDef('no_entries'); ?></td>
        </tr>

<?php
      }
?>

    </tbody>
  </table>

  <div>
    <span class="float-md-right"><?php echo $Qlog->getPageSetLinks(CLICSHOPPING::getAllGET(array('page'))); ?></span>
    <?php echo $Qlog->getPageSetLabel($CLICSHOPPING_PayPal->getDef('listing_number_of_log_entries')); ?>
  </div>

  <div id="delLogs-dialog-confirm" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php echo $CLICSHOPPING_PayPal->getDef('dialog_delete_title'); ?></h4>
        </div>

        <div class="modal-body">
          <p><?php echo $CLICSHOPPING_PayPal->getDef('dialog_delete_body'); ?></p>
        </div>

        <div class="modal-footer">
          <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_delete'), null, $CLICSHOPPING_PayPal->link('Log&DeleteAll'), 'danger'); ?>
          <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_cancel'), null, '#', 'warning', ['params' => 'data-dismiss="modal"']); ?>
        </div>
      </div>
    </div>
  </div>

  <script>
      $(function () {
          $('a[data-button="delLogs"]').click(function (e) {
              e.preventDefault();

              $('#delLogs-dialog-confirm').modal('show');
          });
      });
  </script>

<?php
  require_once(__DIR__ . '/template_bottom.php');