<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Weight\Sites\ClicShoppingAdmin\Pages\Home\Actions\Weight;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ClassDeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Weight');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
    $weight_class_from_id = HTML::sanitize($_GET['wID']);
    $weight_class_to_id = HTML::sanitize($_GET['tID']);

    $sql_array = [
      'weight_class_from_id' => (int)$weight_class_from_id,
      'weight_class_from_id' => (int)$weight_class_to_id
    ];

    $this->app->db->delete('weight_classes_rules', $sql_array);
    $this->app->db->delete('weight_classes', ['weight_class_id' => (int)$weight_class_from_id]);

    Cache::clear('weight-classes');
    Cache::clear('weight-rules');

    $this->app->redirect('Weight&page=' . $page);
  }
}