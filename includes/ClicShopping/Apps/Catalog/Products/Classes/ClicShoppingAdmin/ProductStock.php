<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Upload;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class ProductStock
  {
    /**
     * Calculate safety stock levels based on historical demand variability, lead time, and desired service level.
     *
     * @param array $historicalDemand An array of historical demand data.
     * @param int $leadTime The lead time in days.
     * @param float $serviceLevel The desired service level (default is 0.95, which corresponds to 95% service level).
     * @param float $standardDeviationFactor The factor used to adjust the influence of standard deviation (default is 1.65).
     * @return float|int The calculated safety stock level.
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
     * @param $p
     * @param $mean
     * @param $stddev
     * @return float|int calculate the inverse of the standard normal cumulative distribution function
     * calculate the inverse of the standard normal cumulative distribution function
     */
    private static function norMinv($p, $mean, $stddev):  float|int
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
     * @param int|string|null $products_id
     * @param int|null $leadTime
     * @return float|false
     */
    public static function getHistoricalCustomerDemandByProducts(int|string $products_id = null, int $leadTime = null): float|false
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (\is_null($leadTime)) {
        $leadTime = (int)SAFETY_STOCK_TIME;
      }

      if (isset($products_id) && !\is_null($products_id)) {
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