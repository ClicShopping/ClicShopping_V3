<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Langues = Registry::get('Langues');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $current_module = $CLICSHOPPING_Page->data['current_module'];

  $CLICSHOPPING_Langues_Config = Registry::get('LanguesAdminConfig' . $current_module);

  if ($CLICSHOPPING_MessageStack->exists('Langues')) {
    echo $CLICSHOPPING_MessageStack->get('Langues');
  }
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/languages.gif', $CLICSHOPPING_Langues->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Langues->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
            <?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_languages'), null, $CLICSHOPPING_Langues->link('Langues'), 'success'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="appLanguesToolbar">
    <li class="nav-item">
<?php
  foreach ($CLICSHOPPING_Langues->getConfigModules() as $m) {

    if ($CLICSHOPPING_Langues->getConfigModuleInfo($m, 'is_installed') === true) {
      echo '<li class="nav-link active" data-module="' . $m . '"><a href="' . $CLICSHOPPING_Langues->link('Configure&module=' . $m) . '">' . $CLICSHOPPING_Langues->getConfigModuleInfo($m, 'short_title') . '</a></li>';
    }
  }
?>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Install</a>
      <div class="dropdown-menu">
<?php
  foreach ($CLICSHOPPING_Langues->getConfigModules() as $m) {
    if ($CLICSHOPPING_Langues->getConfigModuleInfo($m, 'is_installed') === false) {
      echo '<a class="dropdown-item" href="' . $CLICSHOPPING_Langues->link('Configure&module=' . $m) . '">' . $CLICSHOPPING_Langues->getConfigModuleInfo($m, 'title') . '</a>';
    }
  }
?>
      </div>
    </li>
  </ul>
<?php
  if ($CLICSHOPPING_Langues_Config->is_installed === true) {
?>
  <form name="ConfigurationLanguesConfigure" action="<?php echo $CLICSHOPPING_Langues->link('Configure&Process&module=' . $current_module); ?>" method="post">

    <div class="mainTitle">
      <?php echo $CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'title'); ?>
    </div>
    <div class="adminformTitle">
      <div class="card-block">

        <p class="card-text">
<?php
    foreach ($CLICSHOPPING_Langues_Config->getInputParameters() as $cfg) {
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
    echo HTML::button($CLICSHOPPING_Langues->getDef('button_save'), null, null, 'success');

    if ($CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'is_uninstallable') === true) {
      echo '<span class="float-md-right">' . HTML::button($CLICSHOPPING_Langues->getDef('button_dialog_uninstall'), null, '#', 'warning', ['params' => 'data-toggle="modal" data-target="#ppUninstallModal"']) . '</span>';
    }
?>
    </div>
  </form>
<?php
    if ($CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'is_uninstallable') === true) {
?>
      <div id="ppUninstallModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><?php echo $CLICSHOPPING_Langues->getDef('dialog_uninstall_title'); ?></h4>
            </div>
            <div class="modal-body">
              <?php echo $CLICSHOPPING_Langues->getDef('dialog_uninstall_body'); ?>
            </div>
            <div class="modal-footer">
              <?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_delete'), null, $CLICSHOPPING_Langues->link('Configure&Delete&module=' . $current_module), 'danger'); ?>
              <?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_uninstall'), null, $CLICSHOPPING_Langues->link('Configure&Uninstall&module=' . $current_module), 'danger'); ?>
              <?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_cancel'), null, '#', 'warning',  ['params' => 'data-dismiss="modal"']); ?>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  } else {
?>
     <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'title'); ?></strong></div>
     <div class="adminformTitle">
       <div class="row">
         <div class="separator"></div>
          <div class="col-md-12">
            <div><?php echo $CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'introduction'); ?></div>
            <div class="separator"></div>
            <div><?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_install_title', ['title' => $CLICSHOPPING_Langues->getConfigModuleInfo($current_module, 'title')]), null, $CLICSHOPPING_Langues->link('Configure&Install&module=' . $current_module), 'warning'); ?></div>
         </div>
       </div>
     </div>
<?php
  }
?>
</div>