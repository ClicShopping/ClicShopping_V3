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

  class WeightInsert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Weight');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
      $CLICSHOPPING_Language = Registry::get('Language');
      $languages = $CLICSHOPPING_Language->getLanguages();

      $QlastId = $this->app->db->prepare('select weight_class_id 
                                          from :table_weight_classes 
                                          order by weight_class_id desc
                                          limit 1
                                         ');
      $QlastId->execute();

      $weight_class_id = $QlastId->valueInt('weight_class_id') + 1;
      $weight_class_key = $_POST['weight_class_key'];

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $weight_class_title_array = HTML::sanitize($_POST['weight_class_title']);
        $language_id = $languages[$i]['id'];

        $weight_class_title_array = HTML::sanitize($weight_class_title_array[$language_id]);

        $sql_data_array = ['weight_class_title' => $weight_class_title_array];

        $insert_sql_data = [
          'weight_class_key' => $weight_class_key,
          'weight_class_id' => (int)$weight_class_id,
          'language_id' => (int)$languages[$i]['id']
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('weight_classes', $sql_data_array);
      }

      Cache::clear('weight-classes');
      Cache::clear('weight-rules');

      $this->app->redirect('Weight&page=' . $page);
    }
  }