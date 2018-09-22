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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\Sites\ClicShoppingAdmin\TemplateAdmin;

  class fo_footer_multi_template {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_footer_multi_template_title');
      $this->description = CLICSHOPPING::getDef('module_footer_multi_template_description');

      if ( defined('MODULE_FOOTER_MULTI_TEMPLATE_STATUS') ) {
        $this->sort_order = MODULE_FOOTER_MULTI_TEMPLATE_SORT_ORDER;
        $this->enabled = MODULE_FOOTER_MULTI_TEMPLATE_STATUS;
        $this->pages = MODULE_FOOTER_MULTI_TEMPLATE_DISPLAY_PAGES;
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

      if  ( MODE_VENTE_PRIVEE == 'false' || (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() )) {

        $content_width = (int)MODULE_FOOTER_MULTI_TEMPLATE_CONTENT_WIDTH;
        $menu_footer =  $CLICSHOPPING_PageManagerShop->pageManagerDisplayFooterMenu();

        $footer_tag = '<!-- Start footer social footer -->' . "\n";
        $footer_tag .='
      <script type="application/ld+json">
{
  "@context" : "https://schema.org",
  "@type" : "Organization",
  "name" : "' . STORE_NAME . '",
  "url" : "' . CLICSHOPPING::getConfig('http_server', 'Shop') . '",
  "sameAs" : [
';

        if (!empty(MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_FACEBOOK_URL )) {
          $footer_tag .=' "' . MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_FACEBOOK_URL . '", ';
        }
        if (!empty(MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL )) {
          $footer_tag .='  "' . MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL . '", ';
        }
        if (!empty(MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_PINTEREST_URL )) {
          $footer_tag .=' "' . MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_PINTEREST_URL . '", ';
        }
        if (!empty(MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_GOOGLEPLUS_URL )) {
          $footer_tag .=' "' . MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_GOOGLEPLUS_URL . '" ';
        }
        $footer_tag .='
  ]
}
</script>' . "\n";

        $footer_tag .= '<!-- footer social footer -->' . "\n";

        $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

        $social_footer = '<!-- footer social footer -->' . "\n";


        $footer_template = '<!-- footer multi template start -->' . "\n";

        $filename = '';
        $filename = $CLICSHOPPING_Template->getTemplateModulesFilename($this->group . '/template_html/' . MODULE_FOOTER_MULTI_TEMPLATE);

        $facebook = MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_FACEBOOK_URL;
        if (!empty($facebook)) {
          $facebook_url = rawurldecode($facebook);
        } else {
          $facebook_url = '#';
        }

        $twitter = MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL;
        if (!empty($twitter)) {
          $twitter_url = rawurldecode(MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL);
        } else {
          $twitter_url = '#';
        }

        $pinterest = MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_PINTEREST_URL;
        if (!empty($pinterest)) {
          $pinterest_url = rawurldecode($pinterest);
        } else {
          $pinterest_url = '#';
        }

        $googleplus= MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_GOOGLEPLUS_URL;
        if (!empty($googleplus)) {
          $googleplus_url = rawurldecode($googleplus);
        } else {
          $googleplus_url = '#';
        }

        if (defined('MODULES_HEADER_TAGS_MAILCHIMP_LIST_ANONYMOUS')) {
          if(!empty(MODULES_HEADER_TAGS_MAILCHIMP_LIST_ANONYMOUS)) {
            $mailchimp_list_anonymous = MODULES_HEADER_TAGS_MAILCHIMP_LIST_ANONYMOUS;
          }
        }

        if (is_file($filename)) {
          ob_start();
          require($filename);
          $footer_template .= ob_get_clean();
        } else {
          echo  '<div class="alert alert-warning text-md-center" role="alert">' . CLICSHOPPING::getDef('template_does_not_exist') . '</div>';
          exit;
        }

        $footer_template .= '<!-- footer multi template end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($footer_template, $this->group);
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_FOOTER_MULTI_TEMPLATE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the template you want to use ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE',
          'configuration_value' => 'footer_multi_template.php',
          'configuration_description' => 'Select the the template you want to use.',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, select the width of your module ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_CONTENT_WIDTH',
          'configuration_value' => '3',
          'configuration_description' => 'Indicate a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display the privacy message (need mailchimp module) ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_MAILCHIMP_DISPLAY_PRIVACY',
          'configuration_value' => 'False',
          'configuration_description' => 'Display the privacy message (need mailchimp module)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the Facebook URL ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_FACEBOOK_URL',
          'configuration_value' => 'Indicate the account url',
          'configuration_description' => 'Ins&eacute;rer un titre',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the Twitter URL ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL',
          'configuration_value' => 'Indicate the account url',
          'configuration_description' => 'Ins&eacute;rer un titre',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the Pointerest URL ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_PINTEREST_URL',
          'configuration_value' => 'Indicate the account url',
          'configuration_description' => 'Ins&eacute;rer un titre',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the Google + URL ?',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_GOOGLEPLUS_URL',
          'configuration_value' => 'Ins&eacute;rer l\'url du compte',
          'configuration_description' => 'Ins&eacute;rer un titre',
          'configuration_group_id' => '6',
          'sort_order' => '8',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_SORT_ORDER',
          'configuration_value' => '200',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULE_FOOTER_MULTI_TEMPLATE_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages où la boxe doit être présente.',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => 'clic_cfg_set_select_pages_list',
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
      return array('MODULE_FOOTER_MULTI_TEMPLATE_STATUS',
                   'MODULE_FOOTER_MULTI_TEMPLATE',
                   'MODULE_FOOTER_MULTI_TEMPLATE_CONTENT_WIDTH',
                   'MODULE_FOOTER_MULTI_TEMPLATE_MAILCHIMP_DISPLAY_PRIVACY',
                   'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_FACEBOOK_URL',
                   'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_TWITTER_URL',
                   'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_PINTEREST_URL',
                   'MODULE_FOOTER_MULTI_TEMPLATE_CONTENTS_GOOGLEPLUS_URL',
                   'MODULE_FOOTER_MULTI_TEMPLATE_SORT_ORDER',
                   'MODULE_FOOTER_MULTI_TEMPLATE_DISPLAY_PAGES'
                  );
    }
  }



