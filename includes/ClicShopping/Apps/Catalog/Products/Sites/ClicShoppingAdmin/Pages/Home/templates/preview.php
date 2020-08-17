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

  $CLICSHOPPING_Products = Registry::get('Products');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (isset($_GET['cPath'])) {
    $cPath = HTML::sanitize($_GET['cPath']) ?? $cPath = 0;
  } else {
    $cPath = 0;
  }

  $Qproducts = $CLICSHOPPING_Products->db->prepare('select pd.*,
                                                            p.*
                                                    from :table_products p,
                                                        :table_products_description pd
                                                    where p.products_id = :products_id
                                                    and pd.language_id = :language_id
                                                    and p.products_id = pd.products_id
                                                   ');
  $Qproducts->bindInt(':products_id', (int)$_GET['pID'] );
  $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
  $Qproducts->execute();

  $products = $Qproducts->fetch();

  $Qmanufacturer = $CLICSHOPPING_Products->db->prepare('select m.manufacturers_id,
                                                               m.manufacturers_name,
                                                               p.products_id
                                                         from :table_products p,
                                                              :table_manufacturers m
                                                         where p.products_id = :products_id
                                                         and m.manufacturers_id = p.manufacturers_id
                                                      ');
  $Qmanufacturer->bindInt(':products_id', (int)$_GET['pID'] );
  $Qmanufacturer->execute();

  $manufacturer = $Qmanufacturer->fetch();

  $Qsupplier = $CLICSHOPPING_Products->db->prepare('select s.suppliers_id,
                                                            s.suppliers_name,
                                                            p.products_id
                                                   from :table_products p,
                                                        :table_suppliers s
                                                   where p.products_id = :products_id
                                                   and p.suppliers_id = s.suppliers_id
                                                   ');
  $Qsupplier->bindInt(':products_id', (int)$_GET['pID'] );
  $Qsupplier->execute();

  $supplier = $Qsupplier->fetch();
?>

  <div class="contentBody">
<?php
    if ($Qproducts->valueInt('products_id') == 0) {
?>
    <div class="contentBody">
      <div class="pageHeading text-md-center" valign="center" height="300"><?php echo  $CLICSHOPPING_Products->getDef('text_no_products'); ?></div>
    </div>
<?php
    } else {
?>
    <div class="contentBody">
      <div class="row" id="tab1ContentRow1">
        <div class="col-md-12">
          <div class="card card-block headerCard">
            <div class="row">
              <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/produit.gif', $CLICSHOPPING_Products->getDef('heading_title'), '40', '40'); ?></span>
              <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Products->getDef('heading_title'); ?></span>
              <span class="col-md-6 text-md-right">
                <?php echo HTML::button($CLICSHOPPING_Products->getDef('button_back'), null, $CLICSHOPPING_Products->link('Products'), 'primary'); ?>
                <?php echo HTML::button($CLICSHOPPING_Products->getDef('button_new_product'), null, $CLICSHOPPING_Products->link('Edit&Insert&cPath='. $cPath), 'success'); ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>

      <div class="row" id="tab1ContentRow2">
        <div class="col-md-12">
          <span class="col-md-5 pageHeading float-md-left"><?php echo $CLICSHOPPING_Products->getDef('text_products_name')  . ' - '  . $products['products_name']; ?></span>
          <span class="col-md-7 pageHeading float-md-right text-md-right"><strong><?php echo $CLICSHOPPING_Products->getDef('text_products_model') . ' ' . $products['products_model']; ?></strong></span>
        </div>
        <div class="separator"></div>
        <div class="col-md-12 text-md-center">
<?php
  if (!is_null($products['products_image'])) {
    echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $products['products_image'], $products['products_name']) . '<br />';
  }
?>
        </div>
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $products['products_description']; ?></div>
      </div>



<?php
// ##############################################
// affichage presentation produit                               //
  // ##############################################
?>
      <div class="separator"></div>
      <div class="mainTitle"><?php echo  $CLICSHOPPING_Products->getDef('text_products_presentation'); ?></div>
      <div class="adminformTitle">
        <div class="row" id="tab1ContentRow3">
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_model') . ' ' . $products['products_model']; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_ean') . ' ' . $products['products_ean']; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_sku') . ' ' . $products['products_sku']; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_volume') . ' ' . $products['products_volume']; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_url') . ' ' .$products['products_url']; ?></div>
<?php
  if ($products['products_only_online'] == '1')  {
    $check_products_only_online = true;
  } else {
    $check_products_only_online = false;
  }
?>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_only_online'). ' ' . HTML::checkboxField('products_only_online', '', $check_products_only_online); ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_manufacturer') . ' ' . $manufacturer['manufacturers_name']; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_suppliers') . ' ' . $supplier['suppliers_name']; ?></div>

<?php
  if ($products['products_packaging'] == 0) $products_packaging = '';
  if ($products['products_packaging'] == 1) $products_packaging = $CLICSHOPPING_Products->getDef('text_products_packaging_new');
  if ($products['products_packaging'] == 2) $products_packaging = $CLICSHOPPING_Products->getDef('text_products_packaging_repackaged');
  if ($products['products_packaging'] == 3) $products_packaging = $CLICSHOPPING_Products->getDef('text_products_used');
?>
        </div>
      </div>
<?php
  // ##############################################
  // affichage STOCK produit                               //
  // ##############################################
?>
      <div class="separator"></div>
      <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_stock'); ?></div>
      <div class="adminformTitle">
        <div class="row" id="tab1ContentRow4">
<?php
  if ($products['products_status'] == '1') {
    $products_status = $CLICSHOPPING_Products->getDef('text_products_available');
  }	else {
    $products_status =  $CLICSHOPPING_Products->getDef('text_products_not_available');
  }
?>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_status') . ' ' . $products_status; ?></div>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_quantity') . ' ' . $products['products_quantity']; ?></div>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_min_order_quantity') . ' ' . $products['products_min_qty_order']; ?></div>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_date_available') . ' ' . $products['products_date_available']; ?></div>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_shipping_delay') . ' ' . $products['products_shipping_delay']; ?></div>

        </div>
      </div>
<?php
  // ##############################################
  // affichage prix produit                               //
  // ##############################################
?>
      <div class="separator"></div>
      <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_price_public'); ?></div>
      <div class="adminformTitle">
        <div class="row" id="tab1ContentRow5">
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_price') . ' ' .$products['products_price'] . ' <strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>'; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_cost') . ' ' .$products['products_cost'] . ' <strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>'; ?></div>
          <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_handling') . ' ' . $products['products_handling'] . ' <strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>'; ?></div>
<?php
// Activation du module B2B
  if  (MODE_B2B_B2C == 'true') {
//inserer les informations concernant la B2B

    $QcustomersGroup = $CLICSHOPPING_Products->db->prepare('select distinct customers_group_id,
                                                                           customers_group_name,
                                                                           customers_group_discount
                                                           from :table_customers_groups
                                                           where customers_group_id >  0
                                                           order by customers_group_id
                                                          ');

    $QcustomersGroup->execute();
  }

// Activation du module B2B
  if  (MODE_B2B_B2C == 'true') {
  //inserer les informations concernant la B2B
    while ($customers_group = $QcustomersGroup->fetch()) {

      if ($QcustomersGroup->rowCount() > 0) {

        $Qattributes= $CLICSHOPPING_Products->db->prepare('select g.customers_group_id,
                                                                 g.customers_group_price,
                                                                 g.price_group_view,
                                                                 g.products_group_view,
                                                                 g.orders_group_view,
                                                                 p.products_price,
                                                                 p.products_id
                                                          from :table_products_groups g,
                                                               :table_products p
                                                          where p.products_id = :products_id
                                                          and p.products_id = g.products_id
                                                          and g.customers_group_id = :customers_group_id
                                                          order by g.customers_group_id
                                                          ');
        $Qattributes->bindInt(':products_id', (int)$_GET['pID'] );
        $Qattributes->bindInt(':customers_group_id', (int)$customers_group['customers_group_id'] );

        $Qattributes->execute();

      }
?>
                  <span class="col-md-1"><?php echo $customers_group['customers_group_name']; ?></span>
                  <span class="col-md-3">

<?php
      if ($attributes = $Qattributes->fetch()) {
        echo $attributes['customers_group_price'] .' <strong>' . $CLICSHOPPING_Products->getDef('text_products_preview_price_public') . '</strong><br />';
      } else {
        echo $attributes['customers_group_price'] . ' <strong>' . $CLICSHOPPING_Products->getDef('text_products_preview_price_public') . '</strong><br />';
      }
?>
                  </span>


<?php
    } // end while
?>
              <div class="separator"></div>
              <!-- Afficher autoriser du produit + autorisation commande //-->

                <div class="col-md-2"><?php echo $CLICSHOPPING_Products->getDef('products_view'); ?></div>
<?php
    if (isset($_GET['pID'])) {
  // Si c'est un nouveau produit case coche par defaut

      if ($products['products_view'] == '1') {
        $check_product_view = true;
      } else {
        $check_product_view = false;
      }

      if ($products['orders_view'] == '1') {
        $check_product_order_view = true;
      } else {
        $check_product_order_view = false;
      }

      if ($products['products_price_kilo'] == '1') {
        $check_products_price_kilo = true;
      } else {
        $check_products_price_kilo = false;
      }

?>
      <div><?php echo HTML::checkboxField('products_view', '', $check_product_view) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $CLICSHOPPING_Products->getDef('text_products_view')) . '&nbsp;&nbsp;' . HTML::checkboxField('product_order_view', '', $check_product_order_view)  . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $CLICSHOPPING_Products->getDef('tab_orders_view')); ?>&nbsp;</div>
<?php
    } else {
?>
      <div><?php echo HTML::checkboxField('products_view', '', $check_product_view) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $CLICSHOPPING_Products->getDef('text_products_view')) . '&nbsp;&nbsp;' . HTML::checkboxField('product_order_view', '', $check_product_order_view) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $CLICSHOPPING_Products->getDef('tab_orders_view')); ?>&nbsp;</div>
<?php
    }
?>

<?php
  }

  if (isset($_GET['pID'])) {
    if ($products['products_price_kilo'] == '1') {
      $check_products_price_kilo = true;
    } else {
      $check_products_price_kilo = false;
    }
  }
?>
            <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_price_kilo') . ' ' . HTML::checkboxField('products_view', '', $check_products_price_kilo); ?></div>
          </div>
        </div>

<?php
  // ##############################################
  // affichage referencement                    //
  // ##############################################
?>
      <div class="separator"></div>
      <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_page_seo'); ?></div>
      <div class="adminformTitle">
        <div class="row" id="tab1ContentRow6">
          <div class="col-md-12">
            <span class="col-md-2"><?php echo $CLICSHOPPING_Products->getDef('text_products_page_title'); ?></span>
            <span class="col-md-10"><?php echo $products['products_head_title_tag']; ?></span>
          </div>
          <div class="col-md-12">
            <span class="col-md-2"><?php echo $CLICSHOPPING_Products->getDef('text_products_header_description'); ?></span>
            <span class="col-md-10"><?php echo $products['products_head_desc_tag']; ?></span>
          </div>
          <div class="col-md-12">
            <span class="col-md-2"><?php echo $CLICSHOPPING_Products->getDef('text_products_keywords'); ?></span>
            <span class="col-md-10"><?php echo $products['products_head_keywords_tag']; ?></span>
          </div>
          <div class="col-md-12">
            <span class="col-md-2"><?php echo  $CLICSHOPPING_Products->getDef('text_products_tag'); ?></span>
            <span class="col-md-10"><?php echo $products['products_head_tag']; ?></span>
          </div>
        </div>
      </div>
 </div>
<?php
    echo $CLICSHOPPING_Hooks->output('Preview', 'PreviewContent', null, 'display');
  }