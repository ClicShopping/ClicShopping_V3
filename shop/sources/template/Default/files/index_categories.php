<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;


  require($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));
  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
  <section class="index_categories" id="index_categories">
    <div class="contentContainer">
      <div class="contentText">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_index_categories'); ?>
      </div>
    </div>
  </section>
<?php
  require($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));
