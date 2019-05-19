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

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_1');
    }


    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['cPath'])) {
        $current_category_id = HTML::sanitize($_GET['cPath']);
      } else {
        $current_category_id = 0;
      }

      $content = '<!-- Categories -->';
      $content .= '<div class="form-group row">';
      $content .= '<div class="col-md-2">' . $this->app->getDef('text_categories_name') . '</div>';

      if (isset($_GET['Insert'])) {
        $content .= '<div class="col-md-5">';
        $content .= '<label for="' . $this->app->getDef('text_products_categories') . '" class="col-5 col-form-label"></label>';
        $content .= '<div id="myAjax">';
        $content .= HTML::selectMenu('move_to_category_id[]', $CLICSHOPPING_CategoriesAdmin->getCategoryTree(), $current_category_id);
        $content .= '</div>';
        $content .= HTML::hiddenField('current_category_id', $current_category_id);
        $content .= '<a href="' . $this->app->link('CategoriesPopUp') . '"  data-toggle="modal" data-refresh="true" data-target="#myModal">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/create.gif', $this->app->getDef('text_create')) . '</a>';
        $content .= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        $content .= '<div class="modal-dialog">';
        $content .= '<div class="modal-content">';
        $content .= '<div class="modal-body"><div class="te"></div></div>';
        $content .= '</div> <!-- /.modal-content -->';
        $content .= '</div><!-- /.modal-dialog -->';
        $content .= '</div><!-- /.modal -->';
        $content .= '</div>';
        $content .= '</div>';
      } else {
        $content .= '<div class="col-md-5">';
        $content .= HTML::selectMenu('move_to_category_id[]', $CLICSHOPPING_CategoriesAdmin->getCategoryTree(), $current_category_id, 'multiple="multiple" size="10"');
        $content .= HTML::hiddenField('current_category_id', $current_category_id);
        $content .= '</div>';
        $content .= '<div class="col-md-5">';
        $content .= $this->app->getDef('text_select_category_action') . ' <br />';
        $content .= HTML::radioField('copy_as', 'none', true) . ' ' . $this->app->getDef('text_copy_as_none') . '<br />';
        $content .= HTML::radioField('copy_as', 'link') . ' ' . $this->app->getDef('text_copy_as_link') . '<br />';
        $content .= HTML::radioField('copy_as', 'duplicate') . ' ' . $this->app->getDef('text_copy_as_duplicate') . '<br />';
        $content .= HTML::radioField('copy_as', 'move') . ' ' . $this->app->getDef('text_copy_as_move') . '<br />';
        $content .= '</div>';
      }

      $content .= '</div>';
      $content .= '<!-- End Categories -->';

      $categories_ajax = CLICSHOPPING::link('ajax/products_categories.php');

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Categories Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow1').append(
    '{$content}'
);
</script>

<script type="text/javascript">
  jQuery(document).ready(function() {
    $("#myAjax").on('click', function () {
      var selectedOptionVal = $('#category_id').val();
      $.ajax({
        url: '{$categories_ajax}',
        dataType: 'json',
        success: function (data) {
          //data returned from php
          var options_html = '';
          for (var index in data) {
            var category_id = data[index]['id'];
            var category_name = data[index]['text'];
            var selectedString = category_id == selectedOptionVal ? ' selected="selected"' : '';
            options_html += '<option value="' + category_id + '"' + selectedString + '>' + category_name + '</option>';
          }
          $('#category_id').html(options_html);
        }
      });
    });
  })
</script>

<!-- ######################## -->
<!--  End Categories App      -->
<!-- ######################## -->

EOD;
      return $output;

    }
  }