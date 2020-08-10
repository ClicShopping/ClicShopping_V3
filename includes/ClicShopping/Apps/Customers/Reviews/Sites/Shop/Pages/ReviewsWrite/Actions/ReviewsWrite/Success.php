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


  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsWrite\Actions\ReviewsWrite;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Success extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

      if (isset($_GET['ReviewsWrite']) && isset($_GET['Success'])) {
// templates
        $this->page->setFile('reviews_write_success.php');
//language
        $CLICSHOPPING_Language->loadDefinitions('product_reviews_write_success');
//Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('product_reviews_write_success');
      }
    }
  }