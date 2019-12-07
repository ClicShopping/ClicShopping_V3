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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Reviews = Registry::get('Reviews');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

  if ($CLICSHOPPING_MessageStack->exists('reviews')) {
    echo $CLICSHOPPING_MessageStack->get('reviews');
  }

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;
?>

<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/reviews.gif', $CLICSHOPPING_Reviews->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Reviews->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-md-right">
            <?php echo HTML::form('delete_all', $CLICSHOPPING_Reviews->link('Reviews&DeleteAll&page=' . $page)); ?>
            <a onclick="$('delete').prop('action', ''); $('form').submit();"
               class="button"><?php echo HTML::button($CLICSHOPPING_Reviews->getDef('button_delete'), null, null, 'danger'); ?></a>&nbsp;
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <!-- //################################################################################################################ -->
  <!-- //                                            LISTING DES AVIS CLIENTS                                             -->
  <!-- //################################################################################################################ -->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th class="text-md-center" width="1"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_products'); ?></th>
          <th><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_rating'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_review_author'); ?></th>
          <th
            class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_products_average_rating'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_review_read'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_review_group'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_last_modified'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_approved'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Reviews->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php

          $Qreviews = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS  r.reviews_id,
                                                                     r.products_id,
                                                                     r.date_added,
                                                                     r.last_modified,
                                                                     r.reviews_rating,
                                                                     r.status,
                                                                     r.customers_group_id,
                                                                     r.customers_name,
                                                                     p.products_image
                                      from :table_reviews r,
                                           :table_products p
                                      where p.products_id = r.products_id
                                      order by r.date_added desc
                                      limit :page_set_offset, :page_set_max_results
                                      ');

          $Qreviews->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qreviews->execute();

          $listingTotalRow = $Qreviews->getPageSetTotalRows();

          if ($listingTotalRow > 0) {
            while ($Qreviews->fetch()) {
              if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ((int)$_GET['rID'] === $Qreviews->valueInt('reviews_id')))) && !isset($rInfo)) {
                $QreviewsText = $CLICSHOPPING_Db->get(['reviews r',
                  'reviews_description rd',
                ], [
                  'r.reviews_read',
                  'r.customers_name',
                  'length(rd.reviews_text) as reviews_text_size',
                ], [
                    'r.reviews_id' => [
                      'val' => $Qreviews->valueInt('reviews_id'),
                      'ref' => 'rd.reviews_id'
                    ]
                  ]
                );

                $Qproducts_image = $CLICSHOPPING_Db->prepare('select products_image
                                                       from :table_products
                                                       where products_id = :products_id
                                                      ');
                $Qproducts_image->bindInt(':products_id', $Qreviews->valueInt('products_id'));
                $Qproducts_image->execute();

                $Qproducts = $CLICSHOPPING_Db->prepare('select products_name
                                                 from :table_products_description
                                                 where products_id = :products_id
                                                 and language_id = :language_id
                                                 ');
                $Qproducts->bindInt(':products_id', $Qreviews->valueInt('products_id'));
                $Qproducts->bindint(':language_id', $CLICSHOPPING_Language->getId());
                $Qproducts->execute();

                $Qaverage = $CLICSHOPPING_Db->get('reviews', ['(avg(reviews_rating) / 5 * 100) as average_rating'],
                  ['products_id' => $Qreviews->valueInt('products_id')]
                );

                $review_info = array_merge($QreviewsText->toArray(), $Qaverage->toArray(), $Qproducts->toArray());

                if ($Qproducts_image->fetch()) {
                  $rInfo_array = array_merge($Qreviews->toArray(), (array)$review_info, $Qproducts_image->toArray());
                } else {
                  $rInfo_array = array_merge($Qreviews->toArray(), (array)$review_info);
                }

                $rInfo = new ObjectInfo($rInfo_array);
              }

              $QcustomerGroup = $CLICSHOPPING_Db->prepare('select customers_group_id,
                                                         customers_group_name
                                                   from :table_customers_groups
                                                   where customers_group_id = :customers_group_id
                                                  ');
              $QcustomerGroup->bindInt(':customers_group_id', $Qreviews->valueInt('customers_group_id'));
              $QcustomerGroup->execute();

              if ($QcustomerGroup->valueInt('customers_group_id') == 0) {
                $customer_group['customers_group_name'] = $CLICSHOPPING_Reviews->getDef('visitor_name');
              }

              ?>
              <td>
                <?php
                  if (isset($_POST['selected'])) {
                    ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $Qreviews->valueInt('reviews_id'); ?>"
                           checked="checked"/>
                    <?php
                  } else {
                    ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $Qreviews->valueInt('reviews_id'); ?>"/>
                    <?php
                  }
                ?>
              </td>
              <td></td>
              <td scope="row"
                  width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qreviews->valueInt('products_id') . '?page=' . $page), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_Reviews->getDef('icon_preview_comment'))); ?></td>
              <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qreviews->value('products_image'), $Qreviews->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
              <td><?php echo $CLICSHOPPING_ProductsAdmin->getProductsName($Qreviews->valueInt('products_id')); ?></td>
              <td><?php echo '<i>' . HTML::stars($Qreviews->valueInt('reviews_rating')) . '</i>'; ?></td>
              <td class="text-md-center"><?php echo $Qreviews->value('customers_name'); ?></td>
              <td
                class="text-md-center"><?php echo number_format($Qreviews->valueDecimal('average_rating'), 2) . '%'; ?></td>
              <td class="text-md-center"><?php echo number_format($Qreviews->valueInt('reviews_read', 2)); ?></td>
              <td class="text-md-center"><?php echo $customer_group['customers_group_name']; ?></td>
              <td class="text-md-center"><?php echo DateTime::toLong($Qreviews->value('last_modified')); ?>s</td>
              <td class="text-md-center">
                <?php
                  if ($Qreviews->valueInt('status') == 1) {
                    echo HTML::link($CLICSHOPPING_Reviews->link('Reviews&SetFlag&flag=0&id=' . $Qreviews->valueInt('reviews_id')), '<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
                  } else {
                    echo HTML::link($CLICSHOPPING_Reviews->link('Reviews&SetFlag&flag=1&id=' . $Qreviews->valueInt('reviews_id')), '<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
                  }
                ?>
              </td>
              <td
                class="text-md-right"><?php echo HTML::link($CLICSHOPPING_Reviews->link('&Edit&page=' . $page . '&rID=' . $Qreviews->valueInt('reviews_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Reviews->getDef('icon_edit'))); ?></td>
              </tr>
              <?php
            } //end while
          } //end $listingTotalRow
        ?>
        </tbody>
        </form><!-- end form delete all -->
      </table>
    </td>
  </table>
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qreviews->getPageSetLabel($CLICSHOPPING_Reviews->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qreviews->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>