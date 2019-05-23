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

  class bm_products_favorites {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_products_favorites_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_products_favorites_description');

      if ( defined('MODULE_BOXES_PRODUCTS_FAVORITES_STATUS') ) {
        $this->sort_order = MODULE_BOXES_PRODUCTS_FAVORITES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_PRODUCTS_FAVORITES_STATUS == 'True');
        $this->pages = MODULE_BOXES_PRODUCTS_FAVORITES_DISPLAY_PAGES;

        $this->group = ((MODULE_BOXES_PRODUCTS_FAVORITES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {

        $Qproducts = $CLICSHOPPING_Db->prepare('select distinct p.products_id
                                                from :table_products_favorites ph,
                                                      :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                                      :table_products_to_categories p2c,
                                                      :table_categories c
                                                where (p.products_status = 1
                                                      and g.price_group_view = 1
                                                      )
                                               or (p.products_status = 1
                                                   and g.price_group_view <> 1
                                                   )
                                                and p.products_id <> :products_id
                                                and ph.status = 1
                                                and p.products_id = ph.products_id
                                                and g.customers_group_id = :customers_group_id
                                                and g.products_group_view = 1
                                                and p.products_archive = 0
                                                and (ph.customers_group_id = :customers_group_id or ph.customers_group_id = 99)
                                                and p.products_id = p2c.products_id
                                                and p2c.categories_id = c.categories_id
                                                and c.status = 1
                                                order by rand()
                                                limit :limit
                                              ');

        $Qproducts->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
        $Qproducts->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getID());
        $Qproducts->bindInt(':limit', (int)MODULE_BOXES_PRODUCTS_FAVORITES_MAX_DISPLAY_LIMIT);
        $Qproducts->execute();

      } else {

        $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id
                                                from :table_products p,
                                                     :table_products_favorites ph,
                                                     :table_products_to_categories p2c,
                                                     :table_categories c
                                                 where p.products_status = 1
                                                 and p.products_id = ph.products_id
                                                 and ph.status = 1
                                                 and p.products_view = 1
                                                 and (ph.customers_group_id = 0 or ph.customers_group_id = 99)
                                                 and p.products_archive = 0
                                                 and p.products_id <> :products_id
                                                 and p.products_id = p2c.products_id
                                                 and p2c.categories_id = c.categories_id
                                                 and c.status = 1
                                                 order by rand()
                                                 limit  :limit
                                                ');

        $Qproducts->bindInt(':products_id',  $CLICSHOPPING_ProductsCommon->getID());
        $Qproducts->bindInt(':limit', (int)MODULE_BOXES_PRODUCTS_FAVORITES_MAX_DISPLAY_LIMIT);
        $Qproducts->execute();
      }

      $col = 0;

      if ($Qproducts->rowCount() > 0  ) {
        $favorites_banner = '';

        if ($CLICSHOPPING_Service->isStarted('Banner') ) {
          if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_PRODUCTS_FAVORITES_BANNER_GROUP)) {
            $favorites_banner = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
          } else {
            $favorites_banner = '';
          }
        }

        $data ='<!-- Boxe Favorites start -->' . "\n";
        $data .= '<section class="boxe_favorites" id="boxe_favorites">';
        $data .= '<div class="clearfix"></div>';
        $data .= '<div class="card boxeContainerFavorites">';
        $data .= '<div class="card-img-top boxeBannerContentsFavorites">' . $favorites_banner . '</div>';
        $data .= '<div class="card-header boxeHeadingFavorites"><span class="card-title boxeTitleFavorites">' . HTML::link(CLICSHOPPING::link(null,'Products&Favorites'), CLICSHOPPING::getDef('module_boxes_products_favorites_box_title')) . '</span></div>';
        $data .= '<div class="card-block  text-md-center boxeContentArroundFavorites">';
        $data .= '<div class="separator"></div>';

        while ($Qproducts->fetch() ) {
          $products_id = $Qproducts->valueInt('products_id');
          $_POST['products_id'] = $products_id;

// **************************
//    product name
// **************************
          $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($products_id);

          $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);

          $products_name_image = $CLICSHOPPING_ProductsFunctionTemplate->getProductsNameUrl($products_id);
// *************************
//       Flash discount
// **************************
          $products_flash_discount = '';
          if ($CLICSHOPPING_ProductsCommon->getProductsFlashDiscount($products_id) != '') {
            $products_flash_discount =  CLICSHOPPING::getDef('text_flash_discount') . '<br/>' . $CLICSHOPPING_ProductsCommon->getProductsFlashDiscount($products_id);
          }
// *************************
// display the differents prices before button
// **************************
          $product_price = $CLICSHOPPING_ProductsCommon->getCustomersPrice($products_id);

// **************************
// See the button more view details
// **************************
          if (MODULE_BOXES_PRODUCTS_FAVORITES_DETAIL_BUTTON == 'True') {
            $button_small_view_details = HTML::button(CLICSHOPPING::getDef('button_detail'), null, $products_name_url, 'info', null, 'sm');
          } else {
            $button_small_view_details = '';
          }

          $products_image = HTML::link($products_name_url, HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $CLICSHOPPING_ProductsCommon->getProductsImage($products_id), HTML::outputProtected($products_name), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT));
// **************************
//Ticker Image
// **************************
          if ($CLICSHOPPING_ProductsCommon->getProductsTickerSpecials($products_id) == 'True' && MODULE_BOXES_PRODUCTS_FAVORITES_TICKER == 'True') {
            $products_image .= HTML::link($products_name_url, HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_specials'), 'ModulesBoxeBootstrapTickerSpecial', $CLICSHOPPING_ProductsCommon->getProductsTickerSpecials($products_id)));
          } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerFavorites($products_id) == 'True' && MODULE_BOXES_PRODUCTS_FAVORITES_TICKER == 'True') {
            $products_image .= HTML::link($products_name_url, HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_favorite'), 'ModulesBoxeBootstrapTickerFavorite', $CLICSHOPPING_ProductsCommon->getProductsTickerFavorites($products_id)));
          } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerFeatured($products_id) == 'True' && MODULE_BOXES_PRODUCTS_FAVORITES_TICKER == 'True') {
            $products_image .= HTML::link($products_name_url, HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_featured'), 'ModulesBoxeBootstrapTickerFeatured', $CLICSHOPPING_ProductsCommon->getProductsTickerFeatured($products_id)));
          } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerProductsNew($products_id) == 'True' && MODULE_BOXES_PRODUCTS_FAVORITES_TICKER == 'True') {
            $products_image .= HTML::link($products_name_url, HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_products_new'), 'ModulesBoxeBootstrapTickerNew', $CLICSHOPPING_ProductsCommon->getProductsTickerProductsNew($products_id)));
          }

          if (MODULE_BOXES_PRODUCTS_FAVORITES_POURCENTAGE_TICKER == 'True' && !is_null($CLICSHOPPING_ProductsCommon->getProductsTickerSpecialsPourcentage($products_id)) ) {
            $ticker = HTML::link($products_name_url, HTML::tickerImage($CLICSHOPPING_ProductsCommon->getProductsTickerSpecialsPourcentage($products_id), 'ModulesBoxeBootstrapTickerSpecialPourcentage', true));
          } else {
            $ticker = '';
          }

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/products_favorites'));
          $data .= ob_get_clean();

          $col ++;
          if ($col > 0) {
            $col = 0;
          }
        } //end while

        $data .= '</div>';
        $data .='<div class="card-footer boxeBottomContentsFavorites"></div>';
        $data .= '</div>' . "\n";
        $data .= '</section>' . "\n";
        $data .='<!-- Boxe Favorites end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_BOXES_PRODUCTS_FAVORITES_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez choisir l\'emplacement du contenu de la boxe',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Parmi les options qui vous sont proposées , veuillez en choisir une. <strong>Note :</strong><br /><br /><i>- Column right : Colonne de droite<br />- Column left : Colonne de gauche</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le groupe d\'appartenance de la banniere',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe_favorites',
          'configuration_description' => 'Veuillez indiquer le groupe d\'appartenance de la bannière<br /><br /><strong>Note :</strong><br /><i>Le groupe sera à indiquer lors de la création de la bannière dans la section Marketing / Gestion des bannières</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher le bouton détail ?',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_DETAIL_BUTTON',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher le bouton détail  ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Combien de produit(s) souhaitez-vous voir affiché dans la boxe ?',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_MAX_DISPLAY_LIMIT',
          'configuration_value' => '1',
          'configuration_description' => 'Affiche un nombre déterminé de produits dans la boxe de fa&ccedil;on aléatoire.',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher un message Nouveauté / Promotion /  Sélection / Coups de coeur?',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher un message Nouveauté / Promotion / Sélection / Coups de coeur en surimpression sur l\'image du produit ?<br /><br /><strong>Note</strong> :<br />- la durée d\affichage est paramétrable dans le Menu configuration / Ma boutique / Valeurs minimales / maximales<br /><i>(Valeur true = Oui - Valeur false = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

       $CLICSHOPPING_Db->save('configuration', [
           'configuration_title' => 'Souhaitez-vous afficher le pourcentage de réduction du prix (promotion) ?',
           'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_POURCENTAGE_TICKER',
           'configuration_value' => 'False',
           'configuration_description' => 'Afficher le pourcentage de réduction du prix<br><i>(Valeur true = Oui - Valeur false = Non)</i>',
           'configuration_group_id' => '6',
           'sort_order' => '9',
           'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
           'date_added' => 'now()'
         ]
       );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer ou la boxe doit s\'afficher',
          'configuration_key' => 'MODULE_BOXES_PRODUCTS_FAVORITES_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages où la boxe doit être présente.',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                              );

    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function  keys() {
      return array('MODULE_BOXES_PRODUCTS_FAVORITES_STATUS',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_CONTENT_PLACEMENT',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_BANNER_GROUP',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_DETAIL_BUTTON',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_MAX_DISPLAY_LIMIT',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_TICKER',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_POURCENTAGE_TICKER',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_SORT_ORDER',
                   'MODULE_BOXES_PRODUCTS_FAVORITES_DISPLAY_PAGES');
    }
  }
