<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\Cookies\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Cookies extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_Language = Registry::get('Language');

// templates
    $this->page->setFile('cookies.php');
//Content
    $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('cookie_usage');
//language
    $CLICSHOPPING_Language->loadDefinitions('cookie_usage');


    $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Info&Cookies'));

  }
}
