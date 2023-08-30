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
require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
  <section class="index_categories" id="index_categories">
    <div class="contentContainer">
      <div class="contentText">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_index_categories'); ?>
      </div>
    </div>
  </section>
<?php
require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));
