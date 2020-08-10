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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Manufacturers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $db;

    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Manufacturers/page_content_tab_1');
    }

    /**
     * @return int
     */
    private function getSuppliersId() :int
    {
      $QsuppliersID = $this->app->db->prepare('select suppliers_id 
                                               from :table_manufacturers
                                               where manufacturers_id = :manufacturers_id
                                             ');
      $QsuppliersID->bindInt('manufacturers_id', $_GET['mID']);
      $QsuppliersID->execute();

      $suppliers_id = $QsuppliersID->valueInt('suppliers_id');

      return $suppliers_id;
    }

    public function display() :string
    {
      if (!defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      If (isset($_GET['mID'])) {
        $suppliers_id = $this->getSuppliersId();
      }

      $Qsuppliers = $this->app ->db->prepare('select suppliers_id,
                                                     suppliers_name
                                               from :table_suppliers
                                               order by suppliers_name
                                              ');
      $Qsuppliers->execute();

      $count = $Qsuppliers->rowCount();

      $output = '';

      if ($count > 0) {
        $suppliers_name_array[] = ['id' => 0,
          'text' => CLICSHOPPING::getDef('text_select')
        ];

        while ($Qsuppliers->fetch()) {
          $suppliers_name_array[] = ['id' => $Qsuppliers->valueInt('suppliers_id'),
            'text' => $Qsuppliers->value('suppliers_name')
          ];
        }

        $output .= '<div class="separator"></div>';
        $output .= '<div class="row" id="supplierName">';
        $output .= '<label for="code" class="col-2 col-form-label">' . $this->app->getDef('text_suppliers_suppliers_name') . '</label>';
        $output .= '<div class="col-md-5">';
        $output .= HTML::selectField('suppliers_id', $suppliers_name_array, $suppliers_id ?? 0);
        $output .= '</div>';
        $output .= '</div>';
      }

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Suppliers Hooks      -->
<!-- ######################## -->
<script>
$('#manufacturersLanguage').append(
    '{$output}'
);
</script>
<!-- ######################## -->
<!--  End Suppliers hoosk      -->
<!-- ######################## -->
EOD;

      return $output;
    }
  }