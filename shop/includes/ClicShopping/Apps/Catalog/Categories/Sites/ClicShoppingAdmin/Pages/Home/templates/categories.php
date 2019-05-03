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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Categories = Registry::get('Categories');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

  $current_category_id = null;
?>
    <div class="contentBody">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-block headerCard">
            <div class="row">
              <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/categorie.gif', $CLICSHOPPING_Categories->getDef('heading_title'), '40', '40'); ?></span>
              <span class="col-md-1 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Categories->getDef('heading_title'); ?></span>
              <span class="col-md-3">
               <div class="form-group">
                 <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_Categories->link('Categories'), 'post', 'class="form-inline"', ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Categories->getDef('heading_title') . '"');
?>
                  </form>
                 </div>
               </div>
              </span>
              <span class="col-md-3 text-md-center">
               <div class="form-group">
                 <div class="controls">
<?php
  if (isset($_POST['cPath'])) $current_category_id = HTML::sanitize($_POST['cPath']);
  if (isset($_GET['cPath'])) $current_category_id = HTML::sanitize($_GET['cPath']);

  echo HTML::form('goto', $CLICSHOPPING_Categories->link('Categories'), 'post', 'class="form-inline"', ['session_id' => true]);
  echo HTML::selectMenu('cPath', $CLICSHOPPING_CategoriesAdmin->getCategoryTree(), $current_category_id, 'onchange="this.form.submit();"');
  echo '</form>';
?>
                  </form>
                 </div>
               </div>
              </span>
              <span class="col-md-4 text-md-right">
<?php
  $cPath_back = '';

  $cPath_array = $CLICSHOPPING_CategoriesAdmin->getPathArray();

  if (isset($cPath_array) && count($cPath_array) > 0) {
    for ($i=0, $n=count($cPath_array)-1; $i<$n; $i++) {
      if (empty($cPath_back)) {
        $cPath_back .= $cPath_array[$i];
      } else {
        $cPath_back .= '_' . $cPath_array[$i];
      }
    }
  }

  $cPath_back = (!is_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';

  if (isset($_GET['search']) || isset($_POST['cPath'])) {
    echo HTML::button($CLICSHOPPING_Categories->getDef('button_back'), null, $CLICSHOPPING_Categories->link('Categories&' . $cPath_back . 'cID=' . $current_category_id), 'primary') . '&nbsp;';
  }

  if (!isset($_GET['search'])) {
    echo HTML::button($CLICSHOPPING_Categories->getDef('button_new_category'), null, $CLICSHOPPING_Categories->link('Insert&cPath=' . $current_category_id), 'info') . '&nbsp;';
    echo HTML::button($CLICSHOPPING_Categories->getDef('button_products'), null, CLICSHOPPING::link(null, 'A&Catalog\Products&Edit&cPath=' . $current_category_id), 'success');
  }
?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="card-deck">
            <?php echo $CLICSHOPPING_Hooks->output('Stats', 'StatsCategories', null, 'display'); ?>
          </div>
        </div>
      </div>
      <div class="separator"></div>
<?php
  if (!isset($_POST['cPath'])) {
?>
      <div class="alert alert-info" role="alert"><?php echo $CLICSHOPPING_Categories->getDef('text_alert_info_categories'); ?></div>
<?php
  }
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <td>
          <table class="table table-sm table-hover table-striped">
            <thead>
            <tr class="dataTableHeadingRow">
              <td width="1" class="text-md-center"></td>
              <td colspan="3">&nbsp;</td>
              <td><?php echo $CLICSHOPPING_Categories->getDef('table_heading_categories_products'); ?></td>
              <td class="text-md-center"><?php echo $CLICSHOPPING_Categories->getDef('table_heading_status'); ?></td>
              <td class="text-md-center"><?php echo $CLICSHOPPING_Categories->getDef('table_heading_last_modified'); ?>&nbsp;</td>
              <td></td>
              <td class="text-md-center"><?php echo $CLICSHOPPING_Categories->getDef('table_heading_sort_order'); ?>&nbsp;</td>
              <td class="text-md-right"><?php echo $CLICSHOPPING_Categories->getDef('table_heading_action'); ?>&nbsp;</td>
            </tr>
          </thead>
          <tbody>
<?php
  $categories_count = 0;

  $Qcategories = $CLICSHOPPING_CategoriesAdmin->getSearch($_POST['search']);

  $listingTotalRow = $Qcategories->getPageSetTotalRows();

  if ($listingTotalRow > 0) {
    while ($Qcategories->fetch() ) {
      $categories_count++;

// Get categories_id for product if search
      if (isset($_POST['search'])) {
        $cPath = $Qcategories->valueInt('categories_id');
      } else {
        if (isset($_POST['cPath'])) {
          $cPath = HTML::sanitize($_POST['cPath']);
        } else {
          $cPath = HTML::sanitize($_GET['cPath']);
        }
      }

       if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qcategories->valueInt('categories_id')))) && !isset($cInfo)) {
        $category_childs = array('childs_count' => $CLICSHOPPING_CategoriesAdmin->getChildsInCategoryCount($Qcategories->valueInt('categories_id')));
        $category_products = array('products_count' => $CLICSHOPPING_CategoriesAdmin->getCatalogInCategoryCount($Qcategories->valueInt('categories_id')));

        $cInfo_array = array_merge($Qcategories->toArray(), $category_childs, $category_products);
        $cInfo = new ObjectInfo($cInfo_array);
      }
?>
                <td class="text-md-center">&nbsp;</td>
                <td><?php echo '<a href="' . $CLICSHOPPING_Categories->link('Categories&' . $CLICSHOPPING_CategoriesAdmin->getCategoriesPath($Qcategories->valueInt('categories_id'))) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/folder.gif', $CLICSHOPPING_Categories->getDef('icon_folder')); ?></td>
                <td colspan="2"></td>
                <td><?php echo '<strong>' . $Qcategories->value('categories_name') . '</strong>'; ?></td>
                <td class="text-md-center">
<?php
        if ($Qcategories->valueInt('status') == 1) {
          echo HTML::link($CLICSHOPPING_Categories->link('Categories&SetFlag&flag=0&cID=' . $Qcategories->valueInt('categories_id') . '&cPath=' . $cPath),'<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
        } else {
          echo HTML::link($CLICSHOPPING_Categories->link('Categories&SetFlag&flag=1&cID=' . $Qcategories->valueInt('categories_id') . '&cPath=' . $cPath),'<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
        }
?>
                </td>
<?php
      if (!is_null($Qcategories->value('last_modified'))) {
        echo '<td class="text-md-center">' . DateTime::toShort($Qcategories->value('last_modified')) .'</td>';
      } else {
        echo '<td class="text-md-center"></td>';
      }
?>

                <td class="text-md-center">&nbsp;</td>
                <td class="text-md-center"><?php echo $Qcategories->valueInt('sort_order'); ?></td>
                <td class="text-md-right">
<?php
      echo '<a href="' . $CLICSHOPPING_Categories->link('Edit&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('categories_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Categories->getDef('icon_edit')) . '</a>' ;
      echo '&nbsp;';
      echo '<a href="' . $CLICSHOPPING_Categories->link('Move&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('categories_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/move.gif', $CLICSHOPPING_Categories->getDef('icon_move')) . '</a>' ;
      echo '&nbsp;';
      echo '<a href="' . $CLICSHOPPING_Categories->link('Delete&cPath=' . $cPath . '&cID=' . $Qcategories->valueInt('categories_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Categories->getDef('icon_delete')) . '</a>' ;
      echo '&nbsp;';
?>
                </td>
              </tr>
<?php
    }
  } // end $listingTotalRow
?>
          </tbody>
        </table></td>
      </table>
      </form>
      <div><?php echo $CLICSHOPPING_Categories->getDef('text_categories') . '&nbsp;' . $categories_count; ?></div>
    </div>