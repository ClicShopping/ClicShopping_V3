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

  class pi_products_info_also_purchased {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_also_purchased_title');
      $this->description = CLICSHOPPING::getDef('module_products_info_also_purchased_description');

      if (defined('MODULE_PRODUCTS_INFO_ALSO_PURCHASED_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_ALSO_PURCHASED_STATUS == 'True');
      }
    }

    public function execute() {
      if (isset($_GET['Description']) && isset($_GET['Products']) ) {
        $CLICSHOPPING_Customer = Registry::get('Customer');
        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
        $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

         if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {

           $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                   p.products_image,
                                                   p.products_image_medium,
                                                   p.products_quantity as in_stock
                                            from :table_orders_products opa,
                                                 :table_orders_products opb,
                                                 :table_orders o,
                                                 :table_products p
                                            where opa.products_id = :products_id
                                            and opa.orders_id = opb.orders_id
                                            and opb.products_id <> :products_id
                                            and opb.products_id = p.products_id
                                            and opb.orders_id = o.orders_id
                                            and p.products_status = 1
                                            and p.products_archive = 0
                                            and p.products_view = 1
                                            group by p.products_id
                                            order by o.date_purchased desc
                                            limit :limit
                                          ');

            $Qproducts->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getID());
            $Qproducts->bindInt(':limit', MODULE_PRODUCTS_INFO_ALSO_PURCHASED_MAX_DISPLAY);
            $Qproducts->execute();

          } else { // mode B2B

           $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                   p.products_image,
                                                   p.products_image_medium,
                                                   p.products_quantity as in_stock
                                          from :table_orders_products opa,
                                               :table_orders_products opb,
                                               :table_orders o,
                                               :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                          where opa.products_id = :products_id
                                          and opa.orders_id = opb.orders_id
                                          and opb.products_id <> :products_id
                                          and p.products_status = 1
                                          and p.products_archive = 0
                                          and opb.products_id = p.products_id
                                          and opb.orders_id = o.orders_id
                                          and g.customers_group_id = :customers_group_id
                                          and g.products_group_view = 1
                                          and g.price_group_view = 1
                                          group by p.products_id
                                          order by o.date_purchased desc
                                          limit :limit
                                          ');

           $Qproducts->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getID());
           $Qproducts->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
           $Qproducts->bindInt(':limit', MODULE_PRODUCTS_INFO_CROSS_SELL_MAX_DISPLAY);
           $Qproducts->execute();
        }

          if ($Qproducts->rowCount() > 0) {
// display number of short description
            $products_short_description_number = (int)MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION;
// delete words
            $delete_word = (int)MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION_DELETE_WORLDS;
// nbr of column to display  boostrap
          $bootstrap_column = (int)MODULE_PRODUCTS_INFO_ALSO_PURCHASED_COLUMNS;
// initialisation des boutons
            $size_button = $CLICSHOPPING_ProductsCommon->getSizeButton('xs');

// Template define
          $filename= '';
          $filename = $CLICSHOPPING_Template-> getTemplateModulesFilename($this->group .'/template_html/' . MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TEMPLATE);

          $new_prods_content = '<!-- Start products_also_purchased -->' . "\n";

          $new_prods_content .= '<div class="clearfix"></div>';
          $new_prods_content .= '<div class="separator"></div>';
          $new_prods_content .= '<div class="contentContainer">';
          $new_prods_content .= '<div class="contentText">';

          if (MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TITLE == 'True') {
            $new_prods_content .= '<div>';
            $new_prods_content .= '<div class="page-header ModuleProductsInfoAlsoPurchasedHeading"><span class="ModuleProductsInfoAlsoPurchasedHeading"><h2>' . sprintf(CLICSHOPPING::getDef('module_products_info_also_purchased_name'), strftime('%B')) . '</h2></span></div>';
            $new_prods_content .= '</div>';
          }

          $new_prods_content .= '<div class="ModuleProductsInfoAlsoPurchasedContainer">';
          $new_prods_content .= '<div class="d-flex flex-wrap">';

            while ($Qproducts->fetch() ) {

              $products_id = $Qproducts->valueInt('products_related_id_slave');
              $in_stock = $Qproducts->valueInt('in_stock');

              $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($products_id);
//product name
              $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
//Short description
              $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription(null, $delete_word, $products_short_description_number);
//Stock (good, alert, out of stock).
              $products_stock = $CLICSHOPPING_ProductsFunctionTemplate->getStock(MODULE_PRODUCTS_INFO_ALSO_PURCHASED_DISPLAY_STOCK, $products_id);
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

// **************************
              $input_quantity = '';

              if ($CLICSHOPPING_ProductsCommon->getProductsAllowingToInsertQuantity($products_id) !='' ) {
                  if ($CLICSHOPPING_ProductsAttributes->getHasProductAttributes($products_id) === false) {
                    $input_quantity = CLICSHOPPING::getDef('text_customer_quantity')  . ' ' . $CLICSHOPPING_ProductsCommon->getProductsAllowingToInsertQuantity();
                }
              }

// **************************
// display the differents buttons before minorder qty
// **************************
              if ($CLICSHOPPING_ProductsCommon->getProductsMinimumQuantity($products_id) != 0 && $CLICSHOPPING_ProductsCommon->getProductsQuantity($products_id) != 0) {
                $submit_button = '';

                if ($CLICSHOPPING_ProductsAttributes->getHasProductAttributes($products_id) === false) {
                  $form =  HTML::form('cart_quantity', CLICSHOPPING::link(null, 'Cart&Add' ),'post','class="justify-content-center"', ['tokenize' => true]). "\n";
                  $form .= HTML::hiddenField('products_id', $products_id);
                  if (isset($_GET['Description'])) $form .= HTML::hiddenField('url', 'Products&Description');
                  $endform = '</form>';
                  $submit_button = $CLICSHOPPING_ProductsCommon->getProductsBuyButton($products_id);
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
                $button_small_view_details = HTML::button(CLICSHOPPING::getDef('button_details'), null, CLICSHOPPING::link($products_name_url, 'info', null, 'sm'));
// 10 - Display the image
                $products_image = HTML::link($products_name_url, HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $Qproducts->value('products_image'), HTML::outputProtected($Qproducts->value('products_name')), MODULE_PRODUCTS_INFO_CROSS_SELL_IMAGE_WIDTH, MODULE_PRODUCTS_INFO_CROSS_SELL_IMAGE_HEIGHT, null, true));
//Ticker Image
              $products_image .= $CLICSHOPPING_ProductsFunctionTemplate->getTicker(MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TICKER, $products_id, 'ModulesProductsInfoBootstrapTickerSpecial', 'ModulesProductsInfoBootstrapTickerFavorite', 'ModulesProductsInfoBootstrapTickerFeatured', 'ModulesProductsInfoBootstrapTickerNew');

              $ticker = $CLICSHOPPING_ProductsFunctionTemplate->getTickerPourcentage(MODULE_PRODUCTS_INFO_ALSO_PURCHASED_POURCENTAGE_TICKER, $products_id, 'ModulesProductsInfoBootstrapTickerPourcentage');

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

            $new_prods_content .= '</div>';
            $new_prods_content .= '</div>';
            $new_prods_content .= '</div>';
            $new_prods_content .= '</div>' . "\n";

            $new_prods_content .= '<!-- end products also purchased -->' . "\n";

            $CLICSHOPPING_Template->addBlock($new_prods_content, $this->group);

          }// num row
        }// isset id
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_ALSO_PURCHASED_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Quel type de template souhaitez-vous voir affiché ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TEMPLATE',
          'configuration_value' => 'template_bootstrap_column_5.php',
          'configuration_description' => 'Veuillez indiquer le type de template que vous souhaitez voir affiché.<br /><br /><b>Note :</b><br /> - Si vous avez opté pour une configuration en ligne, veuillez choisir un type de nom de template comme <u>template_line</u>.<br /><br /> - Si vous avez opté pour un affichage en colonne, veuillez choisir un type de nom de template comme <u>template_column</u> puis veuillez configurer le nombre de colonnes.<br />',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher le titre ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TITLE',
          'configuration_value' => 'True',
          'configuration_description' => 'Affiche le titre du module dans le catalogue<br /><br /><i>(Valeur True = Oui - Valeur False = Non</i>)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indiquer le nombre de nouveaux produits à afficher sur la page d\'accueil',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_MAX_DISPLAY',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre maximum de nouveaux produits à afficher.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de colonnes de produit que vous souhaitez voir affiché  ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre de colonnes de produit à afficher par ligne.<br /><br />Note:<br /><br />- Entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous afficher une description courte des produits dans la page ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer la longueur de cette description.<br /><br /><i>- 0 pour aucune description<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous supprimer une certaine longeur de texte descriptif ',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION_DELETE_WORLDS',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer le nombre de mots à supprimer. Ce système est utile avec le module des onglets<br /><br /><i>- 0 pour aucune suppression<br>- 50 pour les 50 premiers caractères</i>',
          'configuration_group_id' => '6',
          'sort_order' => '8',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher un message Nouveauté / Promotion /  Sélection / Coups de coeur?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher un message Nouveauté / Promotion / Sélection / Coups de coeur en surimpression sur l\'image du produit ?<br /><br />la durée est paramétrable dans le Menu configuration / ma boutique / Valeurs minimales / maximales<br><br><i>(Valeur true = Oui - Valeur false = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher le pourcentage de réduction du prix (promotion) ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_POURCENTAGE_TICKER',
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
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_DISPLAY_STOCK',
          'configuration_value' => 'none',
          'configuration_description' => 'Est-que vous souhaitez afficher une image indiquant une information sur le stock du produit (En stock, pratiquement épuisé, hors stock) ?',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'image\', \'number\'),',
          'date_added' => 'now()'
        ]
      );

       $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Qu\'elle est la largeur des images concernant les produits complémentaires achetés ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_IMAGE_WIDTH',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez indiquer la largeur des images qui seront affichées<br /><br /><strong>Note :</strong><br>Si le champs est vide, la taille de l\'image aura sa taille réelle. A défaut, elle sera recalculée en fonction de la nouvelle taille insérée',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_IMAGE_HEIGHT',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez indiquer la hauteur des images qui seront affichées<br /><br /><strong>Note :</strong><br>Si le champs est vide, la taille de l\'image aura sa taille réelle. A défaut, elle sera recalculée en fonction de la nouvelle taille insérée',
          'configuration_group_id' => '6',
          'sort_order' => '11',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SORT_ORDER',
          'configuration_value' => '2000',
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
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_STATUS',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TEMPLATE',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TITLE',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_MAX_DISPLAY',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_COLUMNS',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SHORT_DESCRIPTION_DELETE_WORLDS',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_POURCENTAGE_TICKER',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_TICKER',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_DISPLAY_STOCK',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_IMAGE_WIDTH',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_IMAGE_HEIGHT',
        'MODULE_PRODUCTS_INFO_ALSO_PURCHASED_SORT_ORDER'
      );
    }
  }
