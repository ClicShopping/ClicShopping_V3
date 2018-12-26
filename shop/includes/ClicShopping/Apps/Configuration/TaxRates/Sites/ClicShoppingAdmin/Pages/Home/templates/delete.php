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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_TaxRates = Registry::get('TaxRates');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qrates = $CLICSHOPPING_TaxRates->db->prepare('select *
                                           from :table_tax_rates
                                           where tax_rates_id = :tax_rates_id
                                          ');
  $Qrates->bindInt(':tax_rates_id', $_GET['tID']);
  $Qrates->execute();

  $trInfo = new ObjectInfo($Qrates->toArray());
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/tax_rates.gif', $CLICSHOPPING_TaxRates->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxRates->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_TaxRates->getDef('text_info_heading_delete_tax_rate');; ?></strong></div>
  <?php echo HTML::form('rates', $CLICSHOPPING_TaxRates->link('TaxRates&DeleteConfirm&page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id)); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_TaxRates->getDef('text_info_delete_info'); ?><br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12"><?php echo '<strong>' . $trInfo->tax_class_title . '</strong>'; ?><br/><br/></div>
      <div class="col-md-12 text-md-center">
        <span><br /><?php echo HTML::button($CLICSHOPPING_TaxRates->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_TaxRates->getDef('button_cancel'), null, $CLICSHOPPING_TaxRates->link('TaxRates&page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id), 'warning', null, 'sm'); ?></span>
      </div>
    </div>
  </div>

  </form>
</div>