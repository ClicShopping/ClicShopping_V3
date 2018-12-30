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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\PasswordForgotten;

  use ClicShopping\OM\Registry;

  class Success extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');
// templates
      $this->page->setFile('password_forgotten_success.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('password_forgotten_success');
//language
      $CLICSHOPPING_Language->loadDefinitions('password_forgotten_success');
    }
  }