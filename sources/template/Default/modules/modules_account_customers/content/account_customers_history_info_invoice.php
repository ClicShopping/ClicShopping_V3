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
  use ClicShopping\OM\DateTime;
  use ClicShopping\Sites\Shop\Tax;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="page-title AccountCustomersHistoryInfo"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_heading_billing_information'); ?></h3></div>
  <div class="card">
    <div class="card-header"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_order_number', ['order_id' => $_GET['order_id']]) . ' <span class="badge float-md-right">' . $CLICSHOPPING_Order->info['orders_status'] . '</span>'; ?></strong></div>
    <div class="card-block">
      <div class="separator"></div>
      <table border="0" width="100%" cellspacing="0" cellpadding="2" class="table-hover order_confirmation">
        <thead>
<?php
  if (count($CLICSHOPPING_Order->info['tax_groups']) > 1) {
?>
          <tr>
            <th colspan="2"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_products'); ?></strong></th>
            <th class="text-md-right"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_tax'); ?></strong></th>
            <th class="text-md-right"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_order_total'); ?></strong></th>
          </tr>
<?php
  } else {
?>
          <tr>
            <th colspan="2"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_products'); ?></strong></th>
            <th class="text-md-right"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_order_total'); ?></strong></th>
          </tr>
        </thead>
       <tbody>
<?php
  }

  for ($i=0, $n=count($CLICSHOPPING_Order->products); $i<$n; $i++) {
    echo '       <tr>' . "\n" .
      '            <td class="text-md-right" valign="top" width="30">' . $CLICSHOPPING_Order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
      '            <td valign="top">' . $CLICSHOPPING_Order->products[$i]['name'];

    if ( (isset($CLICSHOPPING_Order->products[$i]['attributes'])) && (count($CLICSHOPPING_Order->products[$i]['attributes']) > 0)) {
      for ($j=0, $n2=count($CLICSHOPPING_Order->products[$i]['attributes']); $j<$n2; $j++) {

        if (!empty($CLICSHOPPING_Order->products[$j]['attributes'][$j]['reference'])) {
          $reference = $CLICSHOPPING_Order->products[$j]['attributes'][$j]['reference'] . ' / ';
        } else {
          $reference =  '';
        }

        if (!empty($CLICSHOPPING_Order->products[$i]['attributes'][$j]['price'])) {
          $price = '( ' .$CLICSHOPPING_Order->products[$i]['attributes'][$j]['prefix'] . ' ' . $CLICSHOPPING_Order->products[$i]['attributes'][$j]['price'] . ' )';
        } else {
          $price = '';
        }

        echo '<br /><nobr><small>&nbsp;<i> - <strong>' . $reference . '</strong>' . $CLICSHOPPING_Order->products[$i]['attributes'][$j]['option'] . ' : ' . $CLICSHOPPING_Order->products[$i]['attributes'][$j]['value'] . $price .'</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (count($CLICSHOPPING_Order->info['tax_groups']) > 1) {
      echo '            <td class="text-md-right" valign="top">' . Tax::displayTaxRateValue($CLICSHOPPING_Order->products[$i]['tax']) . '</td>' . "\n";
    }

    echo '            <td class="text-md-right" valign="top">' . $CLICSHOPPING_Currencies->format(Tax::addTax($CLICSHOPPING_Order->products[$i]['final_price'], $CLICSHOPPING_Order->products[$i]['tax']) * $CLICSHOPPING_Order->products[$i]['qty'], true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']) . '</td>' . "\n" .
      '          </tr>' . "\n";
  }
?>
          </tbody>
        </table>
        <div style="height:10px;"></div>
        <div class="hr"></div>
<?php
  // ----------------------
  // --- Total order   -----
  // ----------------------
?>
        <table width="100%" class="float-md-right">
<?php
  for ($i=0, $n=count($CLICSHOPPING_Order->totals); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
      '                   <td class="text-md-right"  width="80%">' . $CLICSHOPPING_Order->totals[$i]['title'] . '&nbsp;</td>' . "\n" .
      '                   <td class="text-md-right" width=20%">' . $CLICSHOPPING_Order->totals[$i]['text'] . '</td>' . "\n" .
      '                 </tr>' . "\n";
  }
?>
      </table>
    </div>
    <div class="clearfix"></div>
    <div class="card-footer">
      <?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_order_date') . ' ' .  DateTime::toLong($CLICSHOPPING_Order->info['date_purchased']); ?>
    </div>
  </div>
  <div class="separator"></div>
  <div class="hr"></div>
</div>
