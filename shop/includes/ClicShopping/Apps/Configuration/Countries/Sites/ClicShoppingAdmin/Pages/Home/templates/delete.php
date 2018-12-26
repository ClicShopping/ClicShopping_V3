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

  $CLICSHOPPING_Countries = Registry::get('Countries');
  $CLICSHOPPING_Address = Registry::get('Address');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qcountries = $CLICSHOPPING_Db->prepare('select *
                                   from :table_countries
                                   where countries_id = :countries_id
                                  ');
  $Qcountries->bindInt(':countries_id', $_GET['cID']);
  $Qcountries->execute();

  $cInfo = new ObjectInfo($Qcountries->toArray());
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/countries.gif', $CLICSHOPPING_Countries->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Countries->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Countries->getDef('text_info_delete_country'); ?></strong></div>
  <?php echo HTML::form('countries', $CLICSHOPPING_Countries->link('Countries&DeleteConfirm&page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id)); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_Countries->getDef('text_info_delete_info'); ?><br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12"><?php echo '<strong>' . $cInfo->countries_name . '</strong>'; ?><br/><br/></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_Countries->getDef('text_info_address_format') . '<br />' . $CLICSHOPPING_Address->getAddressFormatRadio($cInfo->address_format_id); ?></div>
      <div class="col-md-12 text-md-center">
        <span><br /><?php echo HTML::button($CLICSHOPPING_Countries->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Countries->getDef('button_cancel'), null, $CLICSHOPPING_Countries->link('Countries&page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id), 'warning', null, 'sm'); ?></span>
      </div>
    </div>
  </div>
  </form>
</div>