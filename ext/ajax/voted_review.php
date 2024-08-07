<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . DIRECTORY_SEPARATOR);

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');

spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();
CLICSHOPPING::loadSite('Shop');

if (isset($_POST['reviewId'], $_POST['product_id'])) {
  $CLICSHOPPING_Db = Registry::get('Db');

  if (!is_null($_POST['reviewId'])) {
    $reviews_id = HTML::sanitize($_POST['reviewId']);
  } else {
    $reviews_id = 0;
  }

  $products_id = HTML::sanitize($_POST['product_id']);
  $vote = HTML::sanitize($_POST['vote']);
  $customer_id = HTML::sanitize($_POST['customer_id']);

  if ($reviews_id === 0) {
    $array = [
      'products_id' => (int)$products_id,
      'reviews_id' => 0,
      'vote' => (int)$vote,
      'customer_id' => (int)$customer_id,
      'sentiment' => (int)$vote
    ];
  } else {
    $array = [
      'products_id' => (int)$products_id,
      'reviews_id' => (int)$reviews_id,
      'vote' => (int)$vote,
      'customer_id' => (int)$customer_id,
      'sentiment' => 0
    ];
  }

  $CLICSHOPPING_Db->save('reviews_vote', $array);
}