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

class WeightUpdate extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Weight');
  }

  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $languages = $CLICSHOPPING_Language->getLanguages();

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
    $weight_class_key = HTML::sanitize($_POST['weight_class_key']);
    $weight_class_id = HTML::sanitize($_POST['weight_class_id']);

    for ($i = 0, $n = \count($languages); $i < $n; $i++) {
      $weight_class_title_array = HTML::sanitize($_POST['weight_class_title']);
      $language_id = $languages[$i]['id'];

      $weight_class_title_array = HTML::sanitize($weight_class_title_array[$language_id]);

      $sql_data_array = [
        'weight_class_title' => $weight_class_title_array,
        'weight_class_key' => $weight_class_key
      ];

      $this->app->db->save('weight_classes', $sql_data_array, ['weight_class_id' => (int)$weight_class_id,
          'language_id' => (int)$language_id
        ]
      );
    }

    Cache::clear('weight-classes');
    Cache::clear('weight-rules');

    $this->app->redirect('Weight&page=' . $page);
  }
}