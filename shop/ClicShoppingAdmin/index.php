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

  require_once('includes/application_top.php');

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  if (CLICSHOPPING::hasSitePage()) {
    if (CLICSHOPPING::isRPC() === false) {

      $page_file = CLICSHOPPING::getSitePageFile();

      if (empty($page_file) || !is_file($page_file)) {
        $page_file = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/error_documents/404.php';
      }

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('header.php'));
      }

      include($page_file);

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('footer.php'));
      }
    }
    goto main_sub3;
  }

  main_sub3: // Sites and Apps skip to here

  require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('application_bottom.php'));
