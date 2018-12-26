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

  $CLICSHOPPING_TaxClass = Registry::get('TaxClass');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qclasse = $CLICSHOPPING_Db->prepare('select *
                                   from :table_tax_class
                                   where tax_class_id = :tax_class_id
                                  ');
  $Qclasse->bindInt(':tax_class_id', $_GET['tID']);
  $Qclasse->execute();
  $tcInfo = new ObjectInfo($Qclasse->toArray());
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/tax_classes.gif', $CLICSHOPPING_TaxClass->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-7 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxClass->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
<?php
  echo HTML::form('status_tax_class', $CLICSHOPPING_TaxClass->link('TaxClass&Update&page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id));
  echo HTML::button($CLICSHOPPING_TaxClass->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_TaxClass->getDef('button_cancel'), null, $CLICSHOPPING_TaxClass->link('TaxClass'), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_TaxClass->getDef('table_heading_tax_classes'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxClass->getDef('text_info_edit_intro'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxClass->getDef('text_info_edit_intro'); ?></label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxClass->getDef('text_info_class_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxClass->getDef('text_info_class_title'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('tax_class_title', $tcInfo->tax_class_title); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_TaxClass->getDef('text_info_class_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_TaxClass->getDef('text_info_class_description'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('tax_class_description', $tcInfo->tax_class_description); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>