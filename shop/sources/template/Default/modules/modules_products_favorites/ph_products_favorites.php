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
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Marketing\Favorites\Classes\Shop\FavoritesClass;

  class ph_products_favorites {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_favorites_title');
      $this->description = CLICSHOPPING::getDef('module_products_favorites_description');

      if (defined('MODULE_PRODUCTS_FAVORITES_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_FAVORITES_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_FAVORITES_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon  = Registry::get('ProductsCommon');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

      if (isset($_GET['Products']) && isset($_GET['Favorites']) ) {
        if (MODULE_PRODUCTS_FAVORITES_MAX_DISPLAY != 0 ) {

          $products_template = MODULE_PRODUCTS_FAVORITES_TEMPLATE;

          $Qlisting = FavoritesClass::getListing();

          $Qlisting->setPageSet(MODULE_PRODUCTS_FAVORITES_MAX_DISPLAY);
          $Qlisting->execute();

          $listingTotalRow = $Qlisting->getPageSetTotalRows();

          $new_prods_content = '  <!-- Product favorites start -->'. "\n";
          $new_prods_content .= '<div class="clearfix"></div>';
          $new_prods_content .= '<div class="contentText">';
          $new_prods_content .= '<div class="ModulesProductsFavoritesContainer">';

          if ($listingTotalRow > 0) {

            $new_prods_content .= '<div class="col-md-5 float-md-right">';
            $new_prods_content .= '<div style="padding-right:2em; padding-top:0.5rem;">';
            $new_prods_content .= '<div class="dropdown">';
            $new_prods_content .= '<div class="btn-group btn-group-sm float-md-right">';
            $new_prods_content .= '<button type="button" class="btn btn-secondary dropdown-toggle"  data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false">';
            $new_prods_content .= CLICSHOPPING::getDef('text_sort_by');
            $new_prods_content .= '</button>';
            $new_prods_content .= '<ul class="dropdown-menu text-md-left" aria-labelledby="dropdownMenu2">';

// number of sort criterias
            $column_list = FavoritesClass::getCountColumnList();

            for ($col = 0, $n = count($column_list); $col < $n; $col++) {
              switch ($column_list[$col]) {
                case 'MODULE_PRODUCTS_FAVORITES_LIST_DATE_ADDED':
                  $lc_text = CLICSHOPPING::getDef('table_heading_date');
                break;
                case 'MODULE_PRODUCTS_FAVORITES_LIST_PRICE':
                  $lc_text = CLICSHOPPING::getDef('table_heading_price');
                break;
                case 'MODULE_PRODUCTS_FAVORITES_LIST_MODEL':
                  $lc_text = CLICSHOPPING::getDef('table_heading_model');
                break;
                case 'MODULE_PRODUCTS_FAVORITES_LIST_QUANTITY':
                  $lc_text = CLICSHOPPING::getDef('table_heading_quantity');
                break;
                case 'MODULE_PRODUCTS_FAVORITES_LIST_WEIGHT':
                  $lc_text = CLICSHOPPING::getDef('table_heading_weight');
                break;
              }

              $lc_text = $CLICSHOPPING_ProductsCommon->createSortHeading(HTML::sanitize($_GET['sort'] ?? '1a'), $col+1, $lc_text);

              $new_prods_content .= '<li>' . $lc_text . '</li>';
            }

            $new_prods_content .= '</ul>';
            $new_prods_content .= '</div>';
            $new_prods_content .= '</div>';

            $new_prods_content .= '</div>';
            $new_prods_content .= '</div>';

            $new_prods_content .= '<div class="clearfix"></div>';
          }

          if (($listingTotalRow > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
            if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
              $new_prods_content .= '<div class="clearfix"></div>';
              $new_prods_content .= '<div>';
              $new_prods_content .= '<div class="col-md-6 pagenumber hidden-xs">';
              $new_prods_content .=  $Qlisting->getPageSetLabel(CLICSHOPPING::getDef('text_display_number_of_items'));
              $new_prods_content .= '</div>';
              $new_prods_content .= '<div class="col-md-6 float-md-right">';
              $new_prods_content .= '<div class="float-md-right pagenav">'.  $Qlisting->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y')), 'Shop') .'</div>';
              $new_prods_content .= '<div class="text-md-right">' . CLICSHOPPING::getDef('text_result_page') .'</div>';
              $new_prods_content .= '</div>';
              $new_prods_content .= '</div>';
              $new_prods_content .= '<div style="padding-top:10px;"></div>';
              $new_prods_content .= '<div class="clearfix"></div>';
            }
          }

          $new_prods_content .= '<div class="separator"></div>';
          $new_prods_content .= '</div>'. "\n";
          $new_prods_content .= '<div class="boxContentsModulesProductsFavorites">';

          if ($listingTotalRow > 0) {

            $new_prods_content .= '<div class="d-flex flex-wrap">';

// display number of short description
            $products_short_description_number = (int)MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION;
// delete words
            $delete_word = (int)MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION_DELETE_WORLDS;
// nbr of column to display  boostrap
            $bootstrap_column = (int)MODULE_PRODUCTS_FAVORITES_COLUMNS;
// initialisation des boutons
            $size_button = $CLICSHOPPING_ProductsCommon->getSizeButton('md');

// Template define
            $filename= '';
            $filename = $CLICSHOPPING_Template-> getTemplateModulesFilename($this->group .'/template_html/' . MODULE_PRODUCTS_FAVORITES_TEMPLATE);

            while($Qlisting->fetch()) {
              $products_id = $Qlisting->valueInt('products_id');
              $_POST['products_id'] = $products_id;

              $in_stock = $Qlisting->valueInt('in_stock');

              $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($CLICSHOPPING_ProductsCommon->getID());

//product name
              $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
//Stock (good, alert, out of stock).
              $products_stock = $CLICSHOPPING_ProductsFunctionTemplate->getStock(MODULE_PRODUCTS_FAVORITES_DISPLAY_STOCK, $products_id);
//Flash discount
              $products_flash_discount = $CLICSHOPPING_ProductsFunctionTemplate->getFlashDiscount($products_id, '<br />');
// Minimum quantity to take an order
              $min_order_quantity_products_display = $CLICSHOPPING_ProductsFunctionTemplate->getMinOrderQuantityProductDisplay($products_id);
// display a message in public function the customer group applied - before submit button
              $submit_button_view = $CLICSHOPPING_ProductsFunctionTemplate->getButtonView($products_id);
// button buy
              $buy_button = HTML::button(CLICSHOPPING::getDef('button_buy_now'), null, null, 'primary', null, 'sm');
              $CLICSHOPPING_ProductsCommon->getBuyButton($buy_button);
// Display an input allowing for the customer to insert a quantity
              $input_quantity = $CLICSHOPPING_ProductsFunctionTemplate->getDisplayInputQuantity(MODULE_PRODUCTS_FAVORITES_DELETE_BUY_BUTTON, $products_id);
// display the differents prices before button
              $product_price = $CLICSHOPPING_ProductsCommon->getCustomersPrice($products_id);
//Short description
              $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription($products_id, $delete_word, $products_short_description_number);

// **************************
// display the differents buttons before minorder qty
// **************************
              $submit_button = '';
              $form = '';
              $endform = '';

              if (MODULE_PRODUCTS_FAVORITES_DELETE_BUY_BUTTON == 'False') {
                if ($CLICSHOPPING_ProductsCommon->getProductsMinimumQuantity($products_id) != 0 && $CLICSHOPPING_ProductsCommon->getProductsQuantity($products_id) != 0) {
                  if ($CLICSHOPPING_ProductsAttributes->getHasProductAttributes($products_id) === false) {
                    $form =  HTML::form('cart_quantity', CLICSHOPPING::link(null, 'Cart&Add' ),'post','class="justify-content-center"', ['tokenize' => true]). "\n";
                    $form .= HTML::hiddenField('products_id', $products_id);
                    if (isset($_GET['Favorites'])) $form .= HTML::hiddenField('url', 'Products&Favorites');
                    $endform = '</form>';
                    $submit_button = $CLICSHOPPING_ProductsCommon->getProductsBuyButton($products_id);
                  }
                }
              }

// Quantity type
              $products_quantity_unit = $CLICSHOPPING_ProductsFunctionTemplate->getProductQuantityUnitType($products_id);


// **************************************************
// Button Free - Must be above getProductsExhausted
// **************************************************
              if ($CLICSHOPPING_ProductsCommon->getProductsOrdersView($products_id) != 1 && NOT_DISPLAY_PRICE_ZERO == 'false') {
                $submit_button = HTML::button(CLICSHOPPING::getDef('text_products_free'), '', $products_name_url, 'danger');
                $min_quantity = 0;
                $input_quantity ='';
                $min_order_quantity_products_display = '';
              }

// **************************
// Display an information if the stock is exhausted for all groups
// **************************
              if (!empty($CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id))) {
                $submit_button = $CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id);
                $form = '';
                $endform = '';
                $min_quantity = 0;
                $input_quantity = '';
                $min_order_quantity_products_display = '';
              }

// See the button more view details
               $button_small_view_details = $CLICSHOPPING_ProductsFunctionTemplate->getButtonViewDetails(MODULE_PRODUCTS_FAVORITES_DELETE_BUY_BUTTON, $products_id);
// Display the image
              $products_image = $CLICSHOPPING_ProductsFunctionTemplate->getImage(MODULE_PRODUCTS_FAVORITES_IMAGE_MEDIUM, $products_id);
// Ticker Image
              $products_image .= $CLICSHOPPING_ProductsFunctionTemplate->getTicker(MODULE_PRODUCTS_FAVORITES_TICKER, $products_id, 'ModulesProductsFavoritesBootstrapTickerSpecial', 'ModulesProductsFavoritesBootstrapTickerFavorite', 'ModulesProductsFavoritesBootstrapTickerFeatured', 'ModulesProductsFavoritesBootstrapTickerNew');

              $ticker = $CLICSHOPPING_ProductsFunctionTemplate->getTickerPourcentage(MODULE_PRODUCTS_FAVORITES_POURCENTAGE_TICKER, $products_id, 'ModulesProductsFavoritesBootstrapTickerPourcentage');

//******************************************************************************************************************
//            Options -- activate and insert code in template and css
//******************************************************************************************************************

// products model
              $products_model = $CLICSHOPPING_ProductsFunctionTemplate->getProductsModel($products_id);
// manufacturer
                $products_manufacturers = $CLICSHOPPING_ProductsFunctionTemplate->getProductsManufacturer($products_id);
// display the price by kilo
                $product_price_kilo = $CLICSHOPPING_ProductsFunctionTemplate->getProductsPriceByWeight($products_id);
// display date available
                $products_date_available =  $CLICSHOPPING_ProductsFunctionTemplate->getProductsDateAvailable($products_id);
// display products only shop
              $products_only_shop = $CLICSHOPPING_ProductsFunctionTemplate->getProductsOnlyTheShop($products_id);
// display products only shop
              $products_only_web = $CLICSHOPPING_ProductsFunctionTemplate->getProductsOnlyOnTheWebSite($products_id);
// display products packaging
                $products_packaging = $CLICSHOPPING_ProductsFunctionTemplate->getProductsPackaging($products_id);
// display shipping delay
                $products_shipping_delay =  $CLICSHOPPING_ProductsFunctionTemplate->getProductsShippingDelay($products_id);
// display products tag
                $tag = $CLICSHOPPING_ProductsFunctionTemplate->getProductsHeadTag($products_id);

                $products_tag = '';
                if (!is_null($tag)) {
                  foreach ($tag as $value) {
                    $products_tag .= '#<span class="productTag">' . HTML::link(CLICSHOPPING::link(null, 'Search&keywords='. HTML::outputProtected(utf8_decode($value) .'&search_in_description=1&categories_id=&inc_subcat=1'), 'rel="nofollow"'), $value) . '</span> ';
                  }
                }
// display products volume
            $products_volume = $CLICSHOPPING_ProductsFunctionTemplate->getProductsVolume($products_id);
// display products weight
            $products_weight = $CLICSHOPPING_ProductsFunctionTemplate->getProductsWeight($products_id);

//******************************************************************************************************************
//            End Options -- activate and insert code in template and css
//******************************************************************************************************************

// *************************
//      Template call
// **************************

              if (is_file($filename)) {
                ob_start();
                require($filename);
                $new_prods_content .= ob_get_clean();
              } else {
                echo CLICSHOPPING::getDef('template_does_not_exist') . '<br /> ' . $filename;
                exit;
              }
            } //while

            $new_prods_content .= '</div>';  // flex
          } else {
            $new_prods_content .= '<div class="text-md-center alert alert-info">'. CLICSHOPPING::getDef('text_no_products') .'</div>';
          }

          if (($listingTotalRow > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
            if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
              $new_prods_content .= '<div class="clearfix"></div>';
              $new_prods_content .= '<div style="padding-top:10px;"></div>';
              $new_prods_content .= '<div>';
              $new_prods_content .= '<div class="col-md-6 pagenumber hidden-xs">';
              $new_prods_content .=  $Qlisting->getPageSetLabel(CLICSHOPPING::getDef('text_display_number_of_items'));
              $new_prods_content .= '</div>';
              $new_prods_content .= '<div class="col-md-6 float-md-right">';
              $new_prods_content .= '<span class="float-md-right pagenav">'.  $Qlisting->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y')), 'Shop') .'</span>';
              $new_prods_content .= '<span class="text-md-right">' . CLICSHOPPING::getDef('text_result_page') .'</span>';
              $new_prods_content .= '</div>';
              $new_prods_content .= '</div>';
              $new_prods_content .= '<div class="clearfix"></div>';
            }
          }

          $new_prods_content .= '</div>';
        } else {
          $new_prods_content .= '<div class="text-md-center alert alert-info">'. CLICSHOPPING::getDef('text_no_products') .'</div>';
        } // max display product

        $new_prods_content .= '</div>' . "\n";

        $new_prods_content .= '<!--  Products favorites End -->' . "\n";

        $CLICSHOPPING_Template->addBlock($new_prods_content, $this->group);

      } // php_self
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_FAVORITES_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select your template ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_TEMPLATE',
          'configuration_value' => 'template_bootstrap_column_5.php',
          'configuration_description' => 'Select your template you want to display',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the number of product do you want to display ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_MAX_DISPLAY',
          'configuration_value' => '6',
          'configuration_description' => 'Please, indicate the number of products do your want to display.',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the number of column that you want to display ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Choose a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display a short description ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Please indicate a number of your short description',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want remove words of your short description ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION_DELETE_WORLDS',
          'configuration_value' => '0',
          'configuration_description' => 'Please indicate a number',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display a message News / Specials / Favorites / Featured ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Display a message News / Specials / Favorites / Featured',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display the discount pourcentage (specials) ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_POURCENTAGE_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Display the discount pourcentage (specials)',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please do you want to display the stock ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_DISPLAY_STOCK',
          'configuration_value' => 'none',
          'configuration_description' => 'Est-que vous souhaitez afficher une image indiquant une information sur le stock du produit (En stock, pratiquement épuisé, hors stock) ?',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'image\', \'number\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a arrival date sort order',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_LIST_DATE_ADDED',
          'configuration_value' => '1',
          'configuration_description' => 'This option allow to choose an order to display the product. Lowest is displayed in first; 0 for nothing',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon les tarifs',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_LIST_PRICE',
          'configuration_value' => '0',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage<br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a model sort order',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_LIST_MODEL',
          'configuration_value' => '0',
          'configuration_description' => 'This option allow to choose an order to display the product. Lowest is displayed in first; 0 for nothing',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a quantity sort order',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_LIST_QUANTITY',
          'configuration_value' => '0',
          'configuration_description' => 'This option allow to choose an order to display the product. Lowest is displayed in first; 0 for nothing',
          'configuration_group_id' => '6',
          'sort_order' => '8',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a weight sort order',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_LIST_WEIGHT',
          'configuration_value' => '0',
          'configuration_description' => 'This option allow to choose an order to display the product. Lowest is displayed in first; 0 for nothing',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose the image size',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_IMAGE_MEDIUM',
          'configuration_value' => 'Small',
          'configuration_description' => 'Quelle taille d\'image souhaitez-vous afficher ?<br /><br /><i>(Valeur Small = Petite - Valeur Medium = Moyenne)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Small\', \'Medium\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want remove the details button ?',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_DELETE_BUY_BUTTON',
          'configuration_value' => 'False',
          'configuration_description' => 'Remove the button details',
          'configuration_group_id' => '6',
          'sort_order' => '11',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_FAVORITES_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '12',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_FAVORITES_STATUS',
        'MODULE_PRODUCTS_FAVORITES_TEMPLATE',
        'MODULE_PRODUCTS_FAVORITES_MAX_DISPLAY',
        'MODULE_PRODUCTS_FAVORITES_COLUMNS',
        'MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION',
        'MODULE_PRODUCTS_FAVORITES_SHORT_DESCRIPTION_DELETE_WORLDS',
        'MODULE_PRODUCTS_FAVORITES_TICKER',
        'MODULE_PRODUCTS_FAVORITES_POURCENTAGE_TICKER',
        'MODULE_PRODUCTS_FAVORITES_DISPLAY_STOCK',
        'MODULE_PRODUCTS_FAVORITES_LIST_DATE_ADDED',
        'MODULE_PRODUCTS_FAVORITES_LIST_PRICE',
        'MODULE_PRODUCTS_FAVORITES_LIST_MODEL',
        'MODULE_PRODUCTS_FAVORITES_LIST_QUANTITY',
        'MODULE_PRODUCTS_FAVORITES_LIST_WEIGHT',
        'MODULE_PRODUCTS_FAVORITES_IMAGE_MEDIUM',
        'MODULE_PRODUCTS_FAVORITES_DELETE_BUY_BUTTON',
        'MODULE_PRODUCTS_FAVORITES_SORT_ORDER'
      );
    }
  }
