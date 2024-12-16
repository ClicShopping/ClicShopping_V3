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
/**
 * Class ReviewsAdmin
 *
 * Provides administrative functionalities for managing customer reviews,
 * including counting tags, setting review statuses, managing sentiments,
 * and retrieving vote statistics.
 */
class ReviewsAdmin
{
  /**
   * Counts the total number of customer tags from the reviews table.
   *
   * @return int The total count of all customer tags across all reviews.
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
   * Checks if the number of customer tags exceeds the specified threshold.
   *
   * @return bool Returns true if the count of customer tags is greater than 300, otherwise false.
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
   * Updates the status of a review in the database based on the provided review ID and status.
   *
   * @param int $id The ID of the review to update.
   * @param int|null $status The new status to set for the review. Accepts 1 for active, 0 for inactive, or null for invalid status.
   * @return bool|int Returns true if the update is successful, -1 if the status is invalid.
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
   * Updates the approved status of a review's sentiment based on the provided status.
   *
   * @param int $id The ID of the review whose sentiment approved status needs to be updated.
   * @param int|null $status The status to set for the review's sentiment (1 for approved, 0 for not approved). Any other value results in a return value of -1.
   * @return bool|int Returns true on successful update, false on failure, and -1 if the status is invalid.
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
   * Retrieves the sentiment description based on the provided sentiment ID and language ID.
   *
   * @param int $id The ID of the sentiment to retrieve the description for.
   * @param int $language_id The ID of the language for the description. If not provided, the default language ID is used.
   * @return string The sentiment description corresponding to the given ID and language ID.
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
   * Retrieves the total number of "vote yes" entries from the reviews votes table.
   *
   * @return int The total count of "yes" votes for reviews.
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
   * Retrieves the total number of reviews where the vote is marked as "No".
   *
   * @return int The count of reviews with a "No" vote.
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
   * Retrieves the total number of positive sentiment votes for a given product based on its ID.
   *
   * @param int $products_id The ID of the product for which to count positive sentiment votes.
   * @return int The total number of votes with a positive sentiment for the specified product.
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
   * Retrieves the total count of negative sentiment votes (sentiment = 0 and reviews_id = 0)
   * for a given product.
   *
   * @param int $products_id The ID of the product for which to retrieve the vote count.
   * @return int The total number of negative sentiment votes for the specified product.
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