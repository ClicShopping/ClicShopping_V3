<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;
  use ClicShopping\Apps\Marketing\ProductRecommendations\Classes\ClicShoppingAdmin\ProductsRecommendationsAdmin;
  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Image = Registry::get('Image');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_ProductRecommendations = Registry::get('ProductRecommendations');

  Registry::set('ProductsRecommendationsAdmin', new ProductsRecommendationsAdmin());
  $CLICSHOPPING_ProductsRecommendationsAdmin = Registry::get('ProductsRecommendationsAdmin');

  if (!Registry::exists('ProductsAdmin')) {
    Registry::set('ProductsAdmin', new ProductsAdmin());
  }

  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  $action = $_GET['action'] ?? '';

  $languages = $CLICSHOPPING_Language->getLanguages();

  $customers_group = GroupsB2BAdmin::getAllGroups();
  $customers_group_name = '';

  foreach ($customers_group as $value) {
    $customers_group_name .= '<option value="' . $value['id'] . '">' . $value['text'] . '</option>';
  } // end empty action

   if(isset($_POST['product_limit'])) {
     $limit = HTML::sanitize($_POST['product_limit']);
   } else {
     $limit = 10;
   }

  if(isset($_POST['score'])){
    $rejection_score = HTML::sanitize($_POST['score']);
  }else {
    $rejection_score = 0.5;
  }

  If (isset($_POST['date'])) {
    $date = HTML::sanitize($_POST['date']);
  } else {
    $date = '';
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <div class="col-md-1 logoHeading">
            <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/product_recommendations.png', $CLICSHOPPING_ProductRecommendations->getDef('heading_title'), '40', '40'); ?>
          </div>
          <div class="col-md-2 pageHeading">
            <?php echo '&nbsp;' . $CLICSHOPPING_ProductRecommendations->getDef('heading_title'); ?>
          </div>
          <div class="col-md-8">
            <?php echo HTML::form('grouped', $CLICSHOPPING_ProductRecommendations->link('ProductRecommendations')); ?>
            <div class="form-group">
              <div class="row">
                <div class="col-md-3">
                  <?php
                  if (MODE_B2B_B2C == 'True') {
                    echo $CLICSHOPPING_ProductRecommendations->getDef('text_customers_group');

                    if (isset($_POST['customers_group_id'])) {
                      $customers_group_id = HTML::sanitize($_POST['customers_group_id']);
                    } else {
                      $customers_group_id = 99;
                    }

                    echo HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getAllGroups(), $customers_group_id);
                  }
                  ?>
                </div>
                <div class="col-md-2">
                  <?php
                  echo $CLICSHOPPING_ProductRecommendations->getDef('text_display_limit');
                  echo HTML::inputField('product_limit', $limit, 'id="product_limit"');
                  ?>
                </div>
                <div class="col-md-2">
                  <?php
                  echo $CLICSHOPPING_ProductRecommendations->getDef('text_rejection_score');
                  echo HTML::inputField('score', $rejection_score, 'id="score"');
                  ?>
                </div>
                <div class="col-md-2">
                  <?php
                  echo $CLICSHOPPING_ProductRecommendations->getDef('text_start_date_analysis');
                  echo HTML::inputField('date', null, 'id="score"', 'date');
                  ?>
                </div>
                <div class="col-md-1 form-group text-end">
                  <?php echo HTML::button($CLICSHOPPING_ProductRecommendations->getDef('text_ok'), null, null, 'primary'); ?>
                </div>
                <div class="col-md-2 form-group text-end">
                  <?php
                  if (isset($_POST['product_limit'])) {
                    echo HTML::button($CLICSHOPPING_ProductRecommendations->getDef('button_reset'), null, $CLICSHOPPING_ProductRecommendations->link('ProductRecommendations'), 'warning');
                  }
                  ?>
                </div>
              </div>
            </div>
            </form>
          </div>

          <div class="col-md-1 text-end">
            <?php
            if (!isset($_POST['product_limit'])) {
              echo HTML::button($CLICSHOPPING_ProductRecommendations->getDef('button_configure'), null, $CLICSHOPPING_ProductRecommendations->link('Configure'), 'primary');
            }
            ?>
          </div>
        </div>
      </div>

    </div>



  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <?php echo $CLICSHOPPING_Hooks->output('ProductRecommendations', 'StatsReviews'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="separator"></div>
  <div class="col-md-12">
    <div class="alert alert-warning text-center" role="alert">
      <?php
        if (CLICSHOPPING_APP_PRODUCT_RECOMMENDATIONS_PR_STRATEGY == 'Range') {
          echo $CLICSHOPPING_ProductRecommendations->getDef('text_range');
        } else {
          echo $CLICSHOPPING_ProductRecommendations->getDef('text_multiple');
        }
      ?>
  </div>
  </div>

  <div class="separator"></div>
  <div id="ProductRecommendationsTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li class="nav-item"><a href="#tab1" role="tab" data-bs-toggle="tab" class="nav-link active"><?php echo $CLICSHOPPING_ProductRecommendations->getDef('tab_analytics'); ?></a></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <div class="tab-pane active" id="tab1">
          <div class="mainTitle"><?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_analytics'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <!-- Analytics -->
              <div class="mt-4">
                <div class="row">
                  <div class="col-md-6">
                    <h3><?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_most_recommended_products'); ?></h3>
                      <table class="table table-bordered">
                        <thead>
                          <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_products'); ?></td>
                          <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_recommendations'); ?></td>
                          <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_score'); ?></td>
                        </thead>
                        <tbody>
                      <?php
                      $mostRecommendedProducts = $CLICSHOPPING_ProductsRecommendationsAdmin->getMostRecommendedProducts($limit, $rejection_score, $customers_group_id, $date);

                      foreach ($mostRecommendedProducts as $product) {
                        $productId = $product['products_id'];
                        $productName = $CLICSHOPPING_ProductsAdmin->getProductsName($productId, $CLICSHOPPING_Language->getId());
                        $recommendationCount = $product['recommendation_count'];
                        $score = $product['score'];
                      ?>
                        <tr>
                          <td><?php echo $productName; ?></td>
                          <td><?php echo $recommendationCount; ?></td>
                          <td><?php echo $score; ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <h3><?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_most_rejected_products'); ?></h3>
                    <table class="table table-bordered">
                      <thead>
                        <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_products'); ?></td>
                        <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_rejected'); ?></td>
                        <td><?php echo $CLICSHOPPING_ProductRecommendations->getDef('table_heading_score'); ?></td>
                      </thead>
                      <tbody>
                      <?php
                      $rejectedProducts = $CLICSHOPPING_ProductsRecommendationsAdmin->getRejectedProducts($limit, $rejection_score, $customers_group_id, $date);

                      foreach ($rejectedProducts as $product) {
                        $productId = $product['products_id'];
                        $productName = $CLICSHOPPING_ProductsAdmin->getProductsName($productId, $CLICSHOPPING_Language->getId());
                        $rejectionCount = $product['rejection_count'];
                        $rejectionScore = $product['score'];
                      ?>
                        <tr>
                          <td><?php echo $productName; ?></td>
                          <td><?php echo $rejectionCount; ?></td>
                          <td><?php echo $rejectionScore; ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="separator"></div>
              <div class="col-md-12">
                <div class="row">
                  <span class="alert alert-info">
                    <i class="bi bi-question-circle" title="<?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_help'); ?>"></i> <?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_help'); ?>
                    <?php echo $CLICSHOPPING_ProductRecommendations->getDef('text_help_description'); ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php echo $CLICSHOPPING_Hooks->output('ProductRecommendations', 'ProductRecommendationContentTab', null, 'display'); ?>
      </div>
    </div>
  </div>
</div>
