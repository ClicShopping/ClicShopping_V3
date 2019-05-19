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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers as ManufacturersApp;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Manufacturers')) {
        Registry::set('Manufacturers', new ManufacturersApp());
      }

      $this->app = Registry::get('Manufacturers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_1');
    }

    private function getManufacturer()
    {
      if (isset($_GET['pID'])) {
        $Qproducts = $this->app->db->prepare('select manufacturers_id
                                              from :table_products
                                              where products_id = :products_id
                                            ');
        $Qproducts->bindInt(':products_id', HTML::sanitize($_GET['pID']));

        $Qproducts->execute();

        $Qmanufacturers = $this->app->db->prepare('select manufacturers_id,
                                                           manufacturers_name
                                                    from :table_manufacturers
                                                    where manufacturers_id = :manufacturers_id
                                                  ');
        $Qmanufacturers->bindInt(':manufacturers_id', $Qproducts->valueInt('manufacturers_id'));
        $Qmanufacturers->execute();

        $result = $Qmanufacturers->fetchAll();

        return $result;
      }
    }

    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!defined('CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS') || CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS == 'False') {
        return false;
      }

      $manufacturer = $this->getManufacturer();

      if (is_array($manufacturer) && count($manufacturer) > 0) {
        $manufacturers_id = $manufacturer[0]['manufacturers_id'];
        $manufacturers_name = $manufacturer[0]['manufacturers_name'];
      } else {
        $manufacturers_id = null;
        $manufacturers_name = '';
      }

      $content = '<!-- Link trigger modal -->';
      $content .= '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_manufacturer') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_manufacturer') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::inputField('manufacturers_id', $manufacturers_id . ' ' . $manufacturers_name, 'id="manufacturer" class="token-input form-control"', null, null, null);
      $content .= '<a href="' . $this->app->link('ManufacturersPopUp') . '"  data-toggle="modal" data-refresh="true" data-target="#myModal">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/create.gif', $this->app->getDef('text_create')) . '</a>';
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

      $smanufacturers_ajax = CLICSHOPPING::link('ajax/manufacturers.php');


      $output = <<<EOD
<!-- ######################## -->
<!--  Start Manufacturer Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow2').append(
    '{$content}'
);
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $("#manufacturer").tokenInput("{$smanufacturers_ajax}" ,
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
    $("#manufacturer").tokenInput("{$smanufacturers_ajax}", {
      prePopulate: [
        {
          id: {$manufacturers_id},
          name: "{$manufacturers_id}  {$manufacturers_name} "
        }
      ],
      tokenLimit: 1
    });
  });
</script>

<!-- ######################## -->
<!--  End Manufacturer App      -->
<!-- ######################## -->

EOD;
      return $output;

    }
  }