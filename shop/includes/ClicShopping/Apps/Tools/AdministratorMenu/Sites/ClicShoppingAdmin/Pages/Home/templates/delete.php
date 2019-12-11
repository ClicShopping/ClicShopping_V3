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
  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  $cPath = HTML::sanitize($_GET['cPath']);

  $Qcategories = $CLICSHOPPING_AdministratorMenu->db->prepare('select a.id,
                                                                      a.link,
                                                                      a.parent_id,
                                                                      a.access,
                                                                      a.sort_order,
                                                                      a.b2b_menu,
                                                                      a.app_code,
                                                                      amd.label
                                                                from :table_administrator_menu a,
                                                                      :table_administrator_menu_description amd
                                                                where a.id = amd.id
                                                                and amd.language_id = :language_id
                                                                and a.id = :id
                                                                order by a.parent_id,
                                                                         a.sort_order
                                                                ');
  $Qcategories->bindInt(':id', $_GET['cID']);
  $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $Qcategories->execute();

  $category_childs = ['childs_count' => AdministratorMenu::getChildsInMenuCount($Qcategories->valueInt('id'))];

  $cInfo_array = array_merge($Qcategories->toArray(), $category_childs);
  $cInfo = new ObjectInfo($cInfo_array);
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/menu.png', $CLICSHOPPING_AdministratorMenu->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_AdministratorMenu->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_AdministratorMenu->getDef('text_info_heading_delete_category'); ?></strong></div>
  <?php echo HTML::form('categories', $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu&DeleteCategoryConfirm&cPath=' . $cPath . '&id=' . $cInfo->id)); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_AdministratorMenu->getDef('text_delete_category_intro'); ?>
        <br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12">
        <span class="col-md-3"><?php echo $cInfo->label; ?></span>
      </div>
      <?php
        if ($cInfo->childs_count > 0) {
          ?>
          <div class="separator"></div>
          <div class="col-md-12">
            <span
              class="col-md-12"><?php echo $CLICSHOPPING_AdministratorMenu->getDef('text_delete_warning_childs', ['delete_child' => $cInfo->childs_count]); ?></span>
          </div>
          <?php
        }
      ?>
      <div class="separator"></div>
      <div class="col-md-12 text-md-center">
        <span><br/><?php echo HTML::button($CLICSHOPPING_AdministratorMenu->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_AdministratorMenu->getDef('button_cancel'), null, $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu&cPath=' . $cPath . '&cID=' . $cInfo->id), 'warning', null, 'sm'); ?></span>
      </div>
    </div>
  </div>
  </form>
</div>