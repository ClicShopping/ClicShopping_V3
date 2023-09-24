<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Classes\Common;

use function count;
use function in_array;

class CategoryCommon
{

  /*
  * Parse and secure the cPath parameter values
  * @int, $cPath, value of cpath
  * return @ string array $tmp_array
  */
  public function getParseCategoryPath(string $cPath): array
  {
// make sure the category IDs are integers
    $cPath_array = array_map(function ($string) {
      return (int)$string;
    }, explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = [];
    $n = count($cPath_array);

    for ($i = 0; $i < $n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }
}
