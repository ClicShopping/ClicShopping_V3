<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Classes\Shop;

use ClicShopping\OM\Registry;

class ProductsAutomation
{
  /**
   * @return mixed
   */
  private static function getProductAverageScore(): mixed
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

    $QmostRecommended = $CLICSHOPPING_Db->prepare('SELECT pr.products_id, 
                                                          avg(pr.score) as average_score
                                                    FROM :table_products_recommendations pr
                                                    WHERE pr.products_id = :products_id
                                                    GROUP BY pr.products_id
                                                   ');

    $QmostRecommended->bindDecimal(':products_id', $CLICSHOPPING_ProductsCommon->getID());

    $QmostRecommended->execute();

    return $QmostRecommended->valueDecimal('average_score');
  }

  //********************************************
  // Favorites Automation
  //********************************************

  /**
   * @return int|null
   */
  public static function favorites():  int|null
  {
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

    $avg_core = self::getProductAverageScore();

    if ($avg_core > (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_FAVORITES_MIN_SCORE) {
      return self::createFavorites($CLICSHOPPING_ProductsCommon->getID());
    } elseif ($avg_core < 0)  {
      return self::deleteFavorites($CLICSHOPPING_ProductsCommon->getID());
    } else {
      return -1;
    }
  }

  /**
   * @param int $id
   * @return bool
   */
  private static function createFavorites(int $id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->get('products_favorites', 'products_id', ['products_id' => $id]);

    if ($Qresult->fetch() === false) {
      $sql_array = [
        'products_id' => $id,
        'products_favorites_date_added' => 'now()',
        'scheduled_date' => null,
        'expires_date' => null,
        'status' => 1
      ];

      $result = $CLICSHOPPING_Db->save('products_favorites', $sql_array);

      return $result !== false; // Return true for success, false for failure
    }

    return false;
  }

  /**
   * @param int $id
   * @return bool
   */
  private static function deleteFavorites(int $id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->get('products_favorites', ['products_id' => $id]);

    if ($Qresult->fetch() === true) {
      $sql_array = [
        'products_id' => $id,
      ];

      $result = $CLICSHOPPING_Db->delete('products_favorites', $sql_array);

      return $result !== false; // Return true for success, false for failure
    }

    return false;
  }


  //********************************************
  // featured Automation
  //********************************************

  /**
   * @return int|null
   */
  public static function featured():  int|null
  {
    $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

    $avg_core = self::getProductAverageScore();

    if ($avg_core > (float)CLICSHOPPING_APP_RECOMMENDATIONS_PR_FEATURED_MIN_SCORE) {
      return self::createFeatured($CLICSHOPPING_ProductsCommon->getID());
    } elseif ($avg_core < 0)  {
      return self::deleteFeatured($CLICSHOPPING_ProductsCommon->getID());
    } else {
      return -1;
    }
  }

  /**
   * @param int $id
   * @return bool
   */
  private static function createFeatured(int $id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->get('products_featured', 'products_id', ['products_id' => $id]);

    if ($Qresult->fetch() === false) {
      $sql_array = [
        'products_id' => $id,
        'products_featured_date_added' => 'now()',
        'scheduled_date' => null,
        'expires_date' => null,
        'status' => 1
      ];

      $result = $CLICSHOPPING_Db->save('products_featured', $sql_array);

      return $result !== false; // Return true for success, false for failure
    }

    return false;
  }

  /**
   * @param int $id
   * @return bool
   */
  private static function deleteFeatured(int $id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->get('products_featured', ['products_id' => $id]);

    if ($Qresult->fetch() === true) {
      $sql_array = [
        'products_id' => $id,
      ];

      $result = $CLICSHOPPING_Db->delete('products_featured', $sql_array);

      return $result !== false; // Return true for success, false for failure
    }

    return false;
  }
}