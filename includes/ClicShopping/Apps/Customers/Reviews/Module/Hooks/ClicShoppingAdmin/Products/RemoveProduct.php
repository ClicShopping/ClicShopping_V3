<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;
use function is_null;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Reviews')) {
      Registry::set('Reviews', new ReviewsApp());
    }

    $this->app = Registry::get('Reviews');
  }

/**
* @param int|null $id
* @return void
 */
  private function removeReviews(int|null $id): void
  {
    if (!is_null($id)) {
      $Qreviews = $this->app->db->get('reviews', 'reviews_id', ['products_id' => $id]);

      if ($Qreviews->fetch()) {
        $this->app->db->delete('reviews', ['products_id' => $id]);

        while ($Qreviews->fetch()) {
          $this->app->db->delete('reviews_description', ['reviews_id' => $Qreviews->valueInt('reviews_id')]);
        }
      }
    }
  }

/**
* @param int|null $id
* @return void
 */
  private function removeReviewsSentiment(int|null $id): void
  {
    if (!is_null($id)) {
      $QreviewsSentiment = $this->app->db->get('reviews_sentiment', 'id', ['products_id' => $id]);

      if ($QreviewsSentiment->fetch()) {
        $this->app->db->delete('reviews', ['products_id' => $id]);

        while ($QreviewsSentiment->fetch()) {
          $this->app->db->delete('reviews_sentiment_description', ['id' => $QreviewsSentiment->valueInt('id')]);
        }
      }
    }
  }

  public function execute()
  {
    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        $this->removeReviews($id);
        $this->removeReviewsSentiment($id);
      }
    } elseif (isset($_POST['products_id'])) {
      $id = HTML::sanitize($_POST['products_id']);
      $this->removeReviews($id);
      $this->removeReviewsSentiment($id);
    }
  }
}