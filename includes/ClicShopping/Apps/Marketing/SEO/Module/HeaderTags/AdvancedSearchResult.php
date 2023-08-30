<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\SEO\Module\HeaderTags;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\Apps\Marketing\SEO\Classes\Shop\SeoShop as SeoShopSearch;
use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

class AdvancedSearchResult extends \ClicShopping\OM\Modules\HeaderTagsAbstract
{
  protected mixed $lang;
  protected mixed $app;
  protected mixed $template;

  protected function init()
  {
    if (!Registry::exists('SEO')) {
      Registry::set('SEO', new SEOApp());
    }

    $this->app = Registry::get('SEO');
    $this->lang = Registry::get('Language');
    $this->group = 'header_tags'; // could be header_tags or footer_scripts

    $this->app->loadDefinitions('Module/header_tag/advanced_search_result');

    $this->title = $this->app->getDef('module_header_tags_advanced_search_result_title');
    $this->description = $this->app->getDef('module_header_tags_advanced_search_result_description');

    if (\defined('MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS')) {
      $this->sort_order = MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER;
      $this->enabled = (MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS == 'True');
    }
  }

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function getOutput()
  {
    if (isset($_GET['Search'], $_GET['Q'], $_POST['keywords'])) {
      $this->template = Registry::get('Template');

      if (!Registry::exists('SeoShopSearch')) {
        Registry::set('SeoShopSearch', new SeoShopSearch());
      }

      $CLICSHOPPING_SEOShop = Registry::get('SeoShopSearch');

      $title = $CLICSHOPPING_SEOShop->getSeoIndexTitle();
      $description = $CLICSHOPPING_SEOShop->getSeoIndexDescription();
      $keywords = $CLICSHOPPING_SEOShop->getSeoIndexKeywords();

      $title = HTML::sanitize($_POST['keywords']) . ',' . $this->template->setTitle($title) . ' ' . $this->template->getTitle();
      $description = HTML::sanitize($_POST['keywords']) . ',' . $this->template->setDescription($description) . ', ' . $this->template->getDescription();
      $keywords = HTML::sanitize($_POST['keywords']) . ',' . $this->template->setKeywords($keywords) . ', ' . $this->template->getKeywords();

      $output =
        <<<EOD
    <title>{$title}</title>
    <meta name="description" content="{$description}" />
    <meta name="keywords" content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;

      return $output;
    }
  }

  public function install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to install this module ?',
        'configuration_key' => 'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS',
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
        'configuration_key' => 'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER',
        'configuration_value' => '45',
        'configuration_description' => 'Display sort order (The lower is displayed in first)',
        'configuration_group_id' => '6',
        'sort_order' => '5',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function remove()
  {
    return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
  }

  public function keys()
  {
    return ['MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_STATUS',
      'MODULE_HEADER_TAGS_ADVANCED_SEARCH_RESULT_SORT_ORDER'
    ];
  }
}
