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
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_TaxGeoZones = Registry::get('TaxGeoZones');
  $CLICSHOPPING_Address = Registry::get('Address');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/geo_zones.gif', $CLICSHOPPING_TaxGeoZones->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxGeoZones->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
<?php
   echo HTML::form('zones', $CLICSHOPPING_TaxGeoZones->link('TaxGeoZones&InsertGeoZone&List&zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&spage=' . $_GET['spage'] . '&' . (isset($_GET['sID']) ? 'sID=' . $_GET['sID'] . '&' : '')));
   echo HTML::button($CLICSHOPPING_TaxGeoZones->getDef('button_insert'), null, null, 'success') . ' ';
   echo HTML::button($CLICSHOPPING_TaxGeoZones->getDef('button_cancel'), null,  $CLICSHOPPING_TaxGeoZones->link('ListGeo&zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . 'spage=' . $_GET['spage'] . '&' . (isset($_GET['sID']) ? 'sID=' . $_GET['sID'] : '')), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_heading_new_sub_zone'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_new_sub_zone_intro'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_new_sub_zone_intro'); ?></label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_country'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_country'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectMenuCountryList('country', null, 'onchange="update_zone(this.form);"'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_country_zone'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxGeoZones->getDef('text_info_country_zone'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectMenu('state', $CLICSHOPPING_Address->getPrepareCountryZonesPullDown()); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>
<?php
  include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/state_dropdown.php');
?>