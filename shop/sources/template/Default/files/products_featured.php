<?php
/*
 * products_featured.php
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 & MIT

*/
  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="featured" id="featured">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_featured'); ?>
    </div>
  </div>
</section>