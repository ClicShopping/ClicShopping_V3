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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Item = Registry::get('Item');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('Item')) {
    echo $CLICSHOPPING_MessageStack->get('Item');
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/modules_modules_checkout_shipping.gif', $CLICSHOPPING_Item->getDef('Item'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Item->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Item->getDef('text_item'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>

      <div class="col-md-12">
        <div class="form-group">
          <div class="col-md-12">
            <?php echo $CLICSHOPPING_Item->getDef('text_intro'); ?>
          </div>
        </div>
      </div>

      <div class="col-md-12 text-md-center">
        <div class="form-group">
          <div class="col-md-12">
            <?php
              echo HTML::form('configure', CLICSHOPPING::link(null, 'A&Shipping\Item&Configure'));
              echo HTML::button($CLICSHOPPING_Item->getDef('button_configure'), null, null, 'primary');
              echo '</form>';
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
  </div>
</div>
