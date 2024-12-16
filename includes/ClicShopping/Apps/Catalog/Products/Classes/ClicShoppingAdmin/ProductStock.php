<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use function is_null;
/**
 * Class ProductStock
 *
 * This class provides functionalities related to calculating product stock levels,
 * including safety stock calculations based on historical demand and other inventory factors.
 */
class ProductStock
{
  /**
   * Calculates the safety stock based on historical demand, lead time, desired service level,
   * and standard deviation factor.
   *
   * @param array $historicalDemand An array of historical demand values used to calculate the mean and standard deviation.
   * @param int $leadTime The lead time in relevant time units (e.g., days, weeks) for replenishment.
   * @param float $serviceLevel Optional parameter specifying the desired service level as a probability (default is 0.95).
   * @param float $standardDeviationFactor Optional parameter representing the Z-score or factor for standard deviation calculation (default is 1.65).
   * @return float|int The calculated safety stock value, rounded to meet specified parameters.
   */
  private static function calculateSafetyStock(array $historicalDemand, int $leadTime, float $serviceLevel = 0.95, float $standardDeviationFactor = 1.65): float|int
  {
    // Calculate the mean (average) of historical demand
    $meanDemand = array_sum($historicalDemand) / count($historicalDemand);

    // Calculate the standard deviation of historical demand
    $standardDeviation = 0;
    foreach ($historicalDemand as $demand) {
      $standardDeviation += pow($demand - $meanDemand, 2);
    }
    $standardDeviation = sqrt($standardDeviation / count($historicalDemand));

    // Calculate the safety stock using the formula: Safety Stock = (Z-score * Standard Deviation * sqrt(Lead Time)) + Mean Demand
    $zScore = abs(static::norMinv((1 - $serviceLevel) / 2, 0, 1));
    $safetyStock = ($zScore * $standardDeviation * sqrt($leadTime)) + $meanDemand;

    return $safetyStock;
  }

  /**
   * Calculates the inverse of the normal cumulative distribution function (CDF).
   *
   * @param float $p The probability at which to evaluate the inverse normal CDF. Must be in the range (0, 1).
   * @param float $mean The mean (μ) of the normal distribution.
   * @param float $stddev The standard deviation (σ) of the normal distribution. Must be positive.
   * @return float|int The value x such that the cumulative distribution function equals $p. The result is a float or an integer based on the computation.
   */
  private static function norMinv($p, $mean, $stddev): float|int
  {
    $b1 = 0.319381530;
    $b2 = -0.356563782;
    $b3 = 1.781477937;
    $b4 = -1.821255978;
    $b5 = 1.330274429;
    $p_low = 0.02425;
    $p_high = 1 - $p_low;

    if ($p < $p_low) {
      $q = sqrt(-2 * log($p));
      return (((((($b5 * $q) + $b4) * $q) + $b3) * $q + $b2) * $q + $b1) / $p_high;
    } elseif ($p <= $p_high) {
      $q = $p - 0.5;
      $r = $q * $q;
      return (((((($b5 * $r) + $b4) * $r) + $b3) * $r + $b2) * $q + $b1) * $stddev + $mean;
    } else {
      $q = sqrt(-2 * log(1 - $p));
      return -(((((($b5 * $q) + $b4) * $q) + $b3) * $q + $b2) * $q + $b1) / $p_high;
    }
  }

  /**
   * Retrieves the historical customer demand for a specified product and calculates the safety stock based on the lead time.
   *
   * @param int|string|null $products_id The ID of the product for which historical demand is to be calculated. Can be null.
   * @param int|null $leadTime The lead time for calculating safety stock. Defaults to a predefined constant if null.
   *
   * @return float|false Returns the calculated safety stock as a float, or false if the product ID is not set or if there is an error during calculation.
   */
  public static function getHistoricalCustomerDemandByProducts(int|string $products_id = null, int $leadTime = null): float|false
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_null($leadTime)) {
      $leadTime = (int)SAFETY_STOCK_TIME;
    }

    if (isset($products_id) && !is_null($products_id)) {
      $QhistoricalDemand = $CLICSHOPPING_Db->get('orders_products', ['products_id', 'products_quantity'], ['products_id' => (int)$products_id]);

      $historicalDemand = $QhistoricalDemand->toArray();

      if (is_array($historicalDemand)) {
        $safetyStock = self::calculateSafetyStock($historicalDemand, $leadTime);

        return round($safetyStock, 2);
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}