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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  require_once(__DIR__ . '/template_top.php');
?>

  <div class="text-md-right">
    <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_back'), null, $CLICSHOPPING_PayPal->link('Log&page=' . (int)$_GET['page']), 'primary'); ?>
  </div>
  <div class="separator"></div>
  <table class="table table-hover">
    <thead>
    <tr class="dataTableHeadingRow">
      <th colspan="2"><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_entries_request'); ?></th>
    </tr>
    </thead>
    <tbody>

<?php
      foreach ($CLICSHOPPING_Page->data['log_request'] as $key => $value) {
?>

        <tr>
          <td width="25%"><?php echo HTML::outputProtected($key); ?></td>
          <td><?php echo HTML::outputProtected($value); ?></td>
        </tr>

<?php
      }
?>

    </tbody>
  </table>
  <div class="separator"></div>
  <table class="table table-hover">
    <thead>
    <tr class="dataTableHeadingRow">
      <th colspan="2"><?php echo $CLICSHOPPING_PayPal->getDef('table_heading_entries_response'); ?></th>
    </tr>
    </thead>
    <tbody>

<?php
      foreach ($CLICSHOPPING_Page->data['log_response'] as $key => $value) {
?>

        <tr>
          <td width="25%"><?php echo HTML::outputProtected($key); ?></td>
          <td><?php echo HTML::outputProtected($value); ?></td>
        </tr>

<?php
      }
?>

    </tbody>
  </table>

<?php
  require_once(__DIR__ . '/template_bottom.php');
