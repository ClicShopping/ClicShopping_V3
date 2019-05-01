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


  namespace ClicShopping\Apps\Marketing\SEO\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

  class Index extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {

      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');

      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/header_tag/index');

      $this->title = $this->app->getDef('module_header_tags_index_title');
      $this->description = $this->app->getDef('module_header_tags_index_description');

      if ( defined('MODULE_HEADER_TAGS_INDEX_STATUS') ) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_INDEX_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_INDEX_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $index = HTTP::getShopUrlDomain() . 'index.php';
      $url = CLICSHOPPING::getConfig('http_server', 'Shop') . $_SERVER['REQUEST_URI'];

      if (isset($_GET['language'])) {
        $language = HTML::sanitize($_GET['language']);
      }

      if (($index === $url || isset($language)) && !isset($_GET['Products']) && !isset($_GET['Blog']) && !isset($_GET['Info'])) {
        $Qsubmit = $this->app->db->prepare('select submit_id,
                                                  language_id,
                                                  submit_defaut_language_title,
                                                  submit_defaut_language_keywords,
                                                  submit_defaut_language_description
                                              from :table_submit_description
                                              where submit_id = 1
                                              and language_id = :language_id
                                          ');

        $Qsubmit->bindInt(':language_id', $this->lang->getId() );
        $Qsubmit->execute();
        $submit = $Qsubmit->fetch();

        if (empty($submit['submit_defaut_language_title'])) {
          $title = HTML::outputProtected(STORE_NAME);
        } else {
          $title = HTML::sanitize($submit['submit_defaut_language_title']);
        }

        if (empty($submit['submit_defaut_language_description'])) {
          $description = HTML::outputProtected(STORE_NAME);
        } else {
          $description = HTML::sanitize($submit['submit_defaut_language_description']);
        }

        if (empty($submit['submit_defaut_language_keywords'])) {
          $keywords = HTML::outputProtected(STORE_NAME);
        } else {
          $keywords = HTML::sanitize($submit['submit_defaut_language_keywords']);
        }

        $title = $CLICSHOPPING_Template->setTitle($title . ', ' . $CLICSHOPPING_Template->getTitle());
        $description = $CLICSHOPPING_Template->setDescription($description  . ', ' . $CLICSHOPPING_Template->getDescription());
        $keywords = $CLICSHOPPING_Template->setKeywords($keywords  . ', ' . $CLICSHOPPING_Template->getKeywords());
        $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($keywords  . ', ' . $CLICSHOPPING_Template->getKeywords());

        $output =
<<<EOD
{$title}
{$description}
{$keywords}
{$new_keywords}
EOD;

      }

      return $output;
    }

    public function Install() {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_INDEX_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_INDEX_SORT_ORDER',
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
      return ['MODULE_HEADER_TAGS_INDEX_STATUS',
              'MODULE_HEADER_TAGS_INDEX_SORT_ORDER'
             ];
    }
  }
