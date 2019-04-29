<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\CategoriesAdmin;

  require_once('../includes/application_top.php');

  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

   $array = $CLICSHOPPING_CategoriesAdmin->getCategoryTree();

# JSON-encode the response
    $json_response = json_encode($array); //Return the JSON Array

# Return the response
    echo $json_response;
