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

  $CLICSHOPPING_Currency = Registry::get('Currency');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qcurrency = $CLICSHOPPING_Currency->db->prepare('select *
                                                  from :table_currencies
                                                  where currencies_id = :currencies_id
                                                ');
  $Qcurrency->bindInt(':currencies_id', $_GET['cID']);
  $Qcurrency->execute();

  $cInfo = new ObjectInfo($Qcurrency->toArray());
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/currencies.gif', $CLICSHOPPING_Currency->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Currency->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
<?php
  echo HTML::form('Currency', $CLICSHOPPING_Currency->link('Currency&Update&page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id));
  echo HTML::button($CLICSHOPPING_Currency->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_Currency->getDef('button_cancel'), null,  $CLICSHOPPING_Currency->link('Currency&page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Currency->getDef('text_info_heading_edit_currency'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_edit_intro'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_edit_intro'); ?></label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_title'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('title', $cInfo->title); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_code'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_code'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('code', $cInfo->code); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_symbol_left'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_symbol_left'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('symbol_left', $cInfo->symbol_left); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_symbol_right'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_symbol_right'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('symbol_right', $cInfo->symbol_right); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_decimal_places'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_decimal_places'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('decimal_places', $cInfo->decimal_places); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_decimal_point'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_decimal_point'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('decimal_point', $cInfo->decimal_point); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_thousands_point'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_thousands_point'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('thousands_point', $cInfo->thousands_point); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_value'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Currency->getDef('text_info_currency_value'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('value', $cInfo->value); ?>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
<?php
  if (DEFAULT_CURRENCY != $cInfo->code) {
?>
    <div class="separator"></div>
    <div class="col-md-12 text-md-center">
      <span class="col-md-3"></span>
      <span class="col-md-3"><br /><?php echo HTML::checkboxField('default') . ' ' . $CLICSHOPPING_Currency->getDef('text_info_set_as_default', ['text_set_default' => $CLICSHOPPING_Currency->getDef('text_set_default')]); ?></span>
    </div>
<?php
  }
?>
    </div>
  </div>
  </form>
</div>