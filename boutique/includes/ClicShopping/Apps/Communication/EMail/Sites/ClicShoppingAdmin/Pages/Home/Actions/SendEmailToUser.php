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

  namespace ClicShopping\Apps\Communication\EMail\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class SendEmailToUser extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_EMail = Registry::get('EMail');

      $this->page->data['action'] = 'SendEmailToUser';

      $CLICSHOPPING_EMail->loadDefinitions('Sites/ClicShoppingAdmin/email');
    }
  }