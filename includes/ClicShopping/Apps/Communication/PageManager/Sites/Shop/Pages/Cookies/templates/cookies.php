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

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Template = Registry::get('Template');

  require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));

  require_once($CLICSHOPPING_Page->data['content']);

  require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));
