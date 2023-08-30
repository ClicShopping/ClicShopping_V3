<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Template = Registry::get('Template');

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<div class="clearfix"></div>
<div class="separator"></div>
<section class="product_reviews_info" id="product_reviews_info">
  <div class="contentContainer">
    <div class="contentText">
      <div class="row m-1">
        <div class="col-md-12">
          <div class="page-title"><h4><?php echo CLICSHOPPING::getDef('heading_title_reviews'); ?></h4></div>
          <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews'); ?>
        </div>
      </div>
    </div>
  </div>
</section>

