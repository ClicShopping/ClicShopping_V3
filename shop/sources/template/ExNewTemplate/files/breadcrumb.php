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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Service = Registry::get('Service');
  $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
?>

  <div class="separator"></div>
  <div class="contentContainer">
    <div class="contentText">

<?php
  if (defined('MODULE_HEADER_BREADCRUMP_STATUS')) {
    if (MODULE_HEADER_BREADCRUMP_STATUS == 'False' || empty(ClicShopping::getDef('module_header_breadcrump_title'))) {
?>
      <div class="separator"></div>
      <div class="col-md-12 breadcrumb">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li>
<?php
      if ($CLICSHOPPING_Service->isStarted('Breadcrumb')) {
        echo $CLICSHOPPING_Breadcrumb->get(' &raquo; ');
      }
?>
            </li>
          </ol>
	</nav>
      </div>
<?php
      if (!CLICSHOPPING::getBaseNameIndex() && $CLICSHOPPING_ProductsCommon->getID()) {
?>
      <div class="page-header">
        <h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1>
      </div>
<?php
      }
    }
  } else {
?>
      <div class="separator"></div>
      <div class="col-md-12 breadcrumb">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li>
<?php
    if ($CLICSHOPPING_Service->isStarted('Breadcrumb')) {
      echo $CLICSHOPPING_Breadcrumb->get(' &raquo; ');
    }
?>
              </span>
            </li>
          </ol>
        </nav>
      </div>
<?php
    if (!CLICSHOPPING::getBaseNameIndex() && $CLICSHOPPING_ProductsCommon->getID()) {
?>
      <div class="page-header">
        <h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1>
      </div>
<?php
    }
  }
?>
    </div>
  </div>
