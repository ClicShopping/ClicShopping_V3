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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Marketing\Featured\Classes\Shop\FeaturedClass;

  class pf_products_featured {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_featured');
      $this->description = CLICSHOPPING::getDef('module_products_featured_description');

      if (defined('MODULE_PRODUCTS_FEATURED_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_FEATURED_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_FEATURED_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon  = Registry::get('ProductsCommon');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

      if (isset($_GET['Products']) && isset($_GET['Featured']) ) {
        if (MODULE_PRODUCTS_FEATURED_MAX_DISPLAY != 0 ) {

          $products_template = MODULE_PRODUCTS_FEATURED_TEMPLATE;

          $Qlisting = FeaturedClass::getListing();

          $Qlisting->setPageSet((int)MODULE_PRODUCTS_FEATURED_MAX_DISPLAY);
          $Qlisting->execute();

          $listingTotalRow = $Qlisting->getPageSetTotalRows();

          $new_prods_content = '<!-- Products featured start -->' . "\n";
          $new_prods_content .= '<div class="clearfix"></div>';
          $new_prods_content .= '<div class="contentText">';
          $new_prods_content .= '<div class="ModulesProductsFeaturedContainer">';

          if ($listingTotalRow > 0) {

            $new_prods_content .= '<div class="col-md-5 float-md-right">';
            $new_prods_content .= '<div style="padding-right:2em; padding-top:0.5rem;">';
            $new_prods_content .= '<div class="dropdown">';
            $new_prods_content .= '<div class="btn-group btn-group-sm float-md-right">';
            $new_prods_content .= '<button type="button" class="btn btn-secondary dropdown-toggle"  data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false">';
            $new_prods_content .= CLICSHOPPING::getDef('text_sort_by');
            $new_prods_content .= '</button>';
            $new_prods_content .= '<ul class="dropdown-menu text-md-left"  aria-labelledby="dropdownMenu2">';

// number of sort criterias
            $column_list = FeaturedClass::getCountColumnList();

            for ($col = 0, $n = count($column_list); $col < $n; $col++) {
              switch ($column_list[$col]) {
                case 'MODULE_PRODUCTS_FEATURED_LIST_DATE_ADDED':
                  $lc_text = CLICSHOPPING::getDef('table_heading_date');
                break;
                case 'MODULE_PRODUCTS_FEATURED_LIST_PRICE':
                  $lc_text = CLICSHOPPING::getDef('table_heading_price');
                break;
                case 'MODULE_PRODUCTS_FEATURED_LIST_MODEL':
                  $lc_text = CLICSHOPPING::getDef('table_heading_model');
                break;
                case 'MODULE_PRODUCTS_FEATURED_LIST_QUANTITY':
                  $lc_text = CLICSHOPPING::getDef('table_heading_quantity');
                break;
                case 'MODULE_PRODUCTS_FEATURED_LIST_WEIGHT':
                  $lc_text = CLICSHOPPING::getDef('table_heading_weight');
                break;
              }

              $lc_text = $CLICSHOPPING_ProductsCommon->createSortHeading($_GET['sort'], $col+1, $lc_text);

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
          $new_prods_content .= '<div class="boxContentsModulesProductsFeatured">';

          if ($listingTotalRow > 0) {

            $new_prods_content .= '<div class="d-flex flex-wrap">';

// display number of short description
            $products_short_description_number = (int)MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION;
// delete words
            $delete_word = (int)MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION_DELETE_WORLDS;
// nbr of column to display  boostrap
            $bootstrap_column = (int)MODULE_PRODUCTS_FEATURED_COLUMNS;
// initialisation des boutons
            $size_button = $CLICSHOPPING_ProductsCommon->getSizeButton('md');

// Template define
            $filename= '';
            $filename = $CLICSHOPPING_Template-> getTemplateModulesFilename($this->group .'/template_html/' . MODULE_PRODUCTS_FEATURED_TEMPLATE);

            while($Qlisting->fetch()) {
              $products_id = $Qlisting->valueInt('products_id');
              $_POST['products_id'] = $products_id;

              $in_stock = $Qlisting->valueInt('in_stock');

              $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($products_id);

//product name
              $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
//Short description
              $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription(null, $delete_word, $products_short_description_number);
//Stock (good, alert, out of stock).
              $products_stock = $CLICSHOPPING_ProductsFunctionTemplate->getStock(MODULE_PRODUCTS_FEATURED_DISPLAY_STOCK, $products_id);
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
              $input_quantity = $CLICSHOPPING_ProductsFunctionTemplate->getDisplayInputQuantity(MODULE_PRODUCTS_FEATURED_DELETE_BUY_BUTTON, $products_id);
// display the differents prices before button
              $product_price = $CLICSHOPPING_ProductsCommon->getCustomersPrice($products_id);
//Short description
              $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription($products_id, $delete_word, $products_short_description_number);

// **************************
// display the differents buttons before minorder qty
// **************************
              IF (MODULE_PRODUCTS_FEATURED_DELETE_BUY_BUTTON == 'False') {
                if ($CLICSHOPPING_ProductsCommon->getProductsMinimumQuantity($products_id) != 0 && $CLICSHOPPING_ProductsCommon->getProductsQuantity($products_id) != 0) {
                  $submit_button = '';

                  if ($CLICSHOPPING_ProductsAttributes->getHasProductAttributes($products_id) === false) {
                    $form =  HTML::form('cart_quantity', CLICSHOPPING::link(null, 'Cart&Add' ),'post','class="form-inline justify-content-center"', ['tokenize' => true]). "\n";
                    $form .= HTML::hiddenField('products_id', $products_id);
                    if (isset($_GET['Featured'])) $form .= HTML::hiddenField('url', 'Products&Featured');
                    $endform = '</form>';
                    $submit_button = $CLICSHOPPING_ProductsCommon->getProductsBuyButton($products_id);
                  }
                }
              } else {
                $submit_button = '';
              }

// Quantity type
              $products_quantity_unit = $CLICSHOPPING_ProductsFunctionTemplate->getProductQuantityUnitType($products_id);


// **************************************************
// Button Free - Must be above getProductsExhausted
// **************************************************
              if ($CLICSHOPPING_ProductsCommon->getProductsOrdersView($products_id) != 1 && NOT_DISPLAY_PRICE_ZERO == 'false') {
                $submit_button = HTML::button(CLICSHOPPING::getDef('text_products_free'), '', $products_name_url, 'danger');
                $min_quantity = 0;
                $form = '';
                $endform = '';
                $input_quantity ='';
                $min_order_quantity_products_display = '';
              }

// **************************
// Display an information if the stock is exhausted for all groups
// **************************
              if (!empty($CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id))) {
                $submit_button = $CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id);
                $min_quantity = 0;
                $input_quantity = '';
                $min_order_quantity_products_display = '';
              }

// See the button more view details
               $button_small_view_details = $CLICSHOPPING_ProductsFunctionTemplate->getButtonViewDetails(MODULE_PRODUCTS_FEATURED_DELETE_BUY_BUTTON, $products_id);
// Display the image
              $products_image = $CLICSHOPPING_ProductsFunctionTemplate->getImage(MODULE_PRODUCTS_FEATURED_IMAGE_MEDIUM, $products_id);
// Ticker Image
              $products_image .= $CLICSHOPPING_ProductsFunctionTemplate->getTicker(MODULE_PRODUCTS_FEATURED_TICKER, $products_id, 'ModulesProductsFeaturedBootstrapTickerSpecial', 'ModulesProductsFeaturedBootstrapTickerFavorite', 'ModulesProductsFeaturedBootstrapTickerFeatured', 'ModulesProductsFeaturedBootstrapTickerNew');

              $ticker = $CLICSHOPPING_ProductsFunctionTemplate->getTickerPourcentage(MODULE_PRODUCTS_FEATURED_POURCENTAGE_TICKER, $products_id, 'ModulesProductsFeaturedBootstrapTickerPourcentage');

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

        $new_prods_content .= '<!--  Products featured End -->' . "\n";

        $CLICSHOPPING_Template->addBlock($new_prods_content, $this->group);

      } // php_self
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_FEATURED_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Quel type de template souhaitez-vous voir affiché dans les produits ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_TEMPLATE',
          'configuration_value' => 'template_bootstrap_column_5.php',
          'configuration_description' => 'Veuillez indiquer le type de template que vous souhaitez voir affiché concernant les produits.<br /><br /><b>Note</b><br /> - Si vous avez opté pour une configuration en ligne, veuillez choisir un type de nom de template comme <u>template_line</u>.<br /><br /> - Si vous avez opté pour un affichage en colonne, veuillez choisir un type de nom de template comme <u>template_column</u> puis veuillez configurer le nombre de colonnes',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indiquer le nombre de produits à afficher',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_MAX_DISPLAY',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre maximum de produits à afficher.',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de colonnes de produit que vous souhaitez voir affiché ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre de colonnes de produit à afficher par ligne.<br /><br />Note:<br /><br />- Entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher une description courte des produits ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer la longueur de cette description.<br /><br /><i>- 0 pour aucune description<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous supprimer une certaine longeur de texte descriptif ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION_DELETE_WORLDS',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer le nombre de mots à supprimer. Ce système est utile avec le module des onglets<br><br><i>- 0 pour aucune suppression<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher un message Nouveauté / Promotion /  Sélection / Coups de coeur?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher un message Nouveauté / Promotion / Sélection / Coups de coeur en surimpression sur l\'image du produit ?<br /><br />la durée est paramétrable dans le Menu configuration / ma boutique / Valeurs minimales / maximales<br><br><i>(Valeur true = Oui - Valeur false = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher le pourcentage de réduction du prix (promotion) ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_POURCENTAGE_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher le pourcentage de réduction du prix<br><i>(Valeur true = Oui - Valeur false = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher une image concernant l\'état du stock du produit ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_DISPLAY_STOCK',
          'configuration_value' => 'none',
          'configuration_description' => 'Est-que vous souhaitez afficher une image indiquant une information sur le stock du produit (En stock, pratiquement épuisé, hors stock) ?',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'image\', \'number\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon la date d\'arrivée',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_LIST_DATE_ADDED',
          'configuration_value' => '1',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage</br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon les tarifs',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_LIST_PRICE',
          'configuration_value' => '0',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage</br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon les références',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_LIST_MODEL',
          'configuration_value' => '0',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage</br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon les quantités en stock',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_LIST_QUANTITY',
          'configuration_value' => '0',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage</br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '8',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer une ordre tri selon le poids',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_LIST_WEIGHT',
          'configuration_value' => '0',
          'configuration_description' => 'Cette option permet au client de choisir un ordre d\'affichage de ses produits.<br/ ><br /><strong>Note :</strong><br /><br />- 0 pour aucun Affichage</br />- Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez choisir une taille d\'affichage de l\'image',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_IMAGE_MEDIUM',
          'configuration_value' => 'Small',
          'configuration_description' => 'Quelle taille d\'image souhaitez-vous afficher ?<br /><br /><i>(Valeur Small = Petite - Valeur Medium = Moyenne)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Small\', \'Medium\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous supprimer le bouton Acheter et Détail ?',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_DELETE_BUY_BUTTON',
          'configuration_value' => 'False',
          'configuration_description' => 'Supprimer le bouton acheter et détail',
          'configuration_group_id' => '6',
          'sort_order' => '11',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_FEATURED_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
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
        'MODULE_PRODUCTS_FEATURED_STATUS',
        'MODULE_PRODUCTS_FEATURED_TEMPLATE',
        'MODULE_PRODUCTS_FEATURED_MAX_DISPLAY',
        'MODULE_PRODUCTS_FEATURED_COLUMNS',
        'MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION',
        'MODULE_PRODUCTS_FEATURED_SHORT_DESCRIPTION_DELETE_WORLDS',
        'MODULE_PRODUCTS_FEATURED_TICKER',
        'MODULE_PRODUCTS_FEATURED_POURCENTAGE_TICKER',
        'MODULE_PRODUCTS_FEATURED_DISPLAY_STOCK',
        'MODULE_PRODUCTS_FEATURED_LIST_DATE_ADDED',
        'MODULE_PRODUCTS_FEATURED_LIST_PRICE',
        'MODULE_PRODUCTS_FEATURED_LIST_MODEL',
        'MODULE_PRODUCTS_FEATURED_LIST_QUANTITY',
        'MODULE_PRODUCTS_FEATURED_LIST_WEIGHT',
        'MODULE_PRODUCTS_FEATURED_IMAGE_MEDIUM',
        'MODULE_PRODUCTS_FEATURED_DELETE_BUY_BUTTON',
        'MODULE_PRODUCTS_FEATURED_SORT_ORDER'
      );
    }
  }
