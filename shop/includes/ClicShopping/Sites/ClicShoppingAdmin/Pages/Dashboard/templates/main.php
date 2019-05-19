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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  require_once('calcul_statistics.php');
?>
<!-- header_eof //-->
<!-- body //-->
<div class="contentBody dashboard">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="card-title">
          <div class="row col-md-12" style="padding-top:5px">
            <span
              class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/home.gif', CLICSHOPPING::getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo CLICSHOPPING::getDef('heading_title'); ?></span>
            <span class="col-md-6 text-md-right">
                  <?php echo HTML::link(CLICSHOPPING::link(CLICSHOPPING::link(null, 'A&Catalog\Categories&Categories')), null, 'class="btn btn-primary btn-sm" role="button"><span class="fas fa-list-alt" title="' . CLICSHOPPING::getDef('heading_short_categories') . '"'); ?>
                  <?php echo HTML::link(CLICSHOPPING::link(CLICSHOPPING::link(null, 'A&Catalog\Products&Products')), null, 'class="btn btn-info btn-sm" role="button"><span class="fab fa-product-hunt" title="' . CLICSHOPPING::getDef('heading_short_products') . '"'); ?>
                  <?php echo HTML::link(CLICSHOPPING::link(CLICSHOPPING::link(null, 'A&Orders\Orders&Orders')), null, 'class="btn btn-success btn-sm" role="button"><span class="fas fa-bookmark" title="' . CLICSHOPPING::getDef('heading_short_orders') . '"'); ?>
                  <?php echo HTML::link(CLICSHOPPING::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers')), null, 'class="btn btn-warning btn-sm" role="button"><span class="fas fa-user" title="' . CLICSHOPPING::getDef('heading_short_customers') . '"'); ?>
                </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="d-flex flex-wrap justify-content-center">

        <?php
          $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/Dashboard/';

          if (is_dir($source_folder)) {
          $files_get = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'IndexDashboardTop*');

          if (is_array($files_get)) {
            foreach ($files_get as $value) {
              if (!empty($value['name'])) {
                echo $CLICSHOPPING_Hooks->output('Dashboard', $value['name']);
              }
            }

            echo '<div class="separator"></div>';
          }
        ?>
      </div>
    </div>
    <?php
      echo $CLICSHOPPING_Hooks->output('IndexDashboardTop', 'PageTabContent', null, 'display');
      }
    ?>
  </div>
  <div class="col-md-12">
    <span class="col-md-8 float-md-left">
      <div class="row">
        <div class="col-md-12">
          <div class="d-flex flex-wrap">
<?php
  if (defined('MODULE_ADMIN_DASHBOARD_INSTALLED') && !is_null(MODULE_ADMIN_DASHBOARD_INSTALLED)) {
    $adm_array = explode(';', MODULE_ADMIN_DASHBOARD_INSTALLED);

    if (!empty(MODULE_ADMIN_DASHBOARD_INSTALLED)) {

      $col = 0;

      foreach ($adm_array as $adm) {
        if (strpos($adm, '\\') !== false) {
          $class = Apps::getModuleClass($adm, 'AdminDashboard');
        }

        $ad = new $class();

        if ($ad->isEnabled()) {
          echo $ad->getOutput();
        }
      }
    } else {
      echo '<div class="alert alert-primary">';
      echo '<div class="col-md-12 text-md-center">' . HTML::link(CLICSHOPPING::link(null, 'A&Configuration\Modules&Modules&set=dashboard&list=new'), ClicShopping::getDef('text_install_dashboard')) . '<br /></div>';
      echo '<div class="col-md-12 text-md-center">' . ClicShopping::getDef('text_good_luck') . '</div>';
      echo '</div>';
    }
  }
?>
        </div>
      </div>
    </div>
  </span>
    <span class="col-md-4 float-md-right" id="indexTabs" style="overflow: auto;">
<style>
  .nav {
    width: 400px;
  }
</style>
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab" class="nav">
      <li class="nav-item"><a href="#tab1" role="tab" data-toggle="tab"
                              class="nav-link active"><?php echo CLICSHOPPING::getDef('tab_statistics'); ?></a></li>
      <li class="nav-item"><a href="#tab3" role="tab" data-toggle="tab"
                              class="nav-link"><?php echo CLICSHOPPING::getDef('tab_customer'); ?></a></li>
    </ul>

    <div class="tabsClicShopping">
      <div class="tab-content">
<!-- ------------------------------------------------------------ //-->
        <!--          ONGLET Statistics                                   //-->
        <!-- ------------------------------------------------------------ //-->
        <div class="tab-pane active" id="tab1">
          <div class="mainTitle"><?php echo CLICSHOPPING::getDef('title_index_order'); ?></div>
          <div
            class="adminformTitle backgroundBlank"><?php echo $CLICSHOPPING_Hooks->output('Dashboard', 'ActionStatsCountStatus'); ?></div>
          <div class="mainTitle"><?php echo CLICSHOPPING::getDef('heading_title_divers'); ?></div>
          <div class="adminformTitle backgroundBlank">
            <div id="tab1Content"></div>
<?php
  echo $CLICSHOPPING_Hooks->output('StatsDashboard', 'PageTabContent', null, 'display');
?>
          </div>
        </div>
        <!-- ------------------------------------------------------------ //-->
        <!--          ONGLET Information                                    //-->
        <!-- ------------------------------------------------------------ //-->
        <div class="tab-pane" id="tab3">
          <div class="mainTitle"><?php echo CLICSHOPPING::getDef('title_customer_information'); ?></div>
          <div
            class="backgroundBlank"><?php echo $CLICSHOPPING_Hooks->output('Dashboard', 'ActionInformation'); ?></div>
        </div>
      </div>
       <?php echo $CLICSHOPPING_Hooks->output('Index', 'PageTabTwitter', null, 'display'); ?>
    </div>
    <div><?php echo $CLICSHOPPING_Hooks->output('Dashboard', 'ActionLinkedin'); ?></div>
    <div><?php echo $CLICSHOPPING_Hooks->output('Dashboard', 'ActionDonate'); ?></div>
  </span>
    <div class="clearfix"></div>
  </div>
</div>
<!-- footer //-->
<!--[if IE]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/excanvas.min.js"></script><![endif]-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js"></script>
