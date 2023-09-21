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

chdir('../../../');

require('includes/application_top.php');

ob_start();

CLICSHOPPING::redirect();

// Afficher le contenu du buffer
ob_end_flush();
