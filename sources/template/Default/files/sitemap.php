<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="index" id="index">
  <div class="contentContainer">
    <div class="contentText">
      <div class="d-flex flex-wrap">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_sitemap'); ?>
      </div>
      <div class="separator"></div>
    </div>
  </div>
</section>
