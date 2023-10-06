<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class TemplateEmail extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');

    $this->page->setFile('template_email.php');
    $this->page->data['action'] = 'TemplateEmail';

    $CLICSHOPPING_TemplateEmail->loadDefinitions('Sites/ClicShoppingAdmin/TemplateEmail');
  }
}