<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Specials\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

  class Specials extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {

    protected $lang;
    protected $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('Specials')) {
        Registry::set('Specials', new SpecialsApp());
      }

      $this->app = Registry::get('Specials');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/products_specials');

      $this->title = $this->app->getDef('module_header_tags_products_specials_title');
      $this->description = $this->app->getDef('module_header_tags_products_specials_description');

      if (defined('MODULE_HEADER_TAGS_PRODUCT_SPECIALS_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_SPECIALS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_SPECIALS_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_GET['Products']) && isset($_GET['Specials'])) {
        $Qsubmit = $this->app->db->prepare('select submit_id,
                                                language_id,
                                                submit_defaut_language_title,
                                                submit_defaut_language_keywords,
                                                submit_defaut_language_description,
                                                submit_language_special_title,
                                                submit_language_special_keywords,
                                                submit_language_special_description
                                        from :table_submit_description
                                        where submit_id = 1
                                        and language_id = :language_id
                                      ');
        $Qsubmit->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qsubmit->execute();

        $tags_array = [];

        if (empty($Qsubmit->value('submit_language_special_title'))) {
          $tags_array['title'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
        } else {
          $tags_array['title'] = HTML::sanitize($Qsubmit->value('submit_language_special_title'));
        }

        if (empty($Qsubmit->value('submit_language_special_description'))) {
          $tags_array['desc'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
        } else {
          $tags_array['desc'] = HTML::sanitize($Qsubmit->value('submit_language_special_description'));
        }

        if (empty($Qsubmit->value('submit_language_special_keywords'))) {
          $tags_array['keywords'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
        } else {
          $tags_array['keywords'] = HTML::sanitize($Qsubmit->value('submit_language_special_keywords'));
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

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_SPECIALS_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_SPECIALS_SORT_ORDER',
          'configuration_value' => '166',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '215',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return ['MODULE_HEADER_TAGS_PRODUCT_SPECIALS_STATUS',
        'MODULE_HEADER_TAGS_PRODUCT_SPECIALS_SORT_ORDER'
      ];
    }
  }
