<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
?>

<html dir="ltr" lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo 'Conditions of sales'; ?></title>
  </head>
  <body onload="resize();">
    <?php echo $QconditionGeneralOfSales->value('page_manager_general_condition'); ?>
  </body>
</html>

