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

  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Category = Registry::get('Category');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');

  $CLICSHOPPING_Language->loadDefinitions('index');

  if ($CLICSHOPPING_Category->getDepth() == 'products') {
    if ($CLICSHOPPING_Category->getCountCategoriesNested() > 0) {
      require_once($CLICSHOPPING_Template->getTemplateFiles('index_listing'));
    } else {
// nested
      require_once($CLICSHOPPING_Template->getTemplateFiles('index_categories'));
    }

  } elseif ($CLICSHOPPING_Category->getDepth() == 'nested' || (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id']))) {
//CATEGORIES Page  2nd level / Listing
    require_once($CLICSHOPPING_Template->getTemplateFiles('index_listing'));
//Index page
  } else {
    require_once($CLICSHOPPING_Template->getTemplateFiles('index_default'));
  }
