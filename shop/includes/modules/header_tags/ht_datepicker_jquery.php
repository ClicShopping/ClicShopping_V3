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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_datepicker_jquery {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = 'footer_scripts';
      $this->title = CLICSHOPPING::getDef('module_header_tags_datepicker_jquery_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_datepicker_jquery_description');

      if ( defined('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_null(MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES)) {
        $pages_array = [];

        foreach (explode(';', MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES) as $page) {
          $page = trim($page);

          if (!empty($page)) {
            $pages_array[] = $page;
          }
        }

        $url_string = $CLICSHOPPING_Template->getUrlWithoutSEFU();

        $language_code = $CLICSHOPPING_Language->getCode();

        if (in_array($url_string, $pages_array)) {

          $CLICSHOPPING_Template->addBlock('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">' . "\n", 'header_tags');

          $CLICSHOPPING_Template->addBlock('<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>' . "\n", $this->group);
          $CLICSHOPPING_Template->addBlock('<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.' . $language_code . '.min.js"></script>' . "\n", $this->group);


          // create_account
          // account edit
          $CLICSHOPPING_Template->addBlock('<script>$(\'#dob\').datepicker({dateFormat: \'' . CLICSHOPPING::getDef('js_date_format')  . '\',viewMode: 2});</script>', $this->group);
          // advanced search
          $CLICSHOPPING_Template->addBlock('<script>var nowTemp = new Date(); var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0); $(\'#dfrom\').datepicker({dateFormat: \'' . CLICSHOPPING::getDef('js_date_format')  . '\',onRender: function(date) {return date.valueOf() > now.valueOf() ? \'disabled\' : \'\';}}); </script>', $this->group);
          $CLICSHOPPING_Template->addBlock('<script>$(\'#dto\').datepicker({dateFormat: \'' . CLICSHOPPING::getDef('js_date_format')  . '\',onRender: function(date) {return date.valueOf() > now.valueOf() ? \'disabled\' : \'\';}});</script>', $this->group);
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous activer ce module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER',
          'configuration_value' => '65',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '45',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Pages ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES',
          'configuration_value' => implode(';', $this->get_default_pages()),
          'configuration_description' => 'The pages to add the Datepicker jQuery Scripts to',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'use_function' => 'ht_datepicker_jquery_show_pages',
          'set_function' => 'ht_datepicker_jquery_edit_pages',
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
      return array('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS',
                   'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER',
                   'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES'
                   );
    }

    public function get_default_pages() {
        return array('Search&AdvancedSearch',
                    'Account&Edit',
                    'Account&Create',
                    'Account&CreatePro'
                    );
    }

  }

