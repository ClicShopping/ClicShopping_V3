<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

class ProductsDescription extends \ClicShopping\OM\Modules\HeaderTagsAbstract
{
  private mixed $lang;
  public mixed $app;
  public string $group;

  /**
   * Initializes the module by setting up required registry entries, language, and configuration properties.
   *
   * @return void
   */
  protected function init()
  {
    if (!Registry::exists('Products')) {
      Registry::set('Products', new ProductsApp());
    }

    $this->app = Registry::get('Products');
    $this->lang = Registry::get('Language');
    $this->group = 'header_tags'; // could be header_tags or footer_scripts

    $this->app->loadDefinitions('Module/HeaderTags/products_description');

    $this->title = $this->app->getDef('module_header_tags_products_description_title');
    $this->description = $this->app->getDef('module_header_tags_products_description_description');

    if (\defined('MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS')) {
      $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_SORT_ORDER;
      $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS == 'True');
    }
  }

  /**
   * Checks whether the current module or feature is enabled.
   *
   * @return bool Returns true if enabled, false otherwise.
   */
  public function isEnabled()
  {
    return $this->enabled;
  }

  /**
   * Generates and returns the SEO output consisting of title, description, and keywords
   * for a product based on the product's information and language settings.
   *
   * @return string|false Returns the generated SEO output as a string if the product
   * information is properly retrieved; returns false otherwise.
   */
  public function getOutput()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

    if (!\defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Id']) || isset($_GET['products_id'])) {
      if ($CLICSHOPPING_ProductsCommon->getID()) {
        $products_id = $CLICSHOPPING_ProductsCommon->getID();

        $Qsubmit = $this->app->db->prepare('select seo_id,
                                                    language_id,
                                                    seo_defaut_language_title,
                                                    seo_defaut_language_keywords,
                                                    seo_defaut_language_description,
                                                    seo_language_products_info_title,
                                                    seo_language_products_info_keywords,
                                                    seo_language_products_info_description
                                              from :table_seo
                                              where seo_id = 1
                                              and language_id = :language_id
                                            ');
        $Qsubmit->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $Qsubmit->execute();

        $QproductInfo = $this->app->db->prepare('select pd.products_head_title_tag,
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
        $QproductInfo->bindInt(':products_id', $products_id);
        $QproductInfo->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $QproductInfo->execute();

        $QcategoryInfo = $this->app->db->prepare('select cd.categories_name
                                                    from :table_products_to_categories ptc,
                                                         :table_categories_description cd
                                                    where ptc.products_id = :products_id
                                                    and ptc.categories_id = cd.categories_id
                                                    and cd.language_id = :language_id
                                                    limit 1
                                                  ');

        $QcategoryInfo->bindInt(':products_id', $products_id);
        $QcategoryInfo->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $QcategoryInfo->execute();

        $products_name_clean = HTML::sanitize($CLICSHOPPING_ProductsCommon->getProductsName($products_id)) . ', ';
        $products_name_replace = HTML::sanitize($QproductInfo->value('products_head_title_tag')) . ', ';
        $categories_name_clean = ', ' . HTML::sanitize($QcategoryInfo->value('categories_name'));

        $store_name = HTML::sanitize(STORE_NAME);

        $seo_language_products_info_title = HTML::sanitize($Qsubmit->value('seo_language_products_info_title')) . ', ';

        if (empty($QproductInfo->value('products_head_title_tag'))) {
          if (empty($seo_language_products_info_title)) {
            $title = $products_name_clean . $categories_name_clean . HTML::sanitize($Qsubmit->value('seo_defaut_language_title')) . ', ' . $store_name;
          } else {
            $title = $CLICSHOPPING_ProductsCommon->getProductsName($products_id) . $categories_name_clean . $seo_language_products_info_title . $store_name;
          }
        } else {
          $title = $products_name_replace . ', ' . $categories_name_clean . $seo_language_products_info_title . $store_name;
        }

        if (empty($QproductInfo->value('products_head_desc_tag'))) {
          if (empty($Qsubmit->value('seo_language_products_info_description'))) {
            $description = $products_name_clean . $products_name_replace . $categories_name_clean . HTML::sanitize($Qsubmit->value('seo_defaut_language_description'));
          } else {
            $description = $products_name_clean . $products_name_replace . $categories_name_clean . HTML::sanitize($Qsubmit->value('seo_language_products_info_description'));
          }
        } else {
          $description = $QproductInfo->value('products_head_desc_tag') . ', ' . $products_name_clean . $products_name_replace . $categories_name_clean;
        }

        if (empty($QproductInfo->value('products_head_keywords_tag'))) {
          if (empty($Qsubmit->value('seo_language_products_info_keywords'))) {
            $keywords = $products_name_clean . $products_name_replace . $categories_name_clean . HTML::sanitize($Qsubmit->value('seo_defaut_language_keywords'));
          } else {
            $keywords = $products_name_clean . $products_name_replace . $categories_name_clean . HTML::sanitize($Qsubmit->value('seo_language_products_info_keywords'));
          }
        } else {
          $keywords = $QproductInfo->value('products_head_keywords_tag') . ', ' . $products_name_clean . $products_name_replace . $categories_name_clean;
        }

        $output =
          <<<EOD
    <title>{$title}</title>
    <meta name="description" content="{$description}" />
    <meta name="keywords"  content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;
        return $output;
      }
    }
  }

  /**
   * Installs the module by adding necessary configuration values into the database.
   *
   * @return void
   */
  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to install this module ?',
        'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to install this module ?',
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
        'configuration_description' => 'Display sort order (The lower is displayed in first)',
        'configuration_group_id' => '6',
        'sort_order' => '215',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  /**
   * Retrieves an array of configuration keys related to the product description module.
   *
   * @return array Returns an array of configuration key names.
   */
  public function keys()
  {
    return ['MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_STATUS',
      'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_DESCRIPTION_SORT_ORDER'
    ];
  }
}
