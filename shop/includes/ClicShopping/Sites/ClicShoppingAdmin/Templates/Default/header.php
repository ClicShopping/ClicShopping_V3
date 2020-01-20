<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\OM\ErrorHandler;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
  use ClicShopping\Apps\Tools\WhosOnline\Classes\ClicShoppingAdmin\WhosOnlineAdmin;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
?>
<!DOCTYPE html>
<html <?php echo CLICSHOPPING::getDef('html_params'); ?>>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CLICSHOPPING::getDef('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex,nofollow" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="generator" content="ClicShopping" />
  <meta name="author" content="ClicShopping" />
  <meta name="Description" content="ClicShopping Administration" />

  <title><?php echo CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]); ?></title>

  <base href="<?php echo CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin'); ?>" />

  <link rel="icon" type="image/webp"  href="<?php echo CLICSHOPPING::link('Shop/images/logo_clicshopping.webp'); ?>" />

  <?php
     $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/ClicShoppingAdmin/Header/';
     $output = 'HeaderOutput*';
     $call = 'HeaderCall*';
     $hook_call = 'Header';

     $CLICSHOPPING_Template->useRecursiveModulesHooksForTemplate($source_folder,  $output,  $call, $hook_call);
?>
  <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/general.js'); ?>"></script>

</head>
<body onload="SetFocus();">
<div class="container-fluid">
  <div class="col-md-12">
    <noscript>
      <div class="alert alert-warning no-script" role="alert">
        <div class="no-script-inner"><?php echo CLICSHOPPING::getDef('no_script_text'); ?></div>
      </div>
    </noscript>
  </div>

  <div class="wrapper" id="wrapper">
    <div class="content" id="content">
      <div class="headerFond">
        <span class="headerLogo"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/logo_clicshopping.webp', 'ClicShopping', '166', '55'); ?></span>
        <span class="infoHeader">
<?php
  if (isset($_SESSION['admin'])) {
    if ($_SESSION['admin']['access'] == 1 && count(glob(ErrorHandler::getDirectory() . 'errors-*.txt', GLOB_NOSORT)) > 0) {
?>
      <span><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Tools\EditLogError&LogError'), '<i class="fas fa-exclamation-circle text-warning"></i>'); ?></span>
 <?php
  }
?>
      <span><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/administrateur.gif', CLICSHOPPING::getDef('text_header_user_administrator'), '16', '16'); ?></span>
      <span class="menuJSCookTexte"><?php echo (isset($_SESSION['admin']) ? '&nbsp;' . AdministratorAdmin::getUserAdmin()  .  '&nbsp; - &nbsp;<a href="' . CLICSHOPPING::link('login.php', 'action=logoff') . '" class="headerLink"><i class="fas fa-power-off" aria-hidden="true"></i></a>' : ''); ?> &nbsp;&nbsp;</span>
      <span class="InfosHeaderWhoOnline"><?php echo (isset($_SESSION['admin']) ? HTML::link(CLICSHOPPING::link(null, 'A&Tools\WhosOnline&WhosOnline'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/clients.gif', CLICSHOPPING::getDef('text_header_online_customers'), '16', '16')) : ''); ?></span>
      <span class="menuJSCookTexte InfosHeaderWhoOnline"><?php  echo (isset($_SESSION['admin']) ? '&nbsp;' . CLICSHOPPING::getDef('text_header_number_of_customers', ['online_customer' => WhosOnlineAdmin::getCountWhosOnline()]) . '&nbsp;&nbsp;' : ''); ?></span>
<?php
  }
?>
       </span>
      </div>
      <div class="headerLine"></div>
<?php
  if (isset($_SESSION['admin'])) {
?>
      <div><?php include_once('header_menu.php'); ?></div>
      <div><?php $CLICSHOPPING_Hooks->output('Header', 'HeaderInfo', null, 'display'); ?></div>
<?php
  }

  if (Registry::get('MessageStack')->exists('main')) {
?>
       <div><?php echo Registry::get('MessageStack')->get('main'); ?></div>
<?php
  }
?>