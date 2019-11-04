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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('Template');
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
<?php
     $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/Header/';

     if (is_dir($source_folder)) {
       $files_get_output = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'HeaderOutput*');
       $files_get_call = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'HeaderCall*');

       foreach ($files_get_output as $value) {
         if (!empty($value['name'])) {
           echo $CLICSHOPPING_Hooks->output('Header', $value['name'], null, 'display');
         }
       }

       foreach ($files_get_call as $value) {
         if (!empty($value['name'])) {
           $CLICSHOPPING_Hooks->call('Header', $value['name']);
         }
       }
     }

      echo $CLICSHOPPING_Template->getBlocks('header_tags') . "\n";
?>
  </head>
  <body>
    <div class="<?php echo BOOTSTRAP_CONTAINER;?>" id="<?php echo BOOTSTRAP_CONTAINER;?>">
      <div class="bodyWrapper" id="bodyWrapper">
        <header class="page-header" id="banner">
<?php
  if  ( MODE_VENTE_PRIVEE == 'false' || (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() )) {
    echo $CLICSHOPPING_Template->getBlocks('modules_header');
  }
?>
        </header>
        <div class="d-flex flex-wrap frameWork" id="frameWork">
          <div id="bodyContent" class="col-lg-<?php echo $CLICSHOPPING_Template->getGridContentWidth(); ?> order-xs-1 order-lg-2">