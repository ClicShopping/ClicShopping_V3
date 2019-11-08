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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

  $id = HTML::sanitize($_GET['pages_id']);
  $page = $CLICSHOPPING_PageManagerShop->pageManagerDisplayInformation($id);
  $page_title = $CLICSHOPPING_PageManagerShop->pageManagerDisplayTitle($id);

  const HEADING_TITLE = '';
  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="information" id="information">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title pageManagerHeader">
        <h1><?php echo $page_title; ?></h1>
      </div>
      <div class="pageManager"><?php echo $page; ?></div>
      <div class="separator"></div>
    </div>
  </div>
</section>