<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class ReviewsAdmin
{
  /**
   * @return int
   */
  public static function countCustomersTags(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qreviews = $CLICSHOPPING_Db->prepare('select customers_tag
                                               from :table_reviews
                                              ');
    $Qreviews->execute();

    $reviews_array = $Qreviews->fetchAll();
    $total = 0;

    foreach ($reviews_array as $value) {
      $customer_tag_array = explode(',', $value['customers_tag']);
      $total += count($customer_tag_array);
    }

    return $total;
  }

  /**
   * @return bool
   * If it's toolong, the response from gpt can give an error
   */
  public static function CountTagCountWarning(): bool
  {
    if (self::countCustomersTags() > 300) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Status reviews  - Sets the status of a reviews products
   *
   * @param int $id , reviews_id
   * @param int|null $status
   * @return string status on or off
   */
  public static function getReviewsStatus(int $id,  int|null $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('reviews', [
        'status' => 1,
        'last_modified' => 'now()'
      ],
        ['reviews_id' => (int)$id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('reviews', ['status' => 0,
        'last_modified' => 'now()'
      ],
        ['reviews_id' => (int)$id]
      );

    } else {
      return -1;
    }
  }

  /**
   * Status reviews  - Sets the status of a reviews products
   *
   * @param int $id , reviews_id
   * @param int|null $status
   * @return string status on or off
   */
  public static function getReviewsSentimentApprovedStatus(int $id,  int|null $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('reviews_sentiment', [
        'sentiment_approved' => 1,
        'date_modified' => 'now()'
      ],
        ['reviews_id' => (int)$id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('reviews_sentiment', [
        'sentiment_approved' => 0,
        'date_modified' => 'now()'
      ],
        ['reviews_id' => (int)$id]
      );

    } else {
      return -1;
    }
  }

  /**
   * @param int $id
   * @param int $language_id
   * @return string
   */
  public static function getSentimentDescription(int $id, int $language_id): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

    $Qdescription = $CLICSHOPPING_Db->prepare('select description
                                            from :table_reviews_sentiment_description
                                            where id = :id
                                            and language_id = :language_id
                                          ');

    $Qdescription->bindInt(':id', $id);
    $Qdescription->bindInt(':language_id', $language_id);

    $Qdescription->execute();

    return $Qdescription->value('description');
  }

/**
* @return int
 */
  public static function getTotalReviewsVoteYes(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qvote = $CLICSHOPPING_Db->prepare('select count(vote) as vote_yes
                                        from :table_reviews_vote
                                        where vote = 1
                                        and reviews_id <> 0
                                        ');

    $Qvote->execute();

    return $Qvote->valueInt('vote_yes');
  }

  /**
   * @return int
   */
  public static function getTotalReviewsVoteNo(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qvote = $CLICSHOPPING_Db->prepare('select count(vote) as vote_no
                                            from :table_reviews_vote
                                            where vote = 0
                                            and reviews_id <> 0
                                        ');

    $Qvote->execute();

    return $Qvote->valueInt('vote_no');
  }


  /**
   * @param int $products_id
   * @return int
   */
  public static function getTotalReviewsSentimentVoteYes(int $products_id): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qvote = $CLICSHOPPING_Db->prepare('select count(vote) as vote_yes
                                        from :table_reviews_vote
                                        where sentiment = 1
                                        and reviews_id = 0
                                        and products_id = :products_id
                                        ');

    $Qvote->bindInt(':products_id', $products_id);
    $Qvote->execute();

    return $Qvote->valueInt('vote_yes');
  }

  /**
   * @param int $products_id
   * @return int
   */
  public static function getTotalReviewsSentimentVoteNo(int $products_id): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qvote = $CLICSHOPPING_Db->prepare('select count(vote) as vote_no
                                        from :table_reviews_vote
                                        where sentiment = 0
                                        and reviews_id = 0
                                        and products_id = :products_id
                                      ');
    $Qvote->bindInt(':products_id', $products_id);
    $Qvote->execute();

    return $Qvote->valueInt('vote_no');
  }
}