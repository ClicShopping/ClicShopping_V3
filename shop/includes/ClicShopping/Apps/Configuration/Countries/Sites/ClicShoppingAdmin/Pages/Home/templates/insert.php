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

  $CLICSHOPPING_Address = Registry::get('Address');
  $CLICSHOPPING_Countries = Registry::get('Countries');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

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
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Countries->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Countries->getDef('button_cancel'), null, $CLICSHOPPING_Countries->link('Countries'), 'warning') . ' ';
  echo HTML::form('status_countries', $CLICSHOPPING_Countries->link('Countries&Insert&page=' . $_GET['page']));
  echo HTML::button($CLICSHOPPING_Countries->getDef('button_insert'), null, null, 'success')
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Countries->getDef('text_info_heading_new_county'); ?></strong></div>
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
            <?php echo HTML::inputField('countries_name', null, 'required aria-required="true"'); ?>
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
            <?php echo HTML::inputField('countries_iso_code_2', null, 'maxlength="2" size="2"'); ?>
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
            <?php echo HTML::inputField('countries_iso_code_3', null, 'maxlength="3" size="3"'); ?>
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
                    <?php echo '<div class="col-md-12 radio">' . '<label for="addressLabel' . $value['address_format_id'] . '">' . HTML::radioField('address_format_id', $value['address_format_id'], null, 'id="addressLabel' . $value['address_format_id'] . '"') . ' ' . $CLICSHOPPING_Countries->getDef('text_format') . ' ' . $value['address_format_id'] . '</div>'; ?></h4>
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
      ?>
    </div>
    <div class="separator"></div>
    <div class="alert alert-info" role="alert">
      <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Countries->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Countries->getDef('title_help_general') ?></div>
      <div class="separator"></div>
      <div><?php echo $CLICSHOPPING_Countries->getDef('help_general'); ?></div>
    </div>
  </div>
  </form>
</div>