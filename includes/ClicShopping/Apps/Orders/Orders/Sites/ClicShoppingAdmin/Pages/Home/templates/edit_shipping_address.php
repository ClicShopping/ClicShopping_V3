<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Orders = Registry::get('Orders');
$CLICSHOPPING_Address = Registry::get('Address');

$customer_id = HTML::sanitize($_GET['customer_id']);
$order_id = HTML::sanitize($_GET['order_id']);

$Qcustomers = $CLICSHOPPING_Orders->db->prepare('select customers_id,
                                                          delivery_name,
                                                          delivery_company,
                                                          delivery_street_address,
                                                          delivery_suburb,
                                                          delivery_city,
                                                          delivery_postcode,
                                                          delivery_state,
                                                          delivery_country
                                                   from :table_orders       
                                                   where orders_id = :order_id
                                                   and customers_id = :customer_id
                                                 ');
$Qcustomers->bindInt(':customer_id', $customer_id);
$Qcustomers->bindInt(':order_id', $order_id);

$Qcustomers->execute();

$delivery_name = $Qcustomers->value('delivery_name');
$customers_company = $Qcustomers->value('delivery_company');
$entry_street_address = $Qcustomers->value('delivery_street_address');
$entry_suburb = $Qcustomers->value('delivery_suburb');
$entry_postcode = $Qcustomers->value('delivery_postcode');
$entry_city = $Qcustomers->value('delivery_city');
$entry_state = $Qcustomers->value('delivery_state');
$entry_country = $Qcustomers->value('delivery_country');

echo HTML::form('update_shipping_address', $CLICSHOPPING_Orders->link('Orders&UpdateShippingAddress'), 'post', 'role="form"');

echo HTML::hiddenField('order_id', $order_id);
echo HTML::hiddenField('customer_id', $customer_id);


?>
<div class="col-md-12">
  <class
  ="row">
  <div class="col-md-12">
    <div class="form-group row">
      <label for="<?php echo $CLICSHOPPING_Orders->getDef('customers_company'); ?>"
             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('customers_company'); ?></label>
      <div class="col-md-7">
        <?php echo HTML::inputField('delivery_company', $customers_company, 'maxlength="32" placeholder="' . $CLICSHOPPING_Orders->getDef('customers_company') . '"', true); ?>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <class
  ="row">
  <div class="col-md-12">
    <div class="form-group row">
      <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_name'); ?>"
             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_name'); ?></label>
      <div class="col-md-7">
        <?php echo HTML::inputField('delivery_name', $delivery_name, 'maxlength="32" required aria-required="true" placeholder="' . $CLICSHOPPING_Orders->getDef('entry_name') . '"', true); ?>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_address'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_address'); ?></label>
        <div class="col-md-7">
          <?php echo HTML::inputField('delivery_street_address', $entry_street_address, 'maxlength="32" required aria-required="true" placeholder="' . $CLICSHOPPING_Orders->getDef('entry_address') . '"', true); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_suburb'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_suburb'); ?></label>
        <div class="col-md-7">
          <?php echo HTML::inputField('entry_suburb', $entry_suburb, 'maxlength="32" placeholder="' . $CLICSHOPPING_Orders->getDef('entry_suburb') . '"', true); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_postcode'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_postcode'); ?></label>
        <div class="col-md-7">
          <?php echo HTML::inputField('entry_postcode', $entry_postcode, 'maxlength="32" required aria-required="true" placeholder="' . $CLICSHOPPING_Orders->getDef('entry_postcode') . '"', true); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_city'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_city'); ?></label>
        <div class="col-md-7">
          <?php echo HTML::inputField('entry_city', $entry_city, 'maxlength="32" required aria-required="true" placeholder="' . $CLICSHOPPING_Orders->getDef('entry_city') . '"', true); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_country_id'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_country_id'); ?></label>
        <div class="col-md-7">
          <?php
          $Qcountry = $CLICSHOPPING_Orders->db->get('countries', 'countries_id', ['countries_name' => $entry_country]);

          echo HTML::selectMenuCountryList('country', $Qcountry->valueInt('countries_id'), 'onchange="update_zone(this.form);"');
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="<?php echo $CLICSHOPPING_Orders->getDef('entry_zone_id'); ?>"
               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Orders->getDef('entry_zone_id'); ?></label>
        <div class="col-md-7">
          <?php
          $Qzones = $CLICSHOPPING_Orders->db->get('zones', ['zone_id'], ['zone_name' => $entry_state]);

          echo HTML::selectField('state', $CLICSHOPPING_Address->getPrepareCountryZonesPullDown($Qcountry->valueInt('countries_id')), $Qzones->valueInt('zone_id'));
          include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/state_dropdown.php');
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="separator"></div>
<div
  class="col-md-12 text-end"><?php echo HTML::button($CLICSHOPPING_Orders->getDef('button_update'), null, null, 'success'); ?></div>
<div class="separator"></div>
</form>