<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));
require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
  <section class="index_listing" id="index_listing">
    <div class="contentContainer">
      <div class="contentText">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_listing'); ?>
      </div>
    </div>
  </section>
<?php
require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));