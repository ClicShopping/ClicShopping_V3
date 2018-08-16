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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers as ManufacturersApp;

  class Manufacturers extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {
      if (!Registry::exists('Manufacturers')) {
        Registry::set('Manufacturers', new ManufacturersApp());
      }

      $this->app = Registry::get('Manufacturers');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/manufacturers');

      $this->title = $this->app->getDef('module_header_tags_manufacturers_title');
      $this->description = $this->app->getDef('module_header_tags_manufacturers_description');

      if ( defined('MODULE_HEADER_TAGS_MANUFACTURERS_STATUS') ) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_MANUFACTURERS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_MANUFACTURERS_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS') || CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS == 'False') {
        return false;
      }

      if (CLICSHOPPING::getBaseNameIndex()) {
        if (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {

          $QmetaInfo = $this->app->db->prepare('select manufacturer_seo_title,
                                                    manufacturer_seo_description,
                                                    manufacturer_seo_keyword
                                             from :table_manufacturers_info
                                             where manufacturers_id = :manufacturers_id
                                             and languages_id = :language_id
                                           ');
          $QmetaInfo->bindInt(':manufacturers_id', (int)$_GET['manufacturers_id'] );
          $QmetaInfo->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );
          $QmetaInfo->execute();

          $Qsubmit = $this->app->db->prepare('select  submit_id,
                                                      language_id,
                                                      submit_defaut_language_title,
                                                      submit_defaut_language_keywords,
                                                      submit_defaut_language_description
                                             from :table_submit_description
                                             where submit_id = 1
                                             and language_id = :language_id
                                            ');

          $Qsubmit->bindInt(':language_id',  (int)$CLICSHOPPING_Language->getId() );

          $tags_array = [];

          if(empty($QmetaInfo->value('manufacturer_seo_title'))) {
            if (empty($Qsubmit->value('submit_defaut_language_title'))) {
              $tags_array['title'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
            } else {
              $tags_array['title'] = $QmetaInfo->value('manufacturer_seo_title');
            }
          } else {
            $tags_array['title'] = HTML::sanitize($QmetaInfo->value('manufacturer_seo_title'));
          }

          if(empty($QmetaInfo->value('manufacturer_seo_description'))) {
            if (empty($Qsubmit->value('submit_defaut_language_description'))) {
              $tags_array['desc'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
            } else {
              $tags_array['desc'] = $QmetaInfo->value('manufacturer_seo_description');
            }
          } else {
            $tags_array['desc'] = HTML::sanitize($QmetaInfo->value('manufacturer_seo_description'));
          }


          if(empty($QmetaInfo->value('manufacturer_seo_keyword'))) {
            if (empty($Qsubmit->value('submit_defaut_language_keywords'))) {
              $tags_array['keywords'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
            } else {
              $tags_array['keywords'] = $QmetaInfo->value('manufacturer_seo_keyword');
            }
          } else {
            $tags_array['keywords'] = HTML::sanitize($QmetaInfo->value('manufacturer_seo_keyword'));
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
          'configuration_key' => 'MODULE_HEADER_TAGS_MANUFACTURERS_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_MANUFACTURERS_SORT_ORDER',
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
      return ['MODULE_HEADER_TAGS_MANUFACTURERS_STATUS',
              'MODULE_HEADER_TAGS_MANUFACTURERS_SORT_ORDER'
             ];
    }
  }
