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
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Apps;
?>
<!DOCTYPE html>
<html <?php echo CLICSHOPPING::getDef('html_params'); ?>>
  <head>
    <meta charset="<?php echo CLICSHOPPING::getDef('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="<?php echo HTTP::getShopUrlDomain() ;?>">
    <?php echo $CLICSHOPPING_Template->getAppsHeaderTags(); ?>
    <title><?php echo HTML::outputProtected($CLICSHOPPING_Template->getTitle());?></title>
    <meta name="Description" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getDescription());?>" />
    <meta name="Keywords" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getKeywords());?>" />
    <meta name="news_keywords" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getNewsKeywords());?>" />
    <meta name="no-email-collection" content="<?php echo HTTP::typeUrlDomain(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

    <link rel="stylesheet" media="screen, print" href="<?php echo $CLICSHOPPING_Template->getTemplategraphism();?>" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <?php echo $CLICSHOPPING_Template->getBlocks('header_tags') . "\n"; ?>
  </head>
  <body>
  <div class="<?php echo BOOTSTRAP_CONTAINER;?>">
    <div class="bodyWrapper" id="bodyWrapper">
      <article>
        <header>
<?php
  if  ( MODE_VENTE_PRIVEE == 'false' || (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() )) {
    echo $CLICSHOPPING_Template->getBlocks('modules_header');
  }
?>
        </header>
        <div class="d-flex flex-wrap">
<?php
  if ($CLICSHOPPING_Template->hasBlocks('boxes_column_left')) {
?>
          <div id="columnLeft" class="col-md-<?php echo $CLICSHOPPING_Template->getGridColumnWidth(); ?>  col-md-pull-<?php echo $CLICSHOPPING_Template->getGridContentWidth(); ?>">
            <?php echo $CLICSHOPPING_Template->getBlocks('boxes_column_left'); ?>
          </div>
<?php
  }
?>
          <div id="bodyContent" class="col-md-<?php echo $CLICSHOPPING_Template->getGridContentWidth(); ?> <?php echo ($CLICSHOPPING_Template->hasBlocks('boxes_column_left') ? 'col-md-push-' . $CLICSHOPPING_Template->getGridColumnWidth() : ''); ?>">
