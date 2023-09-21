<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Settings\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class SettingsPopUp extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Settings = Registry::get('Settings');

    $this->page->setUseSiteTemplate(false); //don't display Header / Footer
    $this->page->setFile('settings_popup.php');

    $CLICSHOPPING_Settings->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}