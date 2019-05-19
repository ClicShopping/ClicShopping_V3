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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin\SeoAdmin;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  use ClicShopping\Sites\ClicShoppingAdmin\Tax;
  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  use ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin\WeightAdmin;

  $CLICSHOPPING_Products = Registry::get('Products');
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_Hooks->call('Products', 'PreAction');

  $languages = $CLICSHOPPING_Language->getLanguages();

  $tax = new tax;

  // check if the catalog image directory exists
  if (is_dir($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages())) {
    if (!FileSystem::isWritable($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages())) $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Products->getDef('error_catalog_image_directory_not_writeable'), 'warning');
  }

  $parameters = [
    'products_name' => '',
    'products_description' => '',
    'products_url' => '',
    'products_id' => '',
    'products_quantity' => '',
    'products_model' => '',
    'products_ean' => '',
    'products_sku' => '',
    'products_image' => '',
    'products_image_zoom' => '',
    'products_larger_images' => [],
    'products_price' => '',
    'products_weight' => '',
    'products_price_kilo' => '',
    'products_date_added' => '',
    'products_last_modified' => '',
    'products_date_available' => '',
    'products_status' => '',
    'products_percentage' => '',
    'products_view' => '',
    'orders_view' => '',
    'products_tax_class_id' => '',
    'products_min_qty_order' => '',
    'products_only_online' => '',
    'products_image_medium' => '',
    'products_cost' => '',
    'products_handling' => '',
    'products_packaging' => '',
    'products_sort_order' => '',
    'products_shipping_delay' => '',
    'products_quantity_alert' => '',
    'products_only_shop' => '',
    'products_download_filename' => '',
    'products_download_public' => '',
    'products_description_summary' => '',
    'products_type' => '',
  ];

  $pInfo = new ObjectInfo($parameters);

  if (isset($_GET['pID']) && isset($_POST)) {
// products_view : Affichage Produit Grand Public - orders_view : Autorisation Commande - Referencement
    $data_products = $CLICSHOPPING_ProductsAdmin->get($_GET['pID']);
    $pInfo->ObjectInfo($data_products);

    $Qimages = $CLICSHOPPING_Products->db->get('products_images', [
      'id',
      'image',
      'htmlcontent',
      'sort_order'
    ], [
      'products_id' => (int)$pInfo->products_id
    ],
      'sort_order'
    );

    while ($Qimages->fetch()) {
      $pInfo->products_larger_images[] = [
        'id' => $Qimages->valueInt('id'),
        'image' => $Qimages->value('image'),
        'htmlcontent' => $Qimages->value('htmlcontent'),
        'sort_order' => $Qimages->valueInt('sort_order')
      ];
    }
  }

  $cPath = 0;

  if (isset($_GET['cPath'])) {
    $cPath = HTML::sanitize($_GET['cPath']);
  }

  $form_action = (isset($_GET['pID'])) ? 'Update' : 'Insert';

  echo HTML::form('new_product', $CLICSHOPPING_Products->link('Products&' . $form_action . '&cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')), 'post', 'enctype="multipart/form-data" id="new_product"');

  echo HTMLOverrideAdmin::getCkeditor();
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/produit.gif', $CLICSHOPPING_Products->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Products->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-md-right">
<?php
  echo HTML::hiddenField('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
  echo HTML::hiddenField('parent_id', $cPath);
  echo HTML::hiddenField('cPath', HTML::sanitize($cPath));

  if ($form_action == 'Update') {
    echo HTML::button($CLICSHOPPING_Products->getDef('button_update'), null, null, 'success') . ' ';
  } else {
    echo HTML::button($CLICSHOPPING_Products->getDef('button_insert'), null, null, 'success') . ' ';
  }

  echo HTML::button($CLICSHOPPING_Products->getDef('button_cancel'), null, $CLICSHOPPING_Products->link('Products&cPath=' . $cPath), 'warning');
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div id="productsTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Products->getDef('tab_general') . '</a>'; ?></li>
      <li
        class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_stock'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_price'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_description'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab5" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_img'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab6" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_ref'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab9" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Products->getDef('tab_other_options'); ?></a></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <?php
          // packaging
          $products_packaging_array = array(array('id' => '0', 'text' => $CLICSHOPPING_Products->getDef('text_choose')),
            array('id' => '1', 'text' => $CLICSHOPPING_Products->getDef('text_products_packaging_new')),
            array('id' => '2', 'text' => $CLICSHOPPING_Products->getDef('text_products_packaging_repackaged')),
            array('id' => '3', 'text' => $CLICSHOPPING_Products->getDef('text_products_packaging_used'))
          );


          // ******************************************
          // Tab 1 General
          //*******************************************
        ?>
        <script
          src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/modal_popup.js'); ?>"></script>
        <style>.modal-dialog {
            width: 900px !important;
          } </style>

        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_Products->getDef('text_products_name'); ?></div>
            <div
              class="float-md-right"><?php echo $CLICSHOPPING_Products->getDef('text_user_name') . ' ' . AdministratorAdmin::getUserAdmin(); ?></div>
          </div>
          <div class="adminformTitle" id="tab1ContentRow1">
            <?php
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
                ?>
                <div class="form-group row">
                  <label for="code"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  <div
                    class="col-md-5"><?php echo HTML::inputField('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? $products_name[$languages[$i]['id']] : $CLICSHOPPING_ProductsAdmin->getProductsName($pInfo->products_id, $languages[$i]['id'])), 'required aria-required="true" id="products_name" placeholder="' . $CLICSHOPPING_Products->getDef('text_products_name') . '"', true) . '&nbsp;'; ?></div>
                </div>
                <?php
              }
            ?>
          </div>
          <div class="separator"></div>
          <div
            class="col-md-12 mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_other_information'); ?></div>
          <div class="adminformTitle">
            <div class="row" id="tab1ContentRow2"></div>

            <div class="row" id="tab1ContentRow3">
              <div class="col-md-5" id="tab1ContentRow3Model">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_model'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_model'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_model', $pInfo->products_model, 'id="products_model" placeholder="' . CONFIGURATION_PREFIX_MODEL . '"'); ?>
                    <a
                      href="<?php echo $CLICSHOPPING_Products->link('ConfigurationPopUpFields&cKey=CONFIGURATION_PREFIX_MODEL'); ?>"
                      data-toggle="modal" data-refresh="true"
                      data-target="#myModal"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Products->getDef('text_edit')); ?></a>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-body">
                            <div class="te"></div>
                          </div>
                        </div> <!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                  </div>
                </div>
              </div>
              <div class="col-md-5" id="tab1ContentRow3Sku">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_sku'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_sku'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_sku', $pInfo->products_sku, 'id="products_sku" maxlength="15"'); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" id="tab1ContentRow4">
              <div class="col-md-5" id="tab1ContentRow4Ean">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_ean'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_ean'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_ean', $pInfo->products_ean, 'id="products_ean" maxlength="15"'); ?>
                  </div>
                </div>
              </div>
              <div class="col-md-5" id="tab1ContentRow4ProducsPackaging">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_packaging'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_packaging'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::selectField('products_packaging', $products_packaging_array, $pInfo->products_packaging); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" id="tab1ContentRow5">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_weight'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_weight'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_weight', $pInfo->products_weight, 'id="products_weight" onKeyUp="return calc_poids(\'products_weight\',value)" size="12"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow6"></div>

            <div class="row" id="tab1ContentRow8">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_only_shop'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_only_shop'); ?></label>
                  <div class="col-md-5">
                    <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                      <span
                        class="col-md-1"><?php echo HTML::checkboxField('products_only_shop', '1', $pInfo->products_only_shop); ?></span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_only_online'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_only_online'); ?></label>
                  <div class="col-md-5">
                    <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                      <span
                        class="col-md-1"><?php echo HTML::checkboxField('products_only_online', '1', $pInfo->products_only_online); ?></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div
            class="col-md-12 mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_other_information'); ?></div>
          <div class="adminformTitle">
            <div class="col-md-12" style="padding-top:10px; padding-bottom:10px;" id="tab1ContentRow10">
              <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_url'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_url') . ' <small>' . $CLICSHOPPING_Products->getDef('text_products_url_without_http') . '</small>'; ?></label>
              <?php
                for ($i = 0, $n = count($languages); $i < $n; $i++) {
                  ?>
                  <div class="form-group row">
                    <label for="code"
                           class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    <div
                      class="col-md-5"><?php echo HTML::inputField('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? $products_url[$languages[$i]['id']] : $CLICSHOPPING_ProductsAdmin->getProductsUrl($pInfo->products_id, $languages[$i]['id'], 'id="products_url[' . $languages[$i]['id'] . ']"'))); ?></div>
                  </div>
                  <?php
                }
              ?>
            </div>

            <div class="col-md-12" style="padding-top:10px; padding-bottom:10px;" id="tab1ContentRow12">
              <span
                class="col-sm-4"><?php echo $CLICSHOPPING_Products->getDef('text_products_shipping_delay'); ?></span>
              <span>
            <a
              href="<?php echo $CLICSHOPPING_Products->link('ConfigurationPopUpFields&cKey=DISPLAY_SHIPPING_DELAY'); ?>"
              data-toggle="modal" data-refresh="true"
              data-target="#myModal"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Products->getDef('text_create')); ?></a>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-body"><div class="te"></div></div>
                </div> <!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
          </span>
            </div>
            <?php
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
                ?>
                <div class="form-group row">
                  <label for="code"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  <div
                    class="col-md-5"><?php echo HTML::inputField('products_shipping_delay[' . $languages[$i]['id'] . ']', (isset($products_shipping_delay[$languages[$i]['id']]) ? $products_shipping_delay[$languages[$i]['id']] : $CLICSHOPPING_ProductsAdmin->getProductsShippingDelay($pInfo->products_id, $languages[$i]['id'])), 'size="90"'); ?></div>
                </div>
                <?php
              }
            ?>
            <div class="row" id="tab1ContentRow13">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_sort_order'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_sort_order'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_sort_order', $pInfo->products_sort_order, 'size="5"'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Products->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Products->getDef('title_help_general') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_Products->getDef('help_general'); ?></div>
          </div>
          <?php echo $CLICSHOPPING_Hooks->output('Products', 'ProductsContentTab1', null, 'display'); ?>
        </div>

        <?php
          // ******************************************
          // Tab 2 Stock
          //*******************************************

          if (!isset($pInfo->products_status)) $pInfo->products_status = '1';
          switch ($pInfo->products_status) {
            case '0':
              $in_status = false;
              $out_status = true;
              break;
            case '1':
            default:
              $in_status = true;
              $out_status = false;
          }

          $stockable_dopdown = array(array('id' => 'product', 'text' => $CLICSHOPPING_Products->getDef('text_stockable_product')),
            array('id' => 'consu', 'text' => $CLICSHOPPING_Products->getDef('text_stockable_consumable')),
            array('id' => 'service', 'text' => $CLICSHOPPING_Products->getDef('text_stockable_service')),
          );
        ?>
        <div class="tab-pane" id="tab2">
          <div class="col-md-12 mainTitle">
            <span><?php echo $CLICSHOPPING_Products->getDef('text_products_stock'); ?></span>
          </div>
          <div class="adminformTitle">
            <div class="row" id="tab2ContentRow1">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_status'); ?>"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_status'); ?></label>
                  <div class="col-md-7">
                    <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                      <span
                        class="col-md-12"><?php echo HTML::radioField('products_status', '1', $in_status) . '&nbsp;' . $CLICSHOPPING_Products->getDef('text_products_available'); ?></span>
                      <span
                        class="col-md-10"><?php echo HTML::radioField('products_status', '0', $out_status) . '&nbsp;' . $CLICSHOPPING_Products->getDef('text_products_not_available'); ?></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab2ContentRow21">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_stock'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_stock'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_quantity', $pInfo->products_quantity, 'id="products_quantity"'); ?>
                  </div>
                </div>
              </div>


              <div class="col-md-5" id="tab2ContentRow3">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_alert'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_alert'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_quantity_alert', $pInfo->products_quantity_alert, 'id="products_quantity_alert"'); ?>
                    <a
                      href="<?php echo $CLICSHOPPING_Products->link('ConfigurationPopUpFields&cKey=STOCK_REORDER_LEVEL'); ?>"
                      data-toggle="modal" data-refresh="true"
                      data-target="#myModal2"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Products->getDef('text_edit_default_configuration')); ?></a>
                    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-body">
                            <div class="te"></div>
                          </div>
                        </div> <!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                  </div>
                </div>
              </div>
            </div>


            <div class="row" id="tab2ContentRow5">

              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_min_order_quantity'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_min_order_quantity'); ?></label>
                  <div class="col-md-5">
                    <?php
                      if ($pInfo->products_min_qty_order == '') {
                        $products_min_qty_order = 1;
                      } else {
                        $products_min_qty_order = $pInfo->products_min_qty_order;
                      }
                      echo HTML::inputField('products_min_qty_order', $products_min_qty_order, 'id="products_min_qty_order"');
                    ?>
                    <a
                      href="<?php echo $CLICSHOPPING_Products->link('ConfigurationPopUpFields&cKey=MAX_MIN_IN_CART'); ?>"
                      data-toggle="modal" data-refresh="true"
                      data-target="#myModal2"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Products->getDef('text_edit_default_configuration')); ?></a>
                    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-body">
                            <div class="te"></div>
                          </div>
                        </div> <!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab2ContentRow6">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_type'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_type'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::selectMenu('products_type', $stockable_dopdown, $pInfo->products_type); ?>
                  </div>
                </div>
              </div>
              <?php
                if ($pInfo->products_date_available != '') {
                  $products_date_available = DateTime::toShort($pInfo->products_date_available);
                } else {
                  $products_date_available = $pInfo->products_date_available;
                }
              ?>
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_date_available'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_date_available'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('products_date_available', $products_date_available, 'id="products_date_available"'); ?>
                  </div>
                  <div class="input-group-addon"><span class="fas fa-calendar"></span></div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div id="tab2ContentRow7"></div>

          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Products->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Products->getDef('title_help_general') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_Products->getDef('help_stock'); ?></div>
          </div>
          <?php echo $CLICSHOPPING_Hooks->output('Products', 'ProductsContentTab2', null, 'display'); ?>
        </div>
        <?php
          // ******************************************
          // Tab 3 Price
          //*******************************************

          if (!isset($pInfo->products_percentage)) $pInfo->products_percentage = '1';

          switch ($pInfo->products_percentage) {
            case '0':
              $in_percent = false;
              $out_percent = true;
              break;
            case '1':
            default:
              $in_percent = true;
              $out_percent = false;
              break;
          }

          $tax_class_drop_down = Tax::taxClassDropDown();
        ?>
        <script type="text/javascript">
            var tax_rates = new Array();
            <?php
            for ($i = 0, $n = count($tax_class_drop_down); $i < $n; $i++) {
              if ($tax_class_drop_down[$i]['id'] > 0) {
                echo 'tax_rates["' . $tax_class_drop_down[$i]['id'] . '"] = ' . $tax->getTaxRateValue($tax_class_drop_down[$i]['id']) . ';' . "\n";
              }
            }
            ?>

            function doRound(x, places) {
                return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
            }

            function getTaxRate() {
                var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
                var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

                if ((parameterVal > 0) && (tax_rates[parameterVal] > 0)) {
                    return tax_rates[parameterVal];
                } else {
                    return 0;
                }
            }

            function updateGross() {
                var taxRate = getTaxRate();
                var grossValue = document.forms["new_product"].products_price.value;

                if (taxRate > 0) {
                    grossValue = grossValue * ((taxRate / 100) + 1);
                }

              <?php
              // Desactivation du module B2B
              if (MODE_B2B_B2C == 'true') {
              $QcustomersGroup = $CLICSHOPPING_Products->db->prepare('select distinct customers_group_id,
                                                                             customers_group_name,
                                                                             customers_group_discount
                                                             from :table_customers_groups
                                                             where customers_group_id != 0
                                                             order by customers_group_id
                                                            ');

              $QcustomersGroup->execute();

              while ($QcustomersGroup->fetch() ) {
              ?>
                var grossValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> = document.forms["new_product"].price<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>.value;

                if (taxRate > 0) {
                    grossValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> = grossValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> * ((taxRate / 100) + 1);
                }

                document.forms["new_product"].price_gross<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>.value = doRound(grossValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>, 4);
              <?php
              }
              }
              ?>
                document.forms["new_product"].products_price_gross.value = doRound(grossValue, 4);
            }

            /********************************/
            /*        Margin report   */
            /********************************/
            function updateMargin() {
                var grossValue = document.forms["new_product"].products_price.value; // valeur net du prix
                var costValue = document.forms["new_product"].products_cost.value; // cout d'achat
                var handlingValue = document.forms["new_product"].products_handling.value; // manutention ou autres frais

                if (isNaN(costValue)) costValue = 0;
                if (isNaN(handlingValue)) handlingValue = 0;

                marginValue = 100 - (((parseInt(costValue) + parseInt(handlingValue)) / parseInt(grossValue)) * 100);
                marginValue = Math.round(marginValue, 2);
                document.getElementById('products_price_margins').innerHTML = marginValue + "%";
            }

            function updateNet() {
                var taxRate = getTaxRate();
                var netValue = document.forms["new_product"].products_price_gross.value;

                if (taxRate > 0) {
                    netValue = netValue / ((taxRate / 100) + 1);
                }

              <?php
              // Desactivation du module B2B
              if (MODE_B2B_B2C == 'true') {
              $QcustomersGroup = $CLICSHOPPING_Products->db->prepare('select distinct customers_group_id,
                                                                            customers_group_name,
                                                                            customers_group_discount
                                                             from :table_customers_groups
                                                             where customers_group_id != 0
                                                             order by customers_group_id
                                                            ');

              $QcustomersGroup->execute();

              while ($QcustomersGroup->fetch() ) {
              ?>
                var netValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> = document.forms["new_product"].price_gross<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>.value;

                if (taxRate > 0) {
                    netValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> = netValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?> / ((taxRate / 100) + 1);
                }

                document.forms["new_product"].price<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>.value = doRound(netValue<?php echo $QcustomersGroup->valueInt('customers_group_id'); ?>, 4);
              <?php
              }
              }
              ?>

                document.forms["new_product"].products_price.value = doRound(netValue, 4);
            }
        </script>

        <div class="tab-pane" id="tab3">
          <div class="col-md-12 mainTitle">
            <span><?php echo $CLICSHOPPING_Products->getDef('text_products_price_public'); ?></span>
          </div>
          <div class="adminformTitle" style="padding-top: 0rem; padding-left: 0rem; padding-bottom: 0rem;">
            <div style="background-color:#ebebff; height:100%;">
              <div class="separator"></div>
              <div class="row" id="tab3ContentRow1">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_tax_class'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_tax_class'); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::selectMenu('products_tax_class_id', $tax_class_drop_down, $pInfo->products_tax_class_id, 'onchange="updateGross()"'); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="tab3ContentRow2">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_price'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_price'); ?></label>
                    <div class="col-md-5">
                      <?php
                        echo HTML::inputField('products_price', $pInfo->products_price, 'id="products_price" onkeyup="updateGross()"') . '<strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>';
                      ?>
                    </div>
                  </div>
                </div>

                <div class="col-md-5">
                  <div class="form-group row">
                    <div class="col-md-5">
                      <?php
                        if (DISPLAY_DOUBLE_TAXE == 'false') {
                          echo HTML::inputField('products_price_gross', $pInfo->products_price, 'id="products_price_gross" onkeyup="updateNet()"') . '<strong>' . $CLICSHOPPING_Products->getDef('text_products_price_gross') . '</strong>';
                        }
                      ?>
                    </div>
                  </div>
                </div>
                <div id="ProductPrice"></div>
              </div>

              <div class="row" id="tab3ContentRow3">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_cost'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_cost'); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::inputField('products_cost', $pInfo->products_cost, 'id="products_cost" onkeyUp="updateMargin()"') . '<strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>'; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="tab3ContentRow4">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_handling'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_handling'); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::inputField('products_handling', $pInfo->products_handling, 'id="products_handling" onkeyUp="updateMargin()"') . '<strong>' . $CLICSHOPPING_Products->getDef('text_products_price_net') . '</strong>'; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="tab3ContentRow5">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_price_margins'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_price_margins'); ?></label>
                    <div class="col-md-5">
                      <span id='products_price_margins'></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="tab3ContentRow6">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('products_view'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('products_view'); ?></label>
                    <div class="col-md-5">
                      <?php
                        if (isset($_GET['pID'])) {
                          ?>
                          <span><?php echo HTML::checkboxField('products_view', '1', $pInfo->products_view) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $CLICSHOPPING_Products->getDef('text_products_view')) . '&nbsp;&nbsp;' . HTML::checkboxField('orders_view', '1', $pInfo->orders_view) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $CLICSHOPPING_Products->getDef('tab_orders_view')); ?>&nbsp;</span>
                          <?php
                        } else {
                          ?>
                          <span><?php echo HTML::checkboxField('products_view', '1', true) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $CLICSHOPPING_Products->getDef('text_products_view')) . '&nbsp;&nbsp;' . HTML::checkboxField('orders_view', '1', true) . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $CLICSHOPPING_Products->getDef('tab_orders_view')); ?>&nbsp;</span>
                          <?php
                        }
                      ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="tab3ContentRow7">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_price_kilo'); ?>"
                           class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_price_kilo'); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::checkboxField('products_price_kilo', '1', $pInfo->products_price_kilo); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div id="tab3ContentPriceB2B">
            <?php echo $CLICSHOPPING_Hooks->output('Products', 'CustomerGroupTab3', null, 'display'); ?>
          </div>

          <script type="text/javascript">
              updateGross();
          </script>
        </div>

        <?php
          // ******************************************
          // Tab 4 Description
          //*******************************************
        ?>
        <div class="tab-pane" id="tab4">
          <div class="col-md-12 mainTitle">
            <span><?php echo $CLICSHOPPING_Products->getDef('text_products_description'); ?></span>
          </div>
          <div class="adminformTitle">
            <div class="separator"></div>
            <div>
              <?php
                for ($i = 0, $n = count($languages); $i < $n; $i++) {
                  ?>
                  <div class="row" id="tab4ContentRow1">
                    <span
                      class="col-sm-2"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?>&nbsp;</span>
                    <span class="col-sm-10">
            <div
              style="visibility:visible; display:block;"><?php echo HTMLOverrideAdmin::textAreaCkeditor('products_description[' . $languages[$i]['id'] . ']', 'soft', '750', '300', (isset($products_description[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($products_description[$languages[$i]['id']])) : $CLICSHOPPING_ProductsAdmin->getProductsDescription($pInfo->products_id, $languages[$i]['id']))); ?></div>
          </span>
                  </div>
                  <div class="separator"></div>
                  <div id="tab4ContentRow2">
                    <span
                      class="col-sm-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_description_summary'); ?></span>
                  </div>
                  <div class="row" id="tab4ContentRow3">
                    <span
                      class="col-sm-2"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?>&nbsp;</span>
                    <span class="col-sm-7">
            <?php echo HTML::textAreaField('products_description_summary[' . $languages[$i]['id'] . ']', (isset($products_description_summary[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($products_description_summary[$languages[$i]['id']])) : $CLICSHOPPING_ProductsAdmin->getProductsDescriptionSummary($pInfo->products_id, $languages[$i]['id'])), '120', '3'); ?>
          </span>
                  </div>
                  <div class="separator"></div>
                  <?php
                }
              ?>
            </div>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Products->getDef('title_help_description')) . ' ' . $CLICSHOPPING_Products->getDef('title_help_description') ?></div>
            <div class="separator"></div>
            <div class="row">
          <span class="col-sm-12">
            <?php echo $CLICSHOPPING_Products->getDef('title_help_description'); ?>
            <blockquote><i><a data-toggle="modal"
                              data-target="#myModalWysiwyg"><?php echo $CLICSHOPPING_Products->getDef('text_help_wysiwyg'); ?></a></i></blockquote>
            <div class="modal fade" id="myModalWysiwyg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title"
                        id="myModalLabel"><?php echo $CLICSHOPPING_Products->getDef('text_help_wysiwyg'); ?></h4>
                  </div>
                  <div class="modal-body text-md-center">
                    <img class="img-fluid"
                         src="<?php echo $CLICSHOPPING_Template->getImageDirectory() . '/wysiwyg.png'; ?>">
                  </div>
                </div>
              </div>
            </div>
          </span>
            </div>
          </div>
        </div>

        <?php
          // ******************************************
          // Tab 5 Image
          //*******************************************
        ?>
        <div class="tab-pane" id="tab5">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_image'); ?></div>
          <div class="adminformTitle">
            <div class="row" id="tab5ContentRow1">
              <div class="col-md-6 float-md-left">
                <div class="col-md-12">
                  <span
                    class="col-sm-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product.gif', $CLICSHOPPING_Products->getDef('text_products_image_vignette'), '40', '40'); ?></span>
                  <span
                    class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_insert_big_image_vignette'); ?></span>
                </div>
                <div class="separator"></div>
                <div class="col-md-12">
                  <span
                    class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_directory'); ?></span>
                  <span
                    class="col-sm-6"><?php echo HTML::selectMenu('directory_products_image', $CLICSHOPPING_ProductsAdmin->getDirectoryProducts()); ?><span>
                </div>
                <div class="col-md-12">
                  <span
                    class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_new_folder'); ?></span>
                  <span
                    class="col-sm-6"><?php echo HTML::inputField('new_directory_products_image', '', 'class="form-control-sm"'); ?><span>
                </div>
                <div class="col-md-12">
                  <span
                    class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_main_image'); ?></span>
                  <span class="col-sm-6"><?php echo HTML::fileField('products_image_resize', 'id="file"'); ?></span>
                </div>
              </div>
              <div class="col-md-5 float-md-right">
                <div class="col-md-12">
                  <span
                    class="col-sm-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product_images.gif', $CLICSHOPPING_Products->getDef('text_products_image_visuel'), '40', '40'); ?></span>
                  <span
                    class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_visuel'); ?></span>
                </div>
                <div class="col-md-11 adminformAide">
                  <div
                    class="col-sm-12 text-md-center"><?php echo $CLICSHOPPING_ProductsAdmin->getInfoImage($pInfo->products_image, $CLICSHOPPING_Products->getDef('text_products_image_vignette'), '150', '150') . HTML::hiddenField('products_image', $pInfo->products_image); ?></div>
                  <div
                    class="col-sm-12 text-md-center"><?php echo 'URL : ' . CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'sources/images/' . $pInfo->products_image; ?></div>
                  <div class="separator"></div>
                  <div class="col-sm-12 text-md-center">
                    <?php echo $CLICSHOPPING_Products->getDef('text_products_delete_image') . ' ' . HTML::checkboxField('delete_image', 'yes', false); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="separator"></div>
            <div class="separator"></div>
          </div>
          <?php
            // -----------------------------------
            // Gallery
            // -----------------------------------
          ?>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_gallery_image'); ?></div>
          <div class="adminformTitle">
            <div id="tab5ContentRow2">
              <div class="col-md-12">
                <div class="row">
                  <ul id="piList"></ul>
                  <a class="linkHandle" data-action="addNewPiForm"><i
                      class="fas fa-plus"></i>&nbsp;<?php echo $CLICSHOPPING_Products->getDef('text_products_add_large_image'); ?>
                  </a>
                </div>
              </div>
              <div class="separator"></div>

              <script id="templateLargeImage" type="x-tmpl-mustache">
            <li id="piId{{counter}}">
              <div class="piActions float-md-right">
                <a class="linkHandle" data-piid="{{counter}}" data-action="showPiDelConfirm" data-state="active"><i class="fas fa-trash-alt" title="<?php echo $CLICSHOPPING_Products->getDef('image_delete'); ?>"></i></a>
                <a class="sortHandle" data-state="active"><i class="fas fa-arrows-alt-v" title="<?php echo $CLICSHOPPING_Products->getDef('image_move'); ?>"></i></a>
                <a class="linkHandle" data-piid="{{counter}}" data-action="undoDelete" data-state="inactive"><i class="fas fa-undo" title="<?php echo $CLICSHOPPING_Products->getDef('image_undo'); ?>"></i></a>
              </div>
              <strong><?php echo $CLICSHOPPING_Products->getDef('text_products_large_image'); ?></strong><br />
              <?php echo HTML::fileField('{{input_file_name}}'); ?><br />
              {{#image}}<a href="<?php echo $CLICSHOPPING_Template->getDirectoryShopTemplateImages(); ?>{{image}}" target="_blank">{{image}}</a><br /><br />{{/image}}
              <?php echo $CLICSHOPPING_Products->getDef('text_products_large_image_html_content'); ?><br />
              <?php echo HTML::textareaField('{{input_html_content_name}}', '{{html_content}}', '200', '3', null, false); ?>
            </li>

              </script>

              <div class="modal" tabindex="-1" role="dialog" id="piDelConfirm">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                          aria-hidden="true">&times;</span></button>

                      <h4
                        class="modal-title"><?php echo $CLICSHOPPING_Products->getDef('text_products_large_image_delete_title'); ?></h4>
                    </div>

                    <div class="modal-body">
                      <p><?php echo $CLICSHOPPING_Products->getDef('text_products_large_image_confirm_delete'); ?></p>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger"
                              id="piDelConfirmButtonDelete"><?= $CLICSHOPPING_Products->getDef('button_delete'); ?></button>
                      <button type="button" class="btn btn-warning"
                              data-dismiss="modal"><?= $CLICSHOPPING_Products->getDef('button_cancel'); ?></button>
                    </div>
                  </div>
                </div>
              </div>

              <style type="text/css">
                #piList {
                  list-style-type: none;
                  margin: 0;
                  padding: 0;
                }

                #piList li {
                  margin: 15px 0;
                  padding: 10px;
                }
              </style>

              <script>
                  $(function () {
                      var templateLargeImage = $('#templateLargeImage').html();
                      Mustache.parse(templateLargeImage);

                    <?php
                    $pi_array = [];

                    foreach ($pInfo->products_larger_images as $pi) {
                      $pi_array[] = [
                        'counter' => count($pi_array) + 1,
                        'input_file_name' => 'products_image_large_' . $pi['id'],
                        'input_html_content_name' => 'products_image_htmlcontent_' . $pi['id'],
                        'image' => $pi['image'],
                        'html_content' => $pi['htmlcontent']
                      ];
                    }

                    echo '  var piArray = ' . json_encode($pi_array) . ';';
                    ?>

                      $.each(piArray, function (k, v) {
                          $('#piList').append(Mustache.render(templateLargeImage, v));
                      });

                      $('#piList .piActions a[data-state="inactive"]').hide();

                      Sortable.create($('#piList')[0], {
                          handle: '.sortHandle'
                      });

                      $('#tab5 a[data-action="addNewPiForm"]').on('click', function () {
                          var piSize = $('#piList li').length + 1;

                          var data = {
                              counter: piSize,
                              input_file_name: 'products_image_large_new_' + piSize,
                              input_html_content_name: 'products_image_htmlcontent_new_' + piSize
                          };

                          $('#piList').append(Mustache.render(templateLargeImage, data));

                          $('#piId' + piSize + ' .piActions a[data-state="inactive"]').hide();
                      });

                      $('#tab5').on('click', '#piList li a[data-action="showPiDelConfirm"]', function () {
                          $('#piDelConfirm').data('piid', $(this).data('piid'));

                          $('#piDelConfirm').modal('show');
                      });

                      $('#tab5').on('click', '#piList li a[data-action="undoDelete"]', function () {
                          $('#piId' + $(this).data('piid') + ' .piActions a[data-state="inactive"]').hide();
                          $('#piId' + $(this).data('piid') + ' .piActions a[data-state="active"]').show();
                          $('#piId' + $(this).data('piid') + ' :input').prop('disabled', false);
                          $('#piId' + $(this).data('piid')).removeClass('bg-danger').addClass('bg-warning');
                      });

                      $('#piDelConfirmButtonDelete').on('click', function () {
                          $('#piId' + $('#piDelConfirm').data('piid')).removeClass('bg-warning').addClass('bg-danger');
                          $('#piId' + $('#piDelConfirm').data('piid') + ' :input').prop('disabled', true);
                          $('#piId' + $('#piDelConfirm').data('piid') + ' .piActions a[data-state="active"]').hide();
                          $('#piId' + $('#piDelConfirm').data('piid') + ' .piActions a[data-state="inactive"]').show();

                          $('#piDelConfirm').modal('hide');
                      });
                  });
              </script>

              <?php
                // -----------------------------------
                // Manual image
                // -----------------------------------
                if (MANUAL_IMAGE_PRODUCTS_DESCRIPTION == 'true') {
                  ?>
                  <div
                    class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_customize'); ?></div>
                  <div class="adminformTitle">
                    <div class="row" id="tab5ContentRow7">
                      <div class="col-md-6 float-md-left">
                        <div class="col-md-12">
                          <span
                            class="col-sm-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product.gif', $CLICSHOPPING_Products->getDef('text_products_image_vignette'), '40', '40'); ?></span>
                          <span
                            class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_insert_big_image_vignette'); ?></span>
                        </div>
                        <div class="separator"></div>
                        <div class="col-md-12 adminformAide">
                          <div class="col-md-12 text-md-center">
                            <span
                              class="col-sm-12 text-md-center"><?php echo HTMLOverrideAdmin::fileFieldImageCkEditor('products_image', null, '100', '100'); ?></span>
                          </div>
                          <div class="col-md-12">
                            <span
                              class="col-sm-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product_zoom.gif', $CLICSHOPPING_Products->getDef('text_products_image_medium'), '40', '40'); ?></span>
                            <span
                              class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_medium'); ?></span>
                          </div>
                          <div class="col-md-12">
                            <span
                              class="col-sm-6"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product_zoom.gif', $CLICSHOPPING_Products->getDef('text_products_image_zoom'), '40', '40'); ?></span>
                            <span
                              class="col-sm-6"><?php echo $CLICSHOPPING_Products->getDef('text_products_image_zoom'); ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 float-md-left">
                        <div class="col-md-12">
                          <span
                            class="col-sm-12"><?php echo $CLICSHOPPING_ProductsAdmin->getInfoImage($pInfo->products_image, $CLICSHOPPING_Products->getDef('text_products_image_vignette')); ?></span>
                          <div class="col-md-12 text-md-center">
                            <span
                              class="col-sm-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_no_image_visuel_zoom'); ?></span>
                          </div>
                          <div class="col-md-12 text-md-right">
                            <span
                              class="col-sm-12"><?php echo $CLICSHOPPING_Products->getDef('text_products_delete_image') . ' ' . HTML::checkboxField('delete_image', 'yes', false); ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php
                }
              ?>
            </div>
          </div>
          <div class="separator"></div>
          <div>
            <?php echo $CLICSHOPPING_Hooks->output('Products', 'ProductsContentTab5', null, 'display'); ?>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Products->getDef('title_help_image')) . ' ' . $CLICSHOPPING_Products->getDef('title_help_image') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_Products->getDef('title_help_products'); ?></div>
          </div>
        </div>
        <?php
          // ******************************************
          // Tab 6 Meta Datas
          //*******************************************
        ?>
        <!-- decompte caracteres -->
        <script type="text/javascript">
            $(document).ready(function () {
              <?php
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
              ?>
                //default title
                $("#default_title_<?php echo $i?>").charCount({
                    allowed: 70,
                    warning: 20,
                    counterText: ' Max : '
                });

                //default_description
                $("#default_description_<?php echo $i?>").charCount({
                    allowed: 150,
                    warning: 20,
                    counterText: 'Max : '
                });

                //default tag
                $("#default_tag_<?php echo $i?>").charCount({
                    allowed: 70,
                    warning: 20,
                    counterText: ' Max : '
                });
              <?php
              }
              ?>
            });
        </script>
        <div class="tab-pane" id="tab6">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_page_seo'); ?></div>
          <div class="adminformTitle">
            <div class="separator"></div>
            <div class="row" id="tab6ContentRow1">
              <div class="col-md-12">
                <span class="col-sm-3"></span>
                <span class="col-sm-3"><a href="https://www.google.fr/trends"
                                          target="_blank"><?php echo $CLICSHOPPING_Products->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-sm-3"><a href="https://adwords.google.com/select/KeywordToolExternal"
                                          target="_blank"><?php echo $CLICSHOPPING_Products->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>
            <?php
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
                ?>

                <div class="row">
                  <div class="col-md-1">
                    <div class="form-group row">
                      <label for="Code"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_page_title'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_page_title'); ?></label>
                      <div class="col-md-8">
                        <?php echo '&nbsp;' . HTML::inputField('products_head_title_tag[' . $languages[$i]['id'] . ']', SeoAdmin::getProductsSeoTitle($pInfo->products_id, $languages[$i]['id']), 'maxlength="70" size="77" id="default_title_' . $i . '"', false); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_header_description'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_header_description'); ?></label>
                      <div class="col-md-8">
                        <?php echo HTML::textAreaField('products_head_desc_tag[' . $languages[$i]['id'] . ']', SeoAdmin::getProductsSeoDescription($pInfo->products_id, $languages[$i]['id']), '75', '2', 'id="default_description_' . $i . '"'); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_keywords'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_keywords'); ?></label>
                      <div class="col-md-8">
                        <?php echo HTML::textAreaField('products_head_keywords_tag[' . $languages[$i]['id'] . ']', SeoAdmin::getProductsSeoKeywords($pInfo->products_id, $languages[$i]['id']), '75', '5'); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_tag'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_tag'); ?></label>
                      <div class="col-md-8">
                        <?php echo HTML::inputField('products_head_tag[' . $languages[$i]['id'] . ']', SeoAdmin::getProductsSeoTag($pInfo->products_id, $languages[$i]['id']), 'maxlength="50" size="77" id="default_tag_' . $i . '"', false); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
          </div>
          <div>
            <div class="separator"></div>
            <?php echo $CLICSHOPPING_Hooks->output('Products', 'ProductsContentTab6', null, 'display'); ?>
          </div>
        </div>
        <?php
          // ******************************************
          // Tab 9 Options
          //*******************************************
          if (!empty($_GET['pID'])) {

            $Qproducts = $CLICSHOPPING_Products->db->prepare('select p.products_id,
                                                             pd.products_name,
                                                             p.products_model
                                                      from :table_products p,
                                                           :table_products_description pd
                                                      where pd.products_id = p.products_id
                                                      and pd.language_id = :language_id
                                                      and pd.products_id = :products_id
                                                      order by pd.products_name
                                                    ');
            $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qproducts->bindInt(':products_id', $_GET['pID']);
            $Qproducts->execute();


            $Qcategories = $CLICSHOPPING_Products->db->prepare('select c.categories_id,
                                                               cd.categories_name
                                                        from :table_categories c,
                                                             :table_categories_description cd
                                                        where cd.categories_id = c.categories_id
                                                        and cd.language_id = :language_id
                                                        and c.categories_id <> :categories_id
                                                       ');
            $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qcategories->bindInt(':categories_id', $cPath);
            $Qcategories->execute();
          }
        ?>
        <div class="tab-pane" id="tab9">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Products->getDef('text_products_others_options'); ?></div>
          <div class="adminformTitle" id="tab9Content">

            <div class="row" id="tab9ContentRow1">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_file_download_public'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_file_download_public'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::checkboxField('products_download_public', '1', $pInfo->products_download_public); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab9ContentRow2">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Products->getDef('text_products_file_download'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Products->getDef('text_products_file_download'); ?></label>
                  <div class="col-md-5">
                    <?php
                      echo HTML::fileField('products_download_filename', 'id="file"');
                      if (!empty($pInfo->products_download_filename)) {
                        echo HTML::inputField('products_download_filename', $pInfo->products_download_filename, 'id="products_download_filename" disabled="disabled"') . '&nbsp';
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>

          <?php
            // ---------------------------------
            // Product clone to categories
            // ---------------------------------
            if (!empty($_GET['pID']) && $Qcategories->rowCount() > 0) {
          ?>
          <div class="col-md-12 mainTitle">
            <span><?php echo $CLICSHOPPING_Products->getDef('text_products_categories_copy'); ?></span>
          </div>
          <div class="adminformTitle">
            <div class="separator"></div>
            <div class="row">
              <div class="col-md-12">
                <span class="col-sm-1"></span>
                <span class="col-sm-4 float-md-left" style="padding-top:50px;">
              <?php echo $CLICSHOPPING_Products->getDef('clone_products_from'); ?>&nbsp;
              <select name="clone_products_id_from">
                <?php echo '<option name="' . $Qproducts->value('products_name') . '" value="' . $Qproducts->valueInt('products_id') . '">' . $Qproducts->value('products_model') . ' - ' . $Qproducts->value('products_name') . '</option>'; ?>
              </select>
            </span>
                <span class="col-md-2 float-md-left"
                      style="padding-top:50px;"> <?php echo $CLICSHOPPING_Products->getDef('clone_products_to'); ?></span>
                <span class="col-md-4 float-md-left">
              <select name="clone_categories_id_to[]" multiple="multiple" size="10">
<?php
  while ($Qcategories->fetch()) {
    echo '<option name="' . $Qcategories->value('categories_name') . '" value="' . $Qcategories->valueInt('categories_id') . '">' . $Qcategories->value('categories_name') . '</option>';
  }
?>
              </select>
            </span>
              </div>
            </div>
            <?php
              } // end empty
            ?>
            <div class="separator"></div>
            <div class="alert alert-info" role="alert">
              <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Products->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Products->getDef('title_help_general') ?></div>
              <div class="separator"></div>
              <div><?php echo $CLICSHOPPING_Products->getDef('help_general_tab8') . ' ' . $CLICSHOPPING_Products->getDef('help_general_tab8_1') . ' ' . (int)(ini_get('upload_max_filesize')) . ' Mb'; ?></div>
            </div>
          </div>
          <?php echo $CLICSHOPPING_Hooks->output('Products', 'ProductsContentTab9', null, 'display'); ?>
        </div>
        <?php
          //***********************************
          // extension
          //***********************************
          echo $CLICSHOPPING_Hooks->output('Products', 'PageTab', null, 'display');
        ?>
      </div>
    </div>
    </form>
  </div>