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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_1');
    }

    private function getSupplier()
    {
      if (isset($_GET['pID'])) {
        $pID = HTML::sanitize($_GET['pID']);

        $Qproducts = $this->app->db->prepare('select suppliers_id
                                              from :table_products
                                              where products_id = :products_id
                                            ');
        $Qproducts->bindInt(':products_id', HTML::sanitize($pID));

        $Qproducts->execute();

        $Qsuppliers = $this->app->db->prepare('select suppliers_id,
                                                       suppliers_name
                                                from :table_suppliers
                                                where suppliers_id = :suppliers_id
                                              ');
        $Qsuppliers->bindInt(':suppliers_id', $Qproducts->valueInt('suppliers_id'));
        $Qsuppliers->execute();

        $result = $Qsuppliers->fetchAll();

        return $result;
      }
    }

    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      $suppliers = $this->getSupplier();

      if (is_array($suppliers) && count($suppliers) > 0) {
        $suppliers_id = $suppliers[0]['suppliers_id'];
        $suppliers_name = $suppliers[0]['suppliers_name'];
      } else {
        $suppliers_id = null;
        $suppliers_name = null;
      }

      $content = '<!-- Link trigger modal -->';
      $content .= '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_suppliers') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_suppliers') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::inputField('suppliers_id', $suppliers_id . ' ' . $suppliers_name, 'id="supplier" class="token-input form-control"', null, null, null);
      $content .= '<a href="' . $this->app->link('SuppliersPopUp') . '"  data-toggle="modal" data-refresh="true" data-target="#myModal">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/create.gif', $this->app->getDef('text_create')) . '</a>';
      $content .= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
      $content .= '<div class="modal-dialog">';
      $content .= '<div class="modal-content">';
      $content .= '<div class="modal-body"><div class="te"></div></div>';
      $content .= '</div> <!-- /.modal-content -->';
      $content .= '</div><!-- /.modal-dialog -->';
      $content .= '</div><!-- /.modal -->';
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $suppliers_ajax = CLICSHOPPING::link('ajax/suppliers.php');


      $output = <<<EOD
<!-- ######################## -->
<!--  Start Supplier Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow2').append(
    '{$content}'
);
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $("#supplier").tokenInput("{$suppliers_ajax}" ,
        {
          tokenLimit: 1,
          resultsLimit: 5,
          onResult: function (results) {
            $.each(results, function (index, value) {
              value.name = value.id + " " + value.name;
            });
            return results;
          }

        });
  });
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $("#supplier").tokenInput("{$suppliers_ajax}", {
      prePopulate: [
        {
          id: {$suppliers_id},
          name: "{$suppliers_id} {$suppliers_name}"
        }
      ],
      tokenLimit: 1
    });
  });
</script>

<!-- ######################## -->
<!--  End Supplier App      -->
<!-- ######################## -->

EOD;
      return $output;

    }
  }