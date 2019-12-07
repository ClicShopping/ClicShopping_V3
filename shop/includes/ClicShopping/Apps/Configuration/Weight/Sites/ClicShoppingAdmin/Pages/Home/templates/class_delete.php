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

  $CLICSHOPPING_Weight = Registry::get('Weight');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qweight = $CLICSHOPPING_Weight->db->prepare('select weight_class_from_id,
                                                       weight_class_to_id,
                                                       weight_class_rule
                                               from :table_weight_classes_rules
                                               where weight_class_from_id = :weight_class_from_id
                                               and weight_class_to_id = :weight_class_to_id
                                              ');
  $Qweight->bindInt(':weight_class_from_id', HTML::sanitize($_GET['wID']));
  $Qweight->bindInt(':weight_class_to_id', HTML::sanitize($_GET['tID']));
  $Qweight->execute();

  $wInfo = new ObjectInfo($Qweight->toArray());

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/weight.png', $CLICSHOPPING_Weight->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Weight->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Weight->getDef('text_info_heading_delete_weight');; ?></strong></div>
  <?php echo HTML::form('delete', $CLICSHOPPING_Weight->link('Weight&ClassDeleteConfirm&page=' . $page . '&wID=' . $wInfo->weight_class_from_id . '&tID=' . $wInfo->weight_class_to_id)); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_Weight->getDef('text_info_delete_info'); ?><br/><br/></div>
      <div class="separator"></div>
      <div
        class="col-md-12"><?php echo '<strong>' . $CLICSHOPPING_Weight->getDef('text_weight_rules') . ' ' . $wInfo->weight_class_rule . '</strong>'; ?>
        <br/><br/></div>
      <div class="col-md-12 text-md-center">
        <span><br/><?php echo HTML::button($CLICSHOPPING_Weight->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Weight->getDef('button_cancel'), null, $CLICSHOPPING_Weight->link('Weight&page=' . HTML::sanitize($_GET['page']) . '&tID=' . $wInfo->weight_id), 'warning', null, 'sm'); ?></span>
      </div>
    </div>
  </div>

  </form>
</div>