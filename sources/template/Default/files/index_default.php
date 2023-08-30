<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));
?>
  <section class="index" id="index">
    <div class="contentContainer">
      <div class="contentText">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_front_page'); ?>
      </div>
    </div>
  </section>
<?php
require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));