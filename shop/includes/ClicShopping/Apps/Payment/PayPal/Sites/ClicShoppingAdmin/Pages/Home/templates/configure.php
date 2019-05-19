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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_PayPal = Registry::get('PayPal');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $current_module = $CLICSHOPPING_Page->data['current_module'];

  $CLICSHOPPING_PayPal_Config = Registry::get('PayPalAdminConfig' . $current_module);

  require_once(__DIR__ . '/template_top.php');
?>

  <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="appPayPalToolbar">
    <li class="nav-item">
      <?php
        foreach ($CLICSHOPPING_PayPal->getConfigModules() as $m) {
          if ($CLICSHOPPING_PayPal->getConfigModuleInfo($m, 'is_installed') === true) {
            echo '<li class="nav-link active" data-module="' . $m . '"><a href="' . $CLICSHOPPING_PayPal->link('Configure&module=' . $m) . '">' . $CLICSHOPPING_PayPal->getConfigModuleInfo($m, 'short_title') . '</a></li>';
          }
        }
      ?>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
         aria-expanded="false">Install</a>
      <div class="dropdown-menu">
        <?php
          foreach ($CLICSHOPPING_PayPal->getConfigModules() as $m) {
            if ($CLICSHOPPING_PayPal->getConfigModuleInfo($m, 'is_installed') === false) {
              echo '<a class="dropdown-item" href="' . $CLICSHOPPING_PayPal->link('Configure&module=' . $m) . '">' . $CLICSHOPPING_PayPal->getConfigModuleInfo($m, 'title') . '</a>';
            }
          }
        ?>
      </div>
    </li>
  </ul>

  <script>
      if ($('#appPayPalToolbar li.dropdown div.dropdown-menu').length === 0) {
          $('#appPayPalToolbar li.dropdown').hide();
      }

      $(function () {
          var active = '<?php echo ($CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'is_installed') === true) ? $current_module : 'new'; ?>';

          if (active !== 'new') {
              $('#appPayPalToolbar li[data-module="' + active + '"]').addClass('active');
          } else {
              $('#appPayPalToolbar div.dropdown').addClass('active');
          }
      });
  </script>

<?php
  if ($CLICSHOPPING_PayPal_Config->is_installed === true) {
    foreach ($CLICSHOPPING_PayPal_Config->req_notes as $rn) {
      echo '<div class="alert alert-warning" role="alert"><p>' . $rn . '</p></div>';
    }
    ?>
    <form name="paypalConfigure"
          action="<?php echo $CLICSHOPPING_PayPal->link('Configure&Process&module=' . $current_module); ?>"
          method="post">

      <div class="card" id="ppAccountBalanceLive">
        <div class="card-header">
          <?php echo $CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'title'); ?>
        </div>
        <div class="adminformTitle">
          <div class="card-block">
            <p class="card-text">
              <?php
                foreach ($CLICSHOPPING_PayPal_Config->getInputParameters() as $cfg) {
                  echo '<div>' . $cfg . '</div>';
                  echo '<div class="separator"></div>';
                }
              ?>
            </p>
          </div>
        </div>
        <div class="separator"></div>
        <div class="col-md-12">
          <?php
            echo HTML::button($CLICSHOPPING_PayPal->getDef('button_save'), null, null, 'success');

            if ($CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'is_uninstallable') === true) {
              echo '<span class="float-md-right">' . HTML::button($CLICSHOPPING_PayPal->getDef('button_dialog_uninstall'), null, '#', 'warning', ['params' => 'data-toggle="modal" data-target="#ppUninstallModal"']) . '</span>';
            }
          ?>
        </div>
      </div>
    </form>
    <?php
    if ($CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'is_uninstallable') === true) {
      ?>

      <div id="ppUninstallModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              <h4 class="modal-title"><?php echo $CLICSHOPPING_PayPal->getDef('dialog_uninstall_title'); ?></h4>
            </div>
            <div class="modal-body">
              <?php echo $CLICSHOPPING_PayPal->getDef('dialog_uninstall_body'); ?>
            </div>
            <div class="modal-footer">
              <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_uninstall'), null, $CLICSHOPPING_PayPal->link('Configure&Uninstall&module=' . $current_module), 'danger'); ?>
              <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_cancel'), null, '#', 'warning', ['params' => 'data-dismiss="modal"']); ?>
            </div>
          </div>
        </div>
      </div>

      <?php
    }
  } else {
    ?>
    <div class="card card-success" id="ppAccountBalanceLive">

      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'title'); ?>
      </div>
      <div class="card-block">
        <p
          class="card-text"><?php echo $CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'introduction'); ?></p>
      </div>
    </div>
    <div class="separator"></div>
    <div>
      <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_install_title', ['title' => $CLICSHOPPING_PayPal->getConfigModuleInfo($current_module, 'title')]), null, $CLICSHOPPING_PayPal->link('Configure&Install&module=' . $current_module), 'warning'); ?>
    </div>

    <?php
  }

  require_once(__DIR__ . '/template_bottom.php');
