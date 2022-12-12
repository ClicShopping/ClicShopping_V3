<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\Customers;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class DeleteCustomers implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Reviews')) {
        Registry::set('Reviews', new ReviewsApp());
      }

      $this->app = Registry::get('Reviews');
    }

    /**
     * @param int $id
     */
    private function deleteCustomer(int $id) :void
    {
      if (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on')) {
        $Qreviews = $this->app->db->get('reviews', 'reviews_id', ['customers_id' => $id]);

        while ($Qreviews->fetch()) {
          $this->app->db->delete('reviews_description', ['reviews_id' => (int)$Qreviews->valueInt('reviews_id')]);
        }

        $this->app->db->delete('reviews', ['customers_id' => $id]);
      } else {
        $this->app->db->save('reviews', ['customers_id' => 'null'], ['customers_id' => $id]);
      }
    }

    public function execute()
    {
      if (isset($_GET['DeleteAll'])) {
        if (isset($_POST['selected'])) {
          foreach ($_POST['selected'] as $id) {
            $this->deleteCustomer($id);
          }
        } else {
          $id = HTML::sanitize($_POST['id']);
          $this->deleteCustomer($id);
        }
      }
    }
  }