<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class PasswordForgotten extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_Language = Registry::get('Language');

// templates
    $this->page->setFile('password_forgotten.php');
//Content
    $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('password_forgotten');
//language
    $CLICSHOPPING_Language->loadDefinitions('password_forgotten');

    $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Account&LogIn'));
    $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link(null, 'Account&PasswordForgotten'));
  }
}