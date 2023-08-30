<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


require_once('includes/application.php');

$page_contents = 'install.php';

if (isset($_GET['step']) && is_numeric($_GET['step'])) {
  switch ($_GET['step']) {
    case '2':
      $page_contents = 'install_2.php';
      break;

    case '3':
      $page_contents = 'install_3.php';
      break;

    case '4':
      $page_contents = 'install_4.php';
      break;
  }
}

require_once('templates/main_page.php');
