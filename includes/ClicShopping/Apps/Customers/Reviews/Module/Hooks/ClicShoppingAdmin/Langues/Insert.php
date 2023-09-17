<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;
use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;
use function defined;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected mixed $lang;

  public function __construct()
  {
    if (!Registry::exists('Reviews')) {
      Registry::set('Reviews', new ReviewsApp());
    }

    $this->app = Registry::get('Reviews');
    $this->lang = Registry::get('Language');
  }

  /**
   * @return void
   */
  private function insert(): void
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();

    $Qreviews = $this->app->db->prepare('select r.reviews_id as orig_review_id,
                                                 rd.*
                                          from :table_reviews p left join :table_reviews_description rd on r.reviews_id = rd.reviews_id
                                          where rd.language_id = :language_id
                                          ');

    $Qreviews->bindInt(':language_id', $this->lang->getId());
    $Qreviews->execute();

    while ($Qreviews->fetch()) {
      $cols = $Qreviews->toArray();

      $cols['reviews_id'] = $cols['orig_review_id'];
      $cols['language_id'] = (int)$insert_language_id;

      unset($cols['orig_review_id']);

      $this->app->db->save('reviews_description', $cols);
    }

// Table review_sentiment
    $QreviewsSentiment = $this->app->db->prepare('select r.id as orig_id,
                                                         rd.*
                                                  from :table_reviews_sentiment r left join :table_reviews_sentiment_description pd on r.id = rd.id
                                                  where rd.language_id = :language_id
                                                  ');

    $QreviewsSentiment->bindInt(':language_id', $this->lang->getId());
    $QreviewsSentiment->execute();

    while ($QreviewsSentiment->fetch()) {
      $cols = $QreviewsSentiment->toArray();

      $cols['id'] = $cols['orig_id'];
      $cols['language_id'] = (int)$insert_language_id;

      unset($cols['orig_id']);

      $this->app->db->save('reviews_sentiment_description', $cols);
    }
  }

  public function execute()
  {
    if (!defined('CLICSHOPPING_APP_REVIEWS_RV_STATUS') || CLICSHOPPING_APP_REVIEWS_RV_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}