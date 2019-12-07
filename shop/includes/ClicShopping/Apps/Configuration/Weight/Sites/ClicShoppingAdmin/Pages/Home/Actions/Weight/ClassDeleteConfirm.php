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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class ClassDeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Weight');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;
      $weight_class_from_id = HTML::sanitize($_GET['wID']);
      $weight_class_to_id = HTML::sanitize($_GET['tID']);

      $this->app->db->delete('weight_classes_rules', ['weight_class_from_id' => (int)$weight_class_from_id,
          'weight_class_from_id' => (int)$weight_class_to_id,
        ]
      );

      $this->app->db->delete('weight_classes', ['weight_class_id' => (int)$weight_class_from_id]);

      Cache::clear('weight-classes');
      Cache::clear('weight-rules');

      $this->app->redirect('Weight&page=' . $page);
    }
  }