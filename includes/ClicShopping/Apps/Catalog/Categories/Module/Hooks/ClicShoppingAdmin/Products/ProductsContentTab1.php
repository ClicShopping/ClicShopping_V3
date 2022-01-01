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
    protected mixed $app;

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

      if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['cPath'])) {
        $current_category_id = HTML::sanitize($_GET['cPath']);
      } else {
        if (isset($_GET['pID'])) {
          $QproductsCategories = $this->app->db->prepare('select categories_id
                                                          from :table_products_to_categories
                                                          where products_id = :products_id
                                                          limit 1
                                                        ');
          $QproductsCategories->bindInt('products_id', $_GET['pID']);
          $QproductsCategories->execute();

          $current_category_id = $QproductsCategories->valueInt('categories_id');
        } else {
          $current_category_id = 0;
        }
      }

      $category_tree = $CLICSHOPPING_CategoriesAdmin->getCategoryTree();

      $content = '<!-- Categories -->';
      $content .= '<div class="form-group row">';
      $content .= '<div class="col-md-2">' . $this->app->getDef('text_categories_name') . '</div>';

      if (isset($_GET['Insert'])) {
        $content .= '<div class="col-md-5">';
        $content .= '<label for="' . $this->app->getDef('text_products_categories') . '" class="col-5 col-form-label"></label>';
        $content .= '<div id="myAjax">';
        $content .= HTML::selectMenu('move_to_category_id[]', $category_tree, $current_category_id, 'id="move_to_category_id"');
        $content .= '</div>';
        $content .= HTML::hiddenField('current_category_id', $current_category_id);
        $content .= '<a href="' . $this->app->link('CategoriesPopUp') . '"  data-bs-toggle="modal" data-bs-refresh="true" data-bs-target="#myModal">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/create.gif', $this->app->getDef('text_create')) . '</a>';
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
        $content .= HTML::selectMenu('move_to_category_id[]', $category_tree, $current_category_id, 'multiple="multiple" size="10"');
        $content .= HTML::hiddenField('current_category_id', $current_category_id);
        $content .= '</div>';
        $content .= '<div class="col-md-5">';
        $content .= $this->app->getDef('text_select_category_action') . ' <br />';
        $content .= '<div class="custom-control custom-radio">';
        $content .= HTML::radioField('copy_as', 'none', true, 'class="custom-control-input" id="copy_as_none" name="copy_categories"');
        $content .= '<label class="custom-control-label" for="copy_as_none">' . $this->app->getDef('text_copy_as_none') . '</label>';
        $content .= '</div>';
        $content .= '<div class="custom-control custom-radio">';
        $content .= HTML::radioField('copy_as', 'link', null, 'class="custom-control-input" id="copy_as_link" name="copy_categories"');
        $content .= '<label class="custom-control-label" for="copy_as_link">' . $this->app->getDef('text_copy_as_link') . '</label>';
        $content .= '</div>';
        $content .= '<div class="custom-control custom-radio">';
        $content .= HTML::radioField('copy_as', 'duplicate', null, 'class="custom-control-input" id="copy_as_duplicate" name="copy_categories"');
        $content .= '<label class="custom-control-label" for="copy_as_duplicate">' . $this->app->getDef('text_copy_as_duplicate') . '</label>';
        $content .= '</div>';
        $content .= '<div class="custom-control custom-radio">';
        $content .= HTML::radioField('copy_as', 'move', null, 'class="custom-control-input" id="copy_as_move" name="copy_categories"');
        $content .= '<label class="custom-control-label" for="copy_as_move">' . $this->app->getDef('text_copy_as_move') . '</label>';
        $content .= '</div>';
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

<script>
  window.addEventListener("DOMContentLoaded", (event) => {
   console.log("DOM uploaded and analysed");
   document.querySelector('#myAjax')
   document.querySelector("div")
   .addEventListener('click',function(e){
     let selectedOptionVal = document.querySelector('#move_to_category_id').value
     ,options_html="";
    fetch("{$categories_ajax}?"+selectedOptionVal)
      .then(function(response) {
         return response.json();
      })
      .then(function(jsonResponse) {
     // Ajax success
     console.log("data is :",jsonResponse);
     for(const index in jsonResponse){
      let category_id = jsonResponse[index].id;
      let category_name = jsonResponse[index].text;
      let selectedString = category_id == selectedOptionVal ? ' selected="selected"' : '';
      options_html += '<option value="' + category_id + '"' + selectedString + '>' + category_name + '</option>';
     }
     $('#move_to_category_id').html(options_html);
      })
      .catch(function(err) {
       // error ajax 
        alert("error :"+err);
    });
   });
  });
 </script>
<!-- ######################## -->
<!--  End Categories App      -->
<!-- ######################## -->

EOD;
      return $output;

    }
  }
