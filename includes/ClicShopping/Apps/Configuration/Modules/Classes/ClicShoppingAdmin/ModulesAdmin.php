<?php
/**
 * Class ModulesAdmin
 *
 * Provides functionality to manage and retrieve specific module types within the admin configuration.
 */

namespace ClicShopping\Apps\Configuration\Modules\Classes\ClicShoppingAdmin;

class ModulesAdmin
{
  /**
   * @param string|null $module_type
   * @return string|null
   */
  public function getSwitchModules(?string $module_type): ?string
  {
    $appModuleType = null;

    switch ($module_type) {
      case 'dashboard':
        $appModuleType = 'AdminDashboard';
        break;
      case 'header_tags':
        $appModuleType = 'HeaderTags';
        break;
      case 'payment':
        $appModuleType = 'Payment';
        break;

      case 'shipping':
        $appModuleType = 'Shipping';
        break;

      case 'order_total':
        $appModuleType = 'OrderTotal';
        break;
    }

    return $appModuleType;
  }
}