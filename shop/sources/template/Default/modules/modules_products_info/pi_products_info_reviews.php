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
  use ClicShopping\Sites\Common\HTMLOverrideCommon;

  class pi_products_info_reviews {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_reviews');
      $this->description = CLICSHOPPING::getDef('module_products_info_reviews_description');

      if (defined('MODULE_PRODUCTS_INFO_REVIEWS_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_REVIEWS_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_REVIEWS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if ($CLICSHOPPING_ProductsCommon->getID() && isset($_GET['Description']) && isset($_GET['Products'])) {

        $content_width = (int)MODULE_PRODUCTS_INFO_REVIEWS_CONTENT_WIDTH;

        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Language = Registry::get('Language');

        $products_reviews_content = HTMLOverrideCommon::starHeaderTagRateYo();

//*******************************************
// products review<
//********************************************
        $Qreviews = $CLICSHOPPING_Db->prepare('select r.reviews_id,
                                                       left(rd.reviews_text, :limitText ) as reviews_text,
                                                       r.reviews_rating,
                                                       r.date_added,
                                                       r.status,
                                                       r.customers_name
                                               from :table_reviews r,
                                                    :table_reviews_description rd
                                               where r.products_id = :products_id
                                               and r.reviews_id = rd.reviews_id
                                               and rd.languages_id =:languages_id
                                               and r.status = 1
                                               order by r.reviews_rating desc,
                                                        r.date_added desc
                                               limit :limit
                                           ');
        $Qreviews->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getID());
        $Qreviews->bindInt(':languages_id', $CLICSHOPPING_Language->getId());
        $Qreviews->bindInt(':limitText', MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_WORDS);
        $Qreviews->bindInt(':limit', MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_COMMENTS);

        $Qreviews->execute();

        $count_review = $Qreviews->rowCount();

//*******************************************
// customers_feedback
//********************************************

        $QorderProducts = $CLICSHOPPING_Db->prepare('select products_id,
                                                            orders_id
                                                     from :table_orders_products
                                                     where products_id = :products_id
                                                    ');
        $QorderProducts->bindValue(':products_id', $CLICSHOPPING_ProductsCommon->getID());
        $QorderProducts->execute();

        $products_reviews_content .= '<!-- Start products_REVIEWS -->' . "\n";
        $products_reviews_content .= '<div class="' . $content_width . '">';
        $products_reviews_content .= '<div class="separator"></div>';

         if ($count_review >= 1 || $QorderProducts->rowCount() >= 1) {

          $products_reviews_content .= '<div class="moduleProductsInfoReviewsRow" itemprop="review" itemscope itemtype="https://schema.org/Review">';
          $products_reviews_content .= '<div class="moduleProductsInfoReviewsTitle">';
          $products_reviews_content .= '<span class="page-header moduleProductsInfoReviewsTitle"  itemprop="name"><h3>' . CLICSHOPPING::getDef('heading_rewiews')  . ' ' . $CLICSHOPPING_ProductsCommon->getProductsName() . '</h3></span>';
          $products_reviews_content .= '</div>';
          $products_reviews_content .= '<div class="float-md-right">';
          $products_reviews_content .= '';
          $products_reviews_content .= '</div>';
          $products_reviews_content .= '<div class="clearfix"></div>';
          $products_reviews_content .= '<hr>';
          $products_reviews_content .= '<div class="d-flex flex-wrap">';

          if ($Qreviews->rowCount() >= 1) {
            $count = 0;

            while ($Qreviews->fetch()) {
              $count = $count + 1;
              $customer_name  = '*** ' . HTML::outputProtected(substr($Qreviews->value('customers_name') . ' ' , 4, -4 )) . ' ***';

              $products_reviews_content .= '<div class="col-md-12">';
              $products_reviews_content .= '<span class="moduleProductsInfoTextReviewByName" itemprop="author">';
              $products_reviews_content .= '<a href="' . CLICSHOPPING::link(null, 'Products&ReviewsInfo&products_id=' . $CLICSHOPPING_ProductsCommon->getID() . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '">' . CLICSHOPPING::getDef('text_review_by', ['customer_name' => $customer_name]) . '</a>';
              $products_reviews_content .= '</span>';
              $products_reviews_content .= '<span class="float-md-right" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">';
              $products_reviews_content .= '<meta itemprop="worstRating" content = "1">';
              $products_reviews_content .= '<span class="col-md-12 productsInfoReviewsRating" itemprop="ratingValue">' . HTML::stars($Qreviews->valueInt('reviews_rating')) . '</span>';
              $products_reviews_content .= '</span>';
              $products_reviews_content .= '</div>';
              $products_reviews_content .= '<div class="col-md-12 moduleProductsInfoDateReviewAdded" itemprop="datePublished" content="' . DateTime::toLong($Qreviews->value('date_added')) . '">';
              $products_reviews_content .= '<span class="moduleProductsInfoDateReviewAdded">' . CLICSHOPPING::getDef('text_review_date_added', ['date' => DateTime::toLong($Qreviews->value('date_added'))] ) . '</span>';
              $products_reviews_content .= '</div>';
              $products_reviews_content .= '<div class="col-md-12">';
              $products_reviews_content .= '<div class="moduleProductsInfoReviewText" itemprop="description">';
              $products_reviews_content .= HTML::breakString(HTML::outputProtected($Qreviews->value('reviews_text')), 60, '-<br />') . ((strlen($Qreviews->value('reviews_text')) >= MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_WORDS) ? '..' : '') . '<br />';
              $products_reviews_content .= '</div>';
              $products_reviews_content .= '</div>';
              $products_reviews_content .= '<hr>';
            }
          }

//*******************************************
// customers_feedback
//********************************************
           if ($count_review != 0) {

             $details_button = HTML::button(CLICSHOPPING::getDef('button_all_reviews'), null, CLICSHOPPING::link(null, 'Products&Reviews&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'info');
             $write_button = HTML::button(CLICSHOPPING::getDef('button_write_review'), null, CLICSHOPPING::link(null, 'Products&ReviewsWrite&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'success');

             $products_reviews_content .= '<div class="clearfix"></div>';

             $products_reviews_content .= '<span class="col-md-2">' . $details_button . '</span>';
             $products_reviews_content .= '<span class="col-md-10 text-md-right">' . $write_button . '</span>';
           }
         }

        if( $count_review == 0) {
          $write_button = HTML::button(CLICSHOPPING::getDef('button_write_review'), null, CLICSHOPPING::link(null, 'Products&ReviewsWrite&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'success');
          $products_reviews_content .= '<div class="text-md-right">' . $write_button . '</div>';
        }

        $products_reviews_content .= '</div>' . "\n";
        $products_reviews_content .= '<!-- end products_REVIEWS -->' . "\n";

        $CLICSHOPPING_Template->addBlock($products_reviews_content, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_REVIEWS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_REVIEWS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_REVIEWS_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Combien de commentaires souhaitez-vous afficher ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_COMMENTS',
          'configuration_value' => '5',
          'configuration_description' => 'Veuillez indiquer le nombre de commentaires que vous souhaitez afficher ?',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Combien de mots souhaitez-vous afficher ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_WORDS',
          'configuration_value' => '300',
          'configuration_description' => 'Veuillez indiquer le nombre de mots que vous souhaitez afficher ?',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_REVIEWS_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '4',
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
        'MODULE_PRODUCTS_INFO_REVIEWS_STATUS',
        'MODULE_PRODUCTS_INFO_REVIEWS_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_COMMENTS',
        'MODULE_PRODUCTS_INFO_REVIEWS_NUMBER_WORDS',
        'MODULE_PRODUCTS_INFO_REVIEWS_SORT_ORDER'
      );
    }
  }
