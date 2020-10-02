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

  namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

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
    private function removeReviews(int $id)
    {
      $Qreviews = $this->app->db->get('reviews', 'reviews_id', ['products_id' => id]);

      if ($Qreviews->fetch()) {
        $this->app->db->delete('reviews', ['products_id' => $id]);

        while ($Qreviews->fetch()) {
          $this->app->db->delete('reviews_description', ['reviews_id' => $Qreviews->valueInt('reviews_id')]);
        }
      }
    }

    public function execute()
    {
      $id = HTML::sanitize($_POST['products_id']);
      $this->removeReviews($id);
    }
  }