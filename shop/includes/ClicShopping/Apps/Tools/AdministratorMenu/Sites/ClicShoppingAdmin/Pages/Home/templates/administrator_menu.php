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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();



  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

  $Qaccess = $CLICSHOPPING_Db->prepare('select access,
                                        id
                                  from :table_administrators
                                  where id = :id
                                  and access = 1
                                  ');
  $Qaccess->bindInt(':id', $_SESSION['admin']['id'] );
  $Qaccess->execute();


  if (is_null($Qaccess->valueInt('access'))) {
    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_AdministratorMenu->getDef('error_no_access'), 'error');
    CLICSHOPPING::redirect();
  }

?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/menu.png', $CLICSHOPPING_AdministratorMenu->getDef($CLICSHOPPING_AdministratorMenu->getDef('heading_title')), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_AdministratorMenu->getDef('heading_title'); ?></span>
          <span class="col-md-3 text-md-right">
           <div class="form-group">
             <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu'), 'post', 'class="form-inline"', ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_AdministratorMenu->getDef('heading_title_search') . '"');
  echo '&nbsp;&nbsp;&nbsp;';
?>
               </form>
           </div>
         </div>
        </span>
          <span class="col-md-3">
           <div class="form-group">
             <div class="controls">
<?php
  if (isset($_POST['cPath'])) $current_category_id = $_POST['cPath'];

  echo HTML::form('goto', $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu'), 'post', 'class="form-inline"', ['session_id' => true]);
  echo HTML::selectMenu('cPath', AdministratorMenu::getLabelTree(), $current_category_id, 'onchange="this.form.submit();"');
  echo '</form>';
?>
               </form>
             </div>
           </div>
         </span>
          <span class="col-md-3 text-md-right">
<?php
  $cPath_back = '';

  $cPath_array = $CLICSHOPPING_CategoriesAdmin->getPathArray();

  if (count($cPath_array) > 0) {
    for ($i=0, $n=count($cPath_array)-1; $i<$n; $i++) {
      if (empty($cPath_back)) {
        $cPath_back .= $cPath_array[$i];
      } else {
        $cPath_back .= '_' . $cPath_array[$i];
      }
    }
  }

  $cPath_back = (!is_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';

  if (isset($_POST['search']) || isset($_POST['cPath'])) {
    echo HTML::button($CLICSHOPPING_AdministratorMenu->getDef('button_reset'), null, $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu&' . $cPath_back . 'cID=' . $current_category_id), 'warning') . '&nbsp;';
  }


  if (!isset($_GET['search'])) {
    echo HTML::button($CLICSHOPPING_AdministratorMenu->getDef('button_new_category'), null, $CLICSHOPPING_AdministratorMenu->link('Edit&cPath=' . $cPath), 'primary') . '&nbsp;';
  }
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th colspan="3">&nbsp;</th>
          <th><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_id'); ?></th>
          <th><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_categories_products'); ?></th>
          <th><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_rights_access'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_app'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_sort_order'); ?>&nbsp;</th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_AdministratorMenu->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        </tbody>
<?php
  $categories_count = 0;
  $rows = 0;

  if (isset($_POST['search'])) {
    $search = HTML::sanitize($_POST['search']);

    $Qcategories = $CLICSHOPPING_AdministratorMenu->db->prepare('select a.id,
                                                                a.link,
                                                                a.parent_id,
                                                                a.access,
                                                                a.sort_order,
                                                                a.b2b_menu,
                                                                amd.label
                                                          from :table_administrator_menu a,
                                                               :table_administrator_menu_description amd
                                                          where a.id = amd.id
                                                          and amd.language_id = :language_id
                                                          and amd.label like :search
                                                          and a.status = 1
                                                          order by a.parent_id,
                                                                   a.sort_order
                                                          ');

    $Qcategories->bindValue(':search', '%' . $search . '%');
    $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
    $Qcategories->execute();

  } else {

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
                                                          and a.parent_id = :parent_id
                                                          and amd.language_id = :language_id
                                                          and a.status = 1
                                                          order by a.parent_id,
                                                                   a.sort_order
                                                          ');

    $Qcategories->bindInt(':parent_id', (int)$current_category_id );
    $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
    $Qcategories->execute();
  }

  while ($Qcategories->fetch() ) {
    $categories_count++;
    $rows++;

// Get parent_id for subcategories if search
    if (isset($_GET['search'])) $cPath = $Qcategories->valueInt('parent_id');

    if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qcategories->valueInt('id')))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
      $category_childs = ['childs_count' => AdministratorMenu::getChildsInMenuCount($Qcategories->valueInt('id'))];

      $cInfo_array = array_merge($Qcategories->toArray(), $category_childs);
      $cInfo = new ObjectInfo($cInfo_array);
    }
?>
            <td><?php echo '<a href="' . $CLICSHOPPING_AdministratorMenu->link('AdministratorMenu&' . AdministratorMenu::getPath($Qcategories->valueInt('id'))) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/folder.gif', $CLICSHOPPING_AdministratorMenu->getDef('image_folder')); ?></td>
            <td colspan="2">&nbsp</td>
            <td><?php echo '<strong>' . $Qcategories->value('id') . '</strong>'; ?></td>
            <td><?php echo '<strong>' . $Qcategories->value('label') . '</strong>'; ?></td>

<?php
    if ($Qcategories->valueInt('access') == 0) {
      echo '<td>' . $CLICSHOPPING_AdministratorMenu->getDef('text_all_right')  . '</td>';
    } else if ($Qcategories->valueInt('access') == 1) {
      echo '<td class="text-info">' . $CLICSHOPPING_AdministratorMenu->getDef('text_all_rights_admin')  . '</td>';
    } elseif ($Qcategories->valueInt('access') == 2) {
      echo '<td class="text-warning">' . $CLICSHOPPING_AdministratorMenu->getDef('text_rights_employee')  . '</td>';
    } elseif ($Qcategories->valueInt('access') == 3) {
      echo '<td class="text-danger">' . $CLICSHOPPING_AdministratorMenu->getDef('text_rights_visitor') . '</td>';
    }
?>
            <td><?php echo $Qcategories->value('app_code'); ?></td>
            <td class="text-md-center"><?php echo $Qcategories->valueInt('sort_order'); ?></td>
            <td class="text-md-right">
<?php
    echo '<a href="' . $CLICSHOPPING_AdministratorMenu->link('Edit&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_AdministratorMenu->getDef('image_edit')) . '</a>' ;
    echo '&nbsp;';
    echo '<a href="' . $CLICSHOPPING_AdministratorMenu->link('Move&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/move.gif', $CLICSHOPPING_AdministratorMenu->getDef('image_move')) . '</a>' ;
    echo '&nbsp;';
    echo '<a href="' . $CLICSHOPPING_AdministratorMenu->link('Delete&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_AdministratorMenu->getDef('image_delete')) . '</a>' ;
    echo '&nbsp;';
?>
            </td>
            </tr>
<?php
  }
?>
        </tbody>
      </table></td>
    </table>
  </form>
  <div><?php echo $CLICSHOPPING_AdministratorMenu->getDef('text_categories') . '&nbsp;' . $categories_count; ?></div>
</div>