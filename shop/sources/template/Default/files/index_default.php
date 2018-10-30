<?php
/*
 * index_default.php
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 & MIT

*/

   require($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));
?>
 <section class="index" id="index">
    <div class="contentContainer">
      <div class="contentText">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_front_page'); ?>
      </div>
    </div>
 </section>
<?php
  require($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));