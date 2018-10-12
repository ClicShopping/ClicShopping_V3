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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
?>
<!DOCTYPE html>
<html <?php echo CLICSHOPPING::getDef('html_params'); ?>>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= CLICSHOPPING::getDef('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex,nofollow">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="generator" content="ClicShopping" />
  <meta name="author" content="innov Concept Consulting" />

  <title><?php echo CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]); ?></title>

  <base href="<?php echo CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin'); ?>" />

  <link rel="icon" type="image/png"  href="<?php echo CLICSHOPPING::link('Shop/images/logo_clicshopping.png'); ?>" />

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.0.1/css/sm-core-css.css">
  <link rel="stylesheet" href="<?php echo CLICSHOPPING::link('css/smartmenus.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo CLICSHOPPING::link('css/smartmenus_customize.css'); ?>">
  <link rel="stylesheet" href="<?php echo CLICSHOPPING::link('css/smartmenus_customize_responsive.css'); ?>">

  <link rel="stylesheet" href="<?php echo CLICSHOPPING::link('css/stylesheet.css'); ?>">
  <link rel="stylesheet" href="<?php echo CLICSHOPPING::link('css/stylesheet_responsive.css'); ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">

  <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/general.js'); ?>"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body onload="SetFocus();">
<div class="container-fluid">
  <div class="wrapper" id="wrapper"></div>
    <div id="content">
      <div class="headerFond"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/header/logo_clicshopping.png', 'ClicShopping', '166', '55'); ?></div>
      <div class="headerLine"></div>
<?php
  if (isset($_SESSION['admin'])) {
?>
      <div><?php include('header_menu.php'); ?></div>
<?php
  }

  if (Registry::get('MessageStack')->exists('main')) {
?>
      <div><?php echo Registry::get('MessageStack')->get('main'); ?></div>
<?php
  }
?>
      <div>
        <noscript>
          <div class="no-script">
            <div class="no-script-inner"><?php echo CLICSHOPPING::getDef('no_script_text'); ?></div>
          </div>
        </noscript>
      </div>
