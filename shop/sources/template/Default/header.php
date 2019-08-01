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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" media="screen, print" href="<?php echo $CLICSHOPPING_Template->getTemplategraphism();?>" />

    <script src="https://kit.fontawesome.com/89fdf54890.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

    <?php echo $CLICSHOPPING_Template->getBlocks('header_tags') . "\n"; ?>
  </head>
  <body>
    <div class="<?php echo BOOTSTRAP_CONTAINER;?>">
      <div class="bodyWrapper" id="bodyWrapper">
        <header>
<?php
  if  ( MODE_VENTE_PRIVEE == 'false' || (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() )) {
    echo $CLICSHOPPING_Template->getBlocks('modules_header');
  }
?>
        </header>
        <div class="d-flex flex-wrap FrameWork">
          <div id="bodyContent" class="col-lg-<?php echo $CLICSHOPPING_Template->getGridContentWidth(); ?> order-xs-1 order-lg-2">