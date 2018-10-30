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

  namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class ProductsDescription extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/products_description');

      $this->title = $this->app->getDef('module_header_tags_products_description_title');
      $this->description = $this->app->getDef('module_header_tags_products_description_description');

      if ( defined('MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS') ) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Products']) && isset($_GET['Description'])) {
        if (isset($_GET['products_id'])) {

          $Qsubmit = $this->app->db->prepare('select submit_id,
                                                    language_id,
                                                    submit_defaut_language_title,
                                                    submit_defaut_language_keywords,
                                                    submit_defaut_language_description,
                                                    submit_language_products_info_title,
                                                    submit_language_products_info_keywords,
                                                    submit_language_products_info_description
                                              from :table_submit_description
                                              where submit_id = 1
                                              and language_id = :language_id
                                            ');
          $Qsubmit->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );
          $Qsubmit->execute();

          $QproductInfo = $this->app->db->prepare('select pd.products_name,
                                                           pd.products_head_title_tag,
                                                           pd.products_head_keywords_tag,
                                                           pd.products_head_desc_tag
                                                    from :table_products p,
                                                         :table_products_description pd
                                                    where p.products_status = 1
                                                    and p.products_view = 1
                                                    and p.products_id = :products_id
                                                    and pd.products_id = p.products_id
                                                    and pd.language_id = :language_id
                                                  ');
          $QproductInfo->bindInt(':products_id', (int)$_GET['products_id'] );
          $QproductInfo->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );
          $QproductInfo->execute();

          $QcategoryInfo = $this->app->db->prepare('select cd.categories_name
                                                    from :table_products_to_categories ptc,
                                                         :table_categories_description cd
                                                    where ptc.products_id = :products_id
                                                    and ptc.categories_id = cd.categories_id
                                                    and cd.language_id = :language_id
                                                    limit 1
                                                  ');

          $QcategoryInfo->bindInt(':products_id', (int)$_GET['products_id'] );
          $QcategoryInfo->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );
          $QcategoryInfo->execute();

          $products_name_clean = HTML::sanitize($QproductInfo->value('products_name'));
          $products_name_replace = HTML::sanitize($QproductInfo->value('products_name'));
          $categories_name_clean = HTML::sanitize($QcategoryInfo->value('categories_name'));

          $tags_array = [];

          if(empty($QproductInfo->value('products_head_title_tag'))) {
            if (empty($Qsubmit->value('submit_language_products_info_title'))) {
              $tags_array['title'] = $products_name_clean .', ' . $categories_name_clean .', '. HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
            } else {
              $tags_array['title'] =  $products_name_clean .', ' . $categories_name_clean .',  '. HTML::sanitize($Qsubmit->value('submit_language_products_info_title'));
            }
          } else {
            $tags_array['title'] = HTML::sanitize($QproductInfo->value('products_head_title_tag')) .', ' . $categories_name_clean;
          }

          if(empty($QproductInfo->value('products_head_desc_tag'))) {
            if (empty($Qsubmit->value('submit_language_products_info_description'))) {
              $tags_array['desc']=  $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
            } else {
              $tags_array['desc']= $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' .  HTML::sanitize($Qsubmit->value('submit_language_products_info_description'));
            }
          } else {
            $tags_array['desc']=  $QproductInfo->value('products_head_desc_tag')  . ', ' .  $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' . HTML::sanitize($QproductInfo->value('products_head_desc_tag'));
          }

          if(empty($QproductInfo->value('products_head_keywords_tag'))) {
            if (empty($Qsubmit->value('submit_language_products_info_keywords'))) {
              $tags_array['keywords']= $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' .  HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
            } else {
              $tags_array['keywords']= $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' . HTML::sanitize($Qsubmit->value('submit_language_products_info_keywords'));
            }
          } else {
            $tags_array['keywords']=  $QproductInfo->value('products_head_keywords_tag') . ', ' . $products_name_clean .', ' . $products_name_replace . ', ' . $categories_name_clean .', ' . HTML::sanitize($Qsubmit->value('products_head_keywords_tag'));
          }

          $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ', ' . $CLICSHOPPING_Template->getTitle());
          $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ', ' . $CLICSHOPPING_Template->getDescription());
          $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
          $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());

          $output =
<<<EOD
{$title}
{$description}
{$keywords}
{$new_keywords}
EOD;
        }
      }

      return $output;
    }

    public function Install() {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $this->app->db->save('configuration', [
          'configuration_title' => 'Display sort order',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_SORT_ORDER',
          'configuration_value' => '162',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '215',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys() {
      return ['MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS',
              'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_SORT_ORDER'
             ];
    }
  }
