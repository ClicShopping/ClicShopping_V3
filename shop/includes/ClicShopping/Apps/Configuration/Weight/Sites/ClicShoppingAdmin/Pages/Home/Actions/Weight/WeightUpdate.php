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

  class WeightUpdate extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Weight');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $languages = $CLICSHOPPING_Language->getLanguages();

      $weight_class_key = HTML::sanitize($_POST['weight_class_key']);
      $weight_class_id = HTMl::sanitize($_POST['weight_class_id']);

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $weight_class_title_array = HTML::sanitize($_POST['weight_class_title']);
        $language_id = $languages[$i]['id'];

        $weight_class_title_array = HTML::sanitize($weight_class_title_array[$language_id]);

        $sql_data_array = ['weight_class_title' => $weight_class_title_array,
          'weight_class_key' => $weight_class_key
        ];

        $this->app->db->save('weight_classes', $sql_data_array, ['weight_class_id' => (int)$weight_class_id,
            'language_id' => (int)$language_id
          ]
        );
      }

      Cache::clear('weight-classes');
      Cache::clear('weight-rules');

      $this->app->redirect('Weight&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }