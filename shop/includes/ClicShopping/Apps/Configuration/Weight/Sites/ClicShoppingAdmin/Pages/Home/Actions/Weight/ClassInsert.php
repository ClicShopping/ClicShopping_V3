<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Configuration\Weight\Sites\ClicShoppingAdmin\Pages\Home\Actions\Weight;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  class ClassInsert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Weight');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;
      $weight_class_id = HTML::sanitize($_POST['weight_class_id']);
      $weight_class_to_id = HTML::sanitize($_POST['weight_class_to_id']);
      $weight_class_rule = $_POST['weight_class_rule'];

      if (isset($weight_class_id)) {
        $sql_data_array = ['weight_class_rule' => (float)$weight_class_rule];

        $insert_sql_data = ['weight_class_from_id' => (int)$weight_class_id,
          'weight_class_to_id' => (int)$weight_class_to_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('weight_classes_rules', $sql_data_array);

        Cache::clear('weight-classes');
        Cache::clear('weight-rules');
      }

      $this->app->redirect('Weight&page=' . $page);
    }
  }