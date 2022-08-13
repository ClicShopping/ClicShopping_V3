<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\ConfigurationPopUpFields;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Save extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Products');
    }

    public function execute()
    {

      if (!empty($_POST['configuration'])) {
        $cKey = HTML::sanitize($_GET['cKey']);

        $array = $_POST['configuration'];

        if (isset($array['CONFIGURATION_PREFIX_MODEL'])) {
          $configuration_value = $array['CONFIGURATION_PREFIX_MODEL'];
        }

        if (isset($array['BAR_CODE_TYPE'])) {
          $configuration_value = $array['BAR_CODE_TYPE'];
        }

        if (isset($array['STOCK_REORDER_LEVEL'])) {
          $configuration_value = (int)$array['STOCK_REORDER_LEVEL'];
        }

        if (isset($array['MAX_MIN_IN_CART'])) {
          $configuration_value = (int)$array['MAX_MIN_IN_CART'];
        }

        $Qupdate = $this->app->db->prepare('update :table_configuration
                                            set configuration_value = :configuration_value,
                                                last_modified = now()
                                            where configuration_key = :configuration_key
                                           ');
        $Qupdate->bindValue(':configuration_value', $configuration_value);
        $Qupdate->bindValue(':configuration_key', $cKey);
        $Qupdate->execute();

        Cache::clear('configuration');

        echo '<div style="padding-top:0.5rem; color: #ee0500;">Data Saved</div>';
//    echo "From Server : ". json_encode($_POST)."<br />"; //debug
      } else {
        echo 'Error <br />';
      }

      exit;
    }
  }