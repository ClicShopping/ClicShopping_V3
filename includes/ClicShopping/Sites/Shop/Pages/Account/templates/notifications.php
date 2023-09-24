<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Page = Registry::get('Site')->getPage();

require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));

$CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_notifications_updated'), 'success');

require_once($CLICSHOPPING_Page->data['content']);

require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));
