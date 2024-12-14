<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


use ClicShopping\OM\HTML;

use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

/**
 * @param $customers_group_id
 * @return string
 */
function clic_cfg_set_customers_group_list_pull_down($customers_group_id)
{
  return HTML::selectMenu('configuration_value', GroupsB2BAdmin::getCustomersGroup(), (int)$customers_group_id);
}