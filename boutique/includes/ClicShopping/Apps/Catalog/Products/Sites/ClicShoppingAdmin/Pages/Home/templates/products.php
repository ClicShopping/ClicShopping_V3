<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Products = Registry::get('Products');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

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
?>

    <div class="contentBody">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-block headerCard">
            <div class="row">
              <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/produit.gif', $CLICSHOPPING_Products->getDef('heading_title'), '40', '40'); ?></span>
              <span class="col-md-1 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Products->getDef('heading_title'); ?></span>
              <span class="col-sm-2 text-md-center">
               <div class="form-group">
                 <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_Products->link('Products'), 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Products->getDef('heading_title_search').'"');
?>
                   </form>
                 </div>
                </div>
              </span>
              <span class="col-sm-2 text-md-center">
               <div class="form-group">
                 <div class="controls">
<?php
 if (isset($_POST['cPath'])) $current_category_id = HTML::sanitize($_POST['cPath']);
  echo HTML::form('goto', $CLICSHOPPING_Products->link('Products'), 'post', ['session_id' => true]);
  echo HTML::selectMenu('cPath', $CLICSHOPPING_CategoriesAdmin->getCategoryTree(), $current_category_id, 'onchange="this.form.submit();"');
?>
                   </form>
                  </div>
                </div>
              </span>

              <span class="col-md-6 text-md-right">
<?php
  if (isset($_GET['search']) || isset($_POST['cPath'])) {
    echo HTML::button($CLICSHOPPING_Products->getDef('button_back'), null, $CLICSHOPPING_Products->link('Products&' . $cPath_back . 'cID=' . $current_category_id), 'primary') . '&nbsp;';
  }

  if (!isset($_GET['search'])) {
    echo HTML::button($CLICSHOPPING_Products->getDef('button_insert'), null, $CLICSHOPPING_Products->link('Edit&cPath=' . $cPath), 'success') . '&nbsp;';
  }

  // select all the product to delete
  echo HTML::form('delete_all', $CLICSHOPPING_Products->link('Products&DeleteAll&cPath=' . $cPath));
?>
                <a onClick="$('delete').prop('action', ''); $('form').submit();" class="button"><?php echo HTML::button($CLICSHOPPING_Products->getDef('button_delete'), null, null, 'danger'); ?></a>&nbsp;
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="card-deck">
            <?php echo $CLICSHOPPING_Hooks->output('Stats', 'StatsProductsOutOfStock', null, 'display'); ?>
          </div>
        </div>
      </div>
      <div class="separator"></div>
<?php
  if (!isset($_POST['cPath'])) {
?>
      <div class="alert alert-info"><?php echo $CLICSHOPPING_Products->getDef('text_alert_info_product'); ?></div>
<?php
  }
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <td>
          <table class="table table-sm table-hover table-striped">
            <tbody>
            <thead>
            <tr class="dataTableHeadingRow">
 <!-- // select all the product to delete -->
              <th width="1" class="text-md-center"><input type="checkbox" onClick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
              <th colspan="3">&nbsp;</th>
              <th><?php echo $CLICSHOPPING_Products->getDef('table_heading_categories_products'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_status'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_price'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_qty'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_last_modified'); ?>&nbsp;</th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_created'); ?>&nbsp;</th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Products->getDef('table_heading_sort_order'); ?>&nbsp;</th>
              <th class="text-md-right"><?php echo $CLICSHOPPING_Products->getDef('table_heading_action'); ?>&nbsp;</th>
            </tr>
            </thead>
<?php
  // ################################################################################################################ -->
  //                                         LISTING PRODUITS                                          -->
  //################################################################################################################ -->
  $products_count = 0;

  $Qproducts = $CLICSHOPPING_ProductsAdmin->getSearch($_POST['search']);

  $listingTotalRow = $Qproducts->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qproducts->fetch() ) {
      $products_count++;
      $rows++;

// Get categories_id for product if search
      if (isset($_POST['search'])) {
        $cPath = $Qproducts->valueInt('categories_id');
      } else {
        if (isset($_POST['cPath'])) {
          $cPath = HTML::sanitize($_POST['cPath']);
        } else {
          $cPath = HTML::sanitize($_GET['cPath']);
        }
      }

      if ((!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['pID']) && ((int)$_GET['pID'] ===  $Qproducts->valueInt('products_id')))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
// find   the rating average from customer reviews
        $Qreviews = $CLICSHOPPING_Products->db->get('reviews', '(avg(reviews_rating) / 5 * 100) as average_rating', ['products_id' => $Qproducts->valueInt('products_id')]);

        $pInfo_array = array_merge($Qproducts->toArray(), $Qreviews->toArray());
        $pInfo = new ObjectInfo($pInfo_array);
      }
?>
                  <td>
<?php // select all the product to delete
  if ($Qproducts->value('selected')) {
?>
                    <input type="checkbox" name="selected[]" value="<?php echo $Qproducts->valueInt('products_id'); ?>" checked="checked" />
<?php
  } else {
?>
                    <input type="checkbox" name="selected[]" value="<?php echo $Qproducts->valueInt('products_id'); ?>" />
<?php
  }
?>
                  </td>

<?php
    if ($Qproducts->valueInt('products_status') == 1) {
?>
                  <td class="dataTableContent" width="20px;"><?php echo '<a href="' . HTTP::getShopUrlDomain() .'index.php?Products&Description&products_id=' . $Qproducts->valueInt('products_id') . '" target="_blank" rel="noreferrer">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview_catalog.png', $CLICSHOPPING_Products->getDef('icon_preview_catalog')) . '</a>'; ?></td>
<?php
    } else {
?>
                  <td></td>
<?php
    }
?>
                  <td width="20px;"><?php echo HTML::link(CLICSHOPPING::link('index.php', 'A&Catalog\Preview&Preview&pID=' . $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_Products->getDef('icon_preview'))); ?></td>
                  <td><?php echo  HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                  <td><?php echo $Qproducts->value('products_name') .' ['. $Qproducts->value('products_model') .']' ; ?></td>
                  <td class="text-md-center">
<?php
    if ($Qproducts->valueInt('products_status') == 1) {
      echo HTML::link($CLICSHOPPING_Products->link('Products&SetFlag&flag=0&pID=' . $Qproducts->valueInt('products_id') . '&cPath=' . $cPath),'<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
    } else {
      echo HTML::link($CLICSHOPPING_Products->link('Products&SetFlag&flag=1&pID=' . $Qproducts->valueInt('products_id') . '&cPath=' . $cPath),'<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
    }
?>
                  </td>
                  <td class="text-md-center"><?php echo $Qproducts->value('products_price'); ?></td>
                  <td class="text-md-center"><?php echo $Qproducts->valueInt('products_quantity'); ?></td>
<?php
    if (!is_null($Qproducts->value('products_last_modified'))) {
      echo '<td class="text-md-center">' . DateTime::toShort($Qproducts->value('products_last_modified')) .'</td>';
    } else {
      echo '<td class="text-md-center"></td>';
    }
?>
                  <td class="text-md-center"><?php echo $Qproducts->value('admin_user_name'); ?></td>
                  <td class="text-md-center"><?php echo $Qproducts->valueInt('products_sort_order'); ?></td>
                  <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_Products->link('Edit&cPath=' . $cPath . '&pID=' .  $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Products->getDef('icon_edit')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Products->link('CopyTo&cPath=' . $cPath . '&pID=' .  $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', $CLICSHOPPING_Products->getDef('icon_copy_to')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Products->link('Move&cPath=' . $cPath . '&pID=' .  $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/move.gif', $CLICSHOPPING_Products->getDef('icon_move')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Products->link('Archive&cPath=' . $cPath . '&pID=' .  $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/archive.gif', $CLICSHOPPING_Products->getDef('icon_archive_to')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Products->link('Delete&cPath=' . $cPath . '&pID=' .  $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Products->getDef('icon_delete')));
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
      <div><?php echo $CLICSHOPPING_Products->getDef('text_products') . '&nbsp;' . $products_count; ?></div>
    </div>