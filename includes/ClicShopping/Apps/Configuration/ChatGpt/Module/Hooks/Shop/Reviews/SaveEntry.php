<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\Shop\Reviews;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop\ChatGptShop35;
use ClicShopping\Apps\Customers\reviews\Classes\Shop\ReviewsClass;

class saveEntry implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $productsCommon;
  protected mixed $reviewsShop;

  public function __construct()
  {
    $this->productsCommon = Registry::get('ProductsCommon');
    Registry::set('ReviewsClass', new ReviewsClass());

    $this->reviewsShop = Registry::get('ReviewsClass');
  }

  /**
   * @return int|bool
   */
  private static function getReviewsId(): int|bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $products_id = HTML::sanitize($_GET['products_id']);

    $Qreviews = $CLICSHOPPING_Db->prepare('select reviews_id
                                              from :table_reviews
                                              where products_id = :products_id
                                              order by reviews_id desc
                                              limit 1
                                             ');
    $Qreviews->bindInt('products_id', $products_id);
    $Qreviews->execute();

    $result = $Qreviews->ValueInt('reviews_id');

    if (empty($result)) {
      return false;
    }

    return $result;
  }

  /**
   * @return string|bool
   */
  private static function getCustomerReviews(): string|bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qreviews = $CLICSHOPPING_Db->prepare('select rd.reviews_text
                                              from :table_reviews r,
                                                   :table_reviews_description rd
                                              where r.reviews_id = rd.reviews_id
                                              and rd.languages_id = :languages_id
                                              and r.reviews_id = :reviews_id
                                             ');

    $Qreviews->bindInt(':reviews_id', self::getReviewsId());
    $Qreviews->bindInt(':languages_id', $CLICSHOPPING_Language->getId());

    $Qreviews->execute();

    $result = $Qreviews->value('reviews_text');

    if (empty($result)) {
      return false;
    }

    return $result;
  }

  /**
   * @param int $id
   * @param string $tag
   */
  private static function saveReviews(int $id, string $tag): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $sql_array = ['customers_tag' => $tag];
    $update_array = ['reviews_id' => $id];

    $CLICSHOPPING_Db->save('reviews', $sql_array, $update_array);
  }

  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    if (!\defined('CLICSHOPPING_APP_REVIEWS_RV_STATUS') || CLICSHOPPING_APP_REVIEWS_RV_STATUS == 'False') {
      return false;
    }

    if (ChatGptShop35::checkGptStatus() === false) {
      return false;
    }

    if (CLICSHOPPING_APP_REVIEWS_RV_SENTIMENT_TAG == 'False') {
      return false;
    }

    $customer_review = self::getCustomerReviews();

    if ($customer_review === false) {
      return false;
    }

    $language_name = $CLICSHOPPING_Language->getLanguagesName($CLICSHOPPING_Language->getId());

    $question = 'Provide up in ' . $language_name . ' to 6 comma-separated tags indicating the sentiment of the customer review. Please exclude the prompt\'s response and any other unrelated information. The customer review : ' . $customer_review;

    $tag = ChatGptShop35::getGptResponse($question, 15, 0.7);

    if (self::getReviewsId() !== false && !empty($tag)) {
      self::saveReviews(self::getReviewsId(), $tag);
    }
  }
}