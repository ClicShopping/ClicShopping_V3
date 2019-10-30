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

  $address_formats_array = $CLICSHOPPING_Countries->db->get('address_format', 'address_format_id, address_format');
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/countries.gif', $CLICSHOPPING_Countries->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Countries->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
<?php
  echo HTML::form('status_countries', $CLICSHOPPING_Countries->link('Countries&Update&page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id));
  echo HTML::button($CLICSHOPPING_Countries->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_Countries->getDef('button_cancel'), null, $CLICSHOPPING_Countries->link('Countries'), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Countries->getDef('text_info_heading_edit_country'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Countries->getDef('text_info_edit_intro'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Countries->getDef('text_info_edit_intro'); ?></label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Countries->getDef('text_info_country_name'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Countries->getDef('text_info_country_name'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('countries_name', $cInfo->countries_name); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Countries->getDef('text_info_country_code_2'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Countries->getDef('text_info_country_code_2'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('countries_iso_code_2', $cInfo->countries_iso_code_2); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Countries->getDef('text_info_country_code_3'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Countries->getDef('text_info_country_code_3'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('countries_iso_code_3', $cInfo->countries_iso_code_3); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <?php echo $CLICSHOPPING_Countries->getDef('text_info_address_format'); ?>
      </div>
    </div>
    <div class="separator"></div>
    <div class="row">
      <?php
        foreach ($address_formats_array as $value) {
          ?>
          <div class="col-md-3">
            <div class="card-deck">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">
                    <div class="col-md-12 custom-control custom-radio">
                      <?php echo HTML::radioField('address_format_id', $value['address_format_id'], ($value['address_format_id'] == $cInfo->address_format_id), 'class="custom-control-input" id="addressLabel' . $value['address_format_id'] . '" name="addressLabel' . $value['address_format_id'] . '"'); ?>
                      <label class="custom-control-label" for="addressLabel<?php echo $value['address_format_id']; ?>"><?php echo  $CLICSHOPPING_Countries->getDef('text_format') . ' ' . $value['address_format_id']; ?></label>
                    </div>
                  </h4>
                  <p class="card-text">
                    <strong><?php echo '<div class="col-md-12">&nbsp;' . $CLICSHOPPING_Address->getAddressFormatRadio($value['address_format_id']) . '</label></div>'; ?></strong>
                  </p>
                </div>
              </div>
            </div>
            <div class="separator"></div>
          </div>
          <?php
        }
      ?>  </div>
    <div class="separator"></div>
    <div class="alert alert-info" role="alert">
      <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Countries->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Countries->getDef('title_help_general') ?></div>
      <div class="separator"></div>
      <div><?php echo $CLICSHOPPING_Countries->getDef('help_general'); ?></div>
    </div>
  </div>
  </form>
</div>