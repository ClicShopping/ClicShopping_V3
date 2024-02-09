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

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin35;

define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();

CLICSHOPPING::loadSite('ClicShoppingAdmin');

$prompt = HTML::sanitize($_POST['message']);

$saveGpt = isset($_POST['saveGpt']) ? HTML::sanitize($_POST['saveGpt']) : null;
$result = ChatGptAdmin35::getGptResponse($prompt);

$pos = strstr($result, ':');

if ($pos !== false) {
  $result = substr($pos, 2); // Pour enlever les deux-points et l'espace
  echo nl2br($result);
} else {
  echo nl2br($result); // Si "Keywords:" n'est pas trouvé, imprimez la chaîne d'origine.
}
