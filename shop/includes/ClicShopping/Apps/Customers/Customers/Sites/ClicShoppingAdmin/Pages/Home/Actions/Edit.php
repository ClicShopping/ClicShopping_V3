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

  namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Edit extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Customers = Registry::get('Customers');

      $this->page->setFile('edit.php');
      $this->page->data['action'] = 'Update';

      $CLICSHOPPING_Customers->loadDefinitions('Sites/ClicShoppingAdmin/customers');

      $Qcustomers = $CLICSHOPPING_Customers->db->prepare('select c.*,
                                                                 a.*
                                                          from :table_customers c left join :table_address_book a on c.customers_default_address_id = a.address_book_id
                                                          where a.customers_id = c.customers_id
                                                          and c.customers_id = :customers_id
                                                        ');
      $Qcustomers->bindInt(':customers_id', (int)$_GET['cID']);
      $Qcustomers->execute();

// if the customer does'nt exist (deleted), redirect in customer page
      if ($Qcustomers->fetch() === false) {
        $CLICSHOPPING_Customers->redirect('Customers');
      }
    }
  }