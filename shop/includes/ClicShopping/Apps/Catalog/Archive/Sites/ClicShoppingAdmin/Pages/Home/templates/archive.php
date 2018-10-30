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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Archive = Registry::get('Archive');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $languages = $CLICSHOPPING_Language->getLanguages();
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <div class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/archive.gif', $CLICSHOPPING_Archive->getDef('heading_title'), '40', '40'); ?></div>
          <div class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Archive->getDef('heading_title'); ?></div>
          <div class="col-md-2">
            <div class="form-group">
              <div class="controls">
<?php
  echo HTML::form('search',  $CLICSHOPPING_Archive->link('Archive'), 'post', 'role="form" class="form-inline"', ['session_id' => true]);
  echo HTML::inputField('search', null, 'id="inputKeywords" placeholder=" ' . $CLICSHOPPING_Archive->getDef('heading_title_search') . ' "');
?>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-1">
<?php
  if (isset($_POST['search']) && !is_null($_POST['search'])) {
    echo HTML::button($CLICSHOPPING_Archive->getDef('button_reset'), null, $CLICSHOPPING_Archive->link('Archive&page=' . $_GET['page']), 'warning');
  }
?>
          </div>
          <span class="col-md-3 text-md-right">
              <?php echo HTML::form('delete_all', $CLICSHOPPING_Archive->link('Archive&DeleteAll&page=' . $_GET['page'])); ?>
            <a onclick="$('delete').prop('action', ''); $('form').submit();" class="button"><?php echo HTML::button($CLICSHOPPING_Archive->getDef('button_unpack'), null, null, 'info'); ?></a>&nbsp;
           </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
<?php // Gestion des erreurs et de succes des validations
  if ($CLICSHOPPING_MessageStack->size > 0) {
    $ClassMessageStack = "messageStackSuccess";
    if ($ClassMessageStackError == 1) {
      $ClassMessageStack = "messageStackError";
    }
?>

    <div class="<?php echo $ClassMessageStack; ?>">
      <?php echo $CLICSHOPPING_MessageStack->output(); ?>
    </div>
    <?php
  }
?>
  <!-- //################################################################################################################ -->
  <!-- //                                             LISTING DES produits                                      -->
  <!-- //################################################################################################################ -->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th width="1" class="text-md-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
          <th>&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Archive->getDef('table_heading_model_archives'); ?></th>
          <th><?php echo $CLICSHOPPING_Archive->getDef('table_heading_products_archives'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Archive->getDef('table_heading_date_archives'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Archive->getDef('table_heading_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Archive->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
<?php
  // Recherche
  $search = '';

  if (isset($_POST['search']) && !is_null($_POST['search'])) {
    $keywords = HTML::sanitize($_POST['search']);

    $Qproducts = $CLICSHOPPING_Archive->db->prepare('select  SQL_CALC_FOUND_ROWS   p.products_id,
                                                                            p.products_model,
                                                                            p.products_image,
                                                                            p.products_price,
                                                                            pd.products_name,
                                                                            p.products_date_added,
                                                                            p.products_last_modified,
                                                                            p.products_status,
                                                                            p.products_archive
                                             from :table_products p,
                                                  :table_products_description pd
                                             where p.products_id = pd.products_id
                                             and p.products_archive = 1
                                             and pd.language_id = :language_id
                                             and (p.products_model like :search
                                                  or  pd.products_name like :search)
                                             order by  p.products_last_modified DESC, pd.products_name
                                             limit :page_set_offset,
                                                  :page_set_max_results
                                            ');

    $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
    $Qproducts->bindValue(':search', '%' . $keywords . '%');
    $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qproducts->execute();

  } else {
    $Qproducts = $CLICSHOPPING_Archive->db->prepare('select  SQL_CALC_FOUND_ROWS   p.products_id,
                                                                            p.products_model,
                                                                            p.products_image,
                                                                            p.products_price,
                                                                            pd.products_name,
                                                                            p.products_date_added,
                                                                            p.products_last_modified,
                                                                            p.products_status,
                                                                            p.products_archive
                                               from :table_products p,
                                                    :table_products_description pd
                                               where p.products_id = pd.products_id
                                               and p.products_archive = 1
                                               and pd.language_id = :language_id
                                               order by  p.products_last_modified DESC, pd.products_name
                                               limit :page_set_offset,
                                                    :page_set_max_results
                                              ');

    $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId() );
    $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qproducts->execute();
  }


  $listingTotalRow = $Qproducts->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

  while ($products = $Qproducts->fetch()) {

    if ((!isset($_GET['aID']) || (isset($_GET['aID']) && ((int)$_GET['aID'] ===  $Qproducts->valueInt('products_id')))) && !isset($mInfo)) {

      $QproductArvhice = $CLICSHOPPING_Archive->db->prepare('select count(*) as products_count
                                                      from :table_products
                                                      where products_id = :products_id
                                                      and products_archive = 1
                                                    ');

      $QproductArvhice->bindInt(':products_id', $Qproducts->valueInt('products_id') );
      $Qproducts->execute();

      $products_archive = $Qproducts->fetch();

      $mInfo_array = array_merge($products, $products_archive);
      $mInfo = new ObjectInfo($mInfo_array);
    }
?>
            <th scope="row">
<?php
    if ($products['selected']) {
?>
              <input type="checkbox" name="selected[]" value="<?php echo  $Qproducts->valueInt('products_id'); ?>" checked="checked" />
<?php
    } else {
?>
              <input type="checkbox" name="selected[]" value="<?php echo  $Qproducts->valueInt('products_id'); ?>" />
<?php
    }
?>
            </th>
            <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
            <td><?php echo $Qproducts->value('products_model'); ?></td>
            <td><?php echo $Qproducts->value('products_name'); ?></td>
<?php
      if (!is_null($Qproducts->value('last_modified'))) {
        echo '<td class="text-md-center">' . DateTime::toShort($Qproducts->value('last_modified')) .'</td>';
      } else {
        echo '<td class="text-md-center"></td>';
      }
?>
            <td class="text-md-center">
<?php
      if ($Qproducts->valueInt('products_status') == 1) {
        echo '<a href="' . $CLICSHOPPING_Archive->link('Archive&SetFlag&flag=0&aID=' .  $Qproducts->valueInt('products_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
      } else {
        echo '<a href="' . $CLICSHOPPING_Archive->link('Archive&SetFlag&flag=1&aID=' .  $Qproducts->valueInt('products_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
      }
?>
            </td>
            <td class="text-md-right">
<?php
      echo '<a href="' . $CLICSHOPPING_Archive->link('Archive&Update&page=' . $_GET['page'] . '&aID=' .  $Qproducts->valueInt('products_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/unpack.gif', $CLICSHOPPING_Archive->getDef('icon_unpack')) . '</a>' ;
      echo '&nbsp;';
?>
            </td>
          </tbody>
        </tr>
<?php
    } // end while
  } // end $listingTotalRow
?>
      </table>
    </td>
    </form>
  </table>

<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts->getPageSetLabel($CLICSHOPPING_Archive->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qproducts->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
  }
?>
</div>
