<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Module\HeaderTags;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Recommendations\Recommendations as RecommendationsApp;

class Recommendations extends \ClicShopping\OM\Modules\HeaderTagsAbstract
{
  protected mixed $lang;
  protected mixed $app;
  protected mixed $template;

  protected function init()
  {
    if (!Registry::exists('Recommendations')) {
      Registry::set('Recommendations', new RecommendationsApp());
    }

    $this->app = Registry::get('Recommendations');
    $this->lang = Registry::get('Language');
    $this->group = 'header_tags'; // could be header_tags or footer_scripts

    $this->app->loadDefinitions('Module/HeaderTags/recommendations');

    $this->title = $this->app->getDef('module_header_tags_recommendations_title');
    $this->description = $this->app->getDef('module_header_tags_recommendations_description');

    if (\defined('MODULE_HEADER_TAGS_RECOMMENDATIONS_STATUS')) {
      $this->sort_order = (int)MODULE_HEADER_TAGS_RECOMMENDATIONS_SORT_ORDER;
      $this->enabled = (MODULE_HEADER_TAGS_RECOMMENDATIONS_STATUS == 'True');
    }
  }

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function getOutput()
  {
    if (isset($_GET['Products'], $_GET['Recommendations'])) {
      $this->template = Registry::get('Template');
      /*
              if (!Registry::exists('SeoShopRecommendations')) {
                Registry::set('SeoShopRecommendations', new SeoShopRecommendations());
              }

              $CLICSHOPPING_SEOShop = Registry::get('SeoShopRecommendations');

              $title = $CLICSHOPPING_SEOShop->getSeoRecommendationsTitle();
              $description = $CLICSHOPPING_SEOShop->getSeoRecommendationsDescription();
              $keywords = $CLICSHOPPING_SEOShop->getSeoRecommendationsKeywords();

              $title = $this->template->setTitle($title) . ' ' . $this->template->getTitle();
              $description = $this->template->setDescription($description) . ' ' . $this->template->getDescription();
              $keywords = $this->template->setKeywords($keywords) . ', ' . $this->template->getKeywords();
      */
      $title = $this->template->getTitle();
      $description = $this->template->getDescription();
      $keywords = $this->template->getKeywords();

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

  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to install this module ?',
        'configuration_key' => 'MODULE_HEADER_TAGS_RECOMMENDATIONS_STATUS',
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
        'configuration_key' => 'MODULE_HEADER_TAGS_RECOMMENDATIONS_SORT_ORDER',
        'configuration_value' => '166',
        'configuration_description' => 'Display sort order (The lower is displayed in first)',
        'configuration_group_id' => '6',
        'sort_order' => '215',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function keys()
  {
    return [
      'MODULE_HEADER_TAGS_RECOMMENDATIONS_STATUS',
      'MODULE_HEADER_TAGS_RECOMMENDATIONS_SORT_ORDER'
    ];
  }
}
