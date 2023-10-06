<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

$CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');
$CLICSHOPPING_Page = Registry::get('Site')->getPage();
$CLICSHOPPING_Language = Registry::get('Language');
$CLICSHOPPING_Template = Registry::get('TemplateAdmin');

$orders_status_inputs_string = '';
$languages = $CLICSHOPPING_Language->getLanguages();

if (isset($_GET['oID'])) {
  $Qstatus = $CLICSHOPPING_ReturnOrders->db->prepare('select return_status_id,
                                                                 language_id,
                                                                 name
                                                          from :table_return_orders_status
                                                          where language_id = :language_id
                                                          and return_status_id = :return_status_id
                                                        ');

  $Qstatus->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $Qstatus->bindInt(':return_status_id', $_GET['oID']);
  $Qstatus->execute();

  $status = $Qstatus->fetch();

  $return_status_id = $status['return_status_id'];

  $action = 'Update';
} else {
  $action = 'Insert';
  $return_status_id = '';
}
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
            <span
              class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/order_status.gif', $CLICSHOPPING_ReturnOrders->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ReturnOrders->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-end">
  <?php
  echo HTML::form('status_orders_status', $CLICSHOPPING_ReturnOrders->link('OrdersStatus&' . $action . '&oID=' . $return_status_id));
  echo HTML::button($CLICSHOPPING_ReturnOrders->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_ReturnOrders->getDef('button_cancel'), null, $CLICSHOPPING_ReturnOrders->link('OrdersStatus'), 'warning');
  ?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_ReturnOrders->getDef('text_info_heading_edit_orders_status'); ?></strong></div>
  <div class="adminformTitle">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_ReturnOrders->getDef('text_info_edit_intro'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_ReturnOrders->getDef('text_info_edit_intro'); ?></label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_ReturnOrders->getDef('text_info_orders_status_name'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_ReturnOrders->getDef('text_info_orders_status_name'); ?></label>
        </div>
      </div>
    </div>
    <?php
    for ($i = 0, $n = \count($languages); $i < $n; $i++) {
      ?>
      <div class="row">
        <div class="col-md-5">
          <div class="form-group row">
            <label for="code"
                   class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
            <div class="col-md-5">
              <?php
              if ($action == 'Update') {
                echo HTML::inputField('name[' . $languages[$i]['id'] . ']', $Qstatus->value('name'));
              } else {
                echo HTML::inputField('name[' . $languages[$i]['id'] . ']', '', 'required aria-required="true"');
              }
              ?>

            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <?php
    }
    ?>
    <div class="separator"></div>
    <?php
    /*
  if (DEFAULT_return_status_id != $return_status_id) {
    ?>
    <div class="col-md-12" id="default">
      <span class="col-md-3"></span>
      <ul class="list-group-slider list-group-flush">
        <li class="list-group-item-slider">
          <label class="switch">
            <?php echo HTML::checkboxField('default', null, null, 'class="success"'); ?>
            <span class="slider"></span>
          </label>
        </li>
        <span class="text-slider"><?php echo $CLICSHOPPING_ReturnOrders->getDef('text_set_default'); ?></span>
      </ul>
    </div>
    <?php
  }
  */
    ?>
  </div>
  </form>
</div>