<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use function is_null;

class RecommendationsAdmin
{
  private mixed $db;

  public function __construct()
  {
    $this->db = Registry::get('Db');
  }

  /**
   * @param float $productsRateWeight
   * @param float $reviewRate
   * @param float|null $userFeedback
   * @return float
   */
  private static function calculateRecommendationScoreWithMultipleSources(float $productsRateWeight = 0.8, float $reviewRate = 0, ?float $userFeedback = 0, ?float $sentimentScore = null): float
  {
    // Normalize the user feedback to a value between -1 and 1
    $userFeedback = static::calculateUserFeedbackScore($userFeedback);

    // If a sentiment score is provided, adjust it to be between -1 and 1
    if (!is_null($sentimentScore)) {
      $sentimentScore = max(-1, min(1, $sentimentScore));
    }

    // Get scores from other recommendation sources (e.g., sales data, external recommendations)
    $salesDataScore = 0.9; // Example: get the score from sales data
    $externalRecommendationScore = 0.85; // Example: get the score from external recommendations

    // Weigh the scores from different sources (adjust weights as needed)
    $salesDataWeight = 0.4;
    $externalRecommendationWeight = 0.3;

    // Calculate the combined score as a weighted sum of individual scores
    $combinedScore = ($reviewRate * $productsRateWeight) +
      ($salesDataScore * $salesDataWeight) +
      ($externalRecommendationScore * $externalRecommendationWeight) +
      ($userFeedback * 0.2); // Adjust the weight of user feedback as needed

    // If a sentiment score is available, incorporate it into the combined score calculation with a specific weight
    if (!is_null($sentimentScore)) {
      $sentimentWeight = (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_WEIGHTING_SENTIMENT;
      $combinedScore = $combinedScore + ($sentimentScore * $sentimentWeight);
    }

    return $combinedScore;
  }

  /**
   * @param float $productsRateWeight
   * @param float $reviewRate
   * @param float|null $userFeedback
   * @param float|null $feedbackWeight
   * @return float
   */
  public static function calculateRecommendationScoreBasedOnRange(float $productsRateWeight = 0.8, float $reviewRate = 0, ?float $userFeedback = 0, ?float $feedbackWeight, ?float $sentimentScore = null): float
  {
    if (is_null($feedbackWeight)) {
      $feedbackWeight = 0.2;
    }

    // Adjust the sentiment score to be between -1 and 1 (if provided)
    if (!is_null($sentimentScore)) {
      $sentimentScore = max(-1, min(1, $sentimentScore));
    }

    // Normalize the user feedback to a value between -1 and 1
    $userFeedback = static::calculateUserFeedbackScore($userFeedback);

    // If the review rate is low (e.g., 1), give more weight to the sentiment score
    if ($reviewRate <= 0.2) {
      $feedbackWeight = 0.7; // You can adjust this weight to your preference
    }

    // Calculate the final recommendation score using a weighted average of review rate, user feedback, and sentiment score (if available)
    $score = ($reviewRate * (1 - $feedbackWeight)) + ($userFeedback * $feedbackWeight);

    // If a sentiment score is available, incorporate it into the final score calculation with a specific weight
    if (!is_null($sentimentScore)) {
      $sentimentWeight = (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_WEIGHTING_SENTIMENT;
      $score = ($score + ($sentimentScore * $sentimentWeight)) / 2; // Adjust the weighting between the sentiment and other factors as needed
    }

    // Apply the products rate weight (from reviews) to the final score
    $score *= $productsRateWeight;

    return $score;
  }

  /**
   * @param float|null $productsRateWeight
   * @param float|null $reviewRate
   * @param int|null $userFeedback (Optional) User feedback on the recommended product, ranging from -1 (disliked) to 1 (liked).
   * @param string|null $strategy
   * @return float
   * Function to calculate the score for product recommendations
   */
  public function calculateRecommendationScore(?float $productsRateWeight = 0.8, ?float $reviewRate = 0,  int|null $userFeedback = 0, ?string $strategy = 'Range', ?float $sentimentScore = null): float
  {
    // Adjust the review rate to be between 0 and 1
    $maxReviewRate = 5; // Maximum possible review rate
    $reviewRate = $reviewRate / $maxReviewRate;

    // Normalize the user feedback to a value between -1 and 1
    $userFeedback = static::calculateUserFeedbackScore($userFeedback);

    if ($strategy == 'Range') {
      return static::calculateRecommendationScoreBasedOnRange($productsRateWeight, $reviewRate, $userFeedback, null, $sentimentScore);
    } else {
      return static::calculateRecommendationScoreWithMultipleSources($productsRateWeight, $reviewRate, $userFeedback, $sentimentScore);
    }

    return $score;
  }

  /**
   * @param int $limit
   * @return array
   */
  public function getMostRecommendedProducts(int $limit = 10, string|int $customers_group_id = 99, ?string $date): array
  {
    if ($customers_group_id == 99) {
      $customers_group = 'AND customers_group_id >= 0';
    } elseif ($customers_group_id > 0) {
      $customers_group = 'AND customers_group_id = ' . $customers_group_id;
    } else {
      $customers_group = 'AND customers_group_id = 0';
    }

    $minRecommendedScore = (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_MIN_SCORE;

    $currentDate = date('Y-m-d');
    if (empty($date)) {
      $date_analyse = '';
    } else {
      $date_analyse = ' between ' . $date . ' AND ' . $currentDate;
    }

    $QmostRecommended = $this->db->prepare('SELECT products_id, 
                                                     COUNT(*) as recommendation_count,
                                                     recommendation_date,
                                                     MAX(score) as score
                                              FROM :table_products_recommendations
                                              WHERE score >= :minRecommendedScore
                                                    ' . $customers_group . '
                                                    ' . $date_analyse . '
                                              GROUP BY products_id
                                              ORDER BY recommendation_count DESC
                                              LIMIT :limit
                                             ');

    $QmostRecommended->bindInt(':limit', $limit);
    $QmostRecommended->bindDecimal(':minRecommendedScore', $minRecommendedScore);

    $QmostRecommended->execute();

    $mostRecommendedProducts = $QmostRecommended->fetchAll();

    return $mostRecommendedProducts;
  }

  /**
   * Get the products that are frequently rejected by customers
   * @param int $limit (Optional) Limit the number of products to retrieve
   * @return array
   */
  public function getRejectedProducts(int $limit = 10, string|int $customers_group_id = 99, ?string $date): array
  {
    if ($customers_group_id == 99) {
      $customers_group = '';
    } elseif ($customers_group_id > 0) {
      $customers_group = ' AND customers_group_id = ' . $customers_group_id;
    } else {
      $customers_group = ' AND customers_group_id = 0';
    }

    $maxRejectedScore = (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_MAX_SCORE;

    $currentDate = date('Y-m-d');
    if (empty($date)) {
      $date_analyse = '';
    } else {
      $date_analyse = ' between ' . $date . ' AND ' . $currentDate;
    }

    $QrejectedProducts = $this->db->prepare('SELECT products_id, 
                                                      COUNT(*) as rejection_count,
                                                      recommendation_date as recommendation_date,
                                                      MAX(score) as score
                                              FROM :table_products_recommendations
                                              WHERE score < :maxRejectedScore
                                                    ' . $customers_group . '
                                                    ' . $date_analyse . '
                                              GROUP BY products_id
                                              ORDER BY rejection_count DESC
                                              LIMIT :limit
                                              ');

    $QrejectedProducts->bindInt(':limit', $limit);
    $QrejectedProducts->bindDecimal(':maxRejectedScore', $maxRejectedScore);
    $QrejectedProducts->execute();

    $rejectedProducts = $QrejectedProducts->fetchAll();

    return $rejectedProducts;
  }

  /**
   * subjective measure that reflects the user's opinion or sentiment about the product, between -1 and 1.
   *
   * @param float|null $userFeedback
   * @return float
   */
  public static function calculateUserFeedbackScore(?float $userFeedback): float
  {
    $normalizedFeedback = max(-1, min(1, $userFeedback));

    return $normalizedFeedback;
  }

//********************************************
// Review calculation
//********************************************
  /**
   * Calculate the average rating weight  of review ratings.
   *
   * @param int $products_id
   * @return float The average rating.
   */
  public function calculateProductsRateWeight(int $products_id): float
  {
    $Qcheck = $this->db->prepare('select avg(reviews_rating) as average
                                from :table_reviews
                                where products_id = :products_id
                              ');
    $Qcheck->bindInt(':products_id', $products_id);
    $Qcheck->execute();

    $review = $Qcheck->fetch();

    if (!$review || $review['average'] === null) {
      return 0;
    }

    $averageRating = $review['average'];

    return $averageRating;
  }
}