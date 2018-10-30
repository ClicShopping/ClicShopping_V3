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

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ConfigurationPopUpFields extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Products = Registry::get('Products');

      $this->page->setUseSiteTemplate(false); //don't display Header / Footer
      $this->page->setFile('configuration_popup_fields.php');
      $this->page->data['action'] = 'ConfigurationPopUpFields';

      $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/configuration_popup_fields');
    }
  }