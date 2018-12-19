<?php
/*
 * products_favorites.php
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 & MIT

*/
  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="favorites" id="favorites">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_favorites'); ?>
    </div>
  </div>
</section>
