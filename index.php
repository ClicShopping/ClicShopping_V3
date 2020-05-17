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

//
//Directory to change
//
  $directory = '/shop/';

// Absolute path
  $cwd = getcwd();

// Chemin relatif à la boutique
  chdir($cwd . '/shop/');

  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/shop/includes/ClicShopping/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');

  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  
  $Qsubmit = $CLICSHOPPING_Db->prepare('select submit_id,
                                               language_id,
                                               submit_defaut_language_title,
                                               submit_defaut_language_keywords,
                                               submit_defaut_language_description
                                         from :table_submit_description
                                         where submit_id = :submit_id
                                         and language_id = :language_id
                                        ');
  $Qsubmit->bindInt(':submit_id', 1);
  $Qsubmit->bindInt(':language_id', $CLICSHOPPING_Language->getID());
  $Qsubmit->execute();

   if (empty($Qsubmit->value('submit_defaut_language_title'))) {
     $tile = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
     $tile = HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
   }

   if (empty($Qsubmit->value('submit_defaut_language_description'))) {
     $description = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
     $description = HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
   }

   if (empty($Qsubmit->value('submit_defaut_language_keywords'))) {
    $keywords = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
    $keywords = HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
   }

  $title = $CLICSHOPPING_Template->setTitle($tile . ' ' . $CLICSHOPPING_Template->getTitle());
  $description = $CLICSHOPPING_Template->setDescription($description . ' ' . $CLICSHOPPING_Template->getDescription());
  $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ' ' . $CLICSHOPPING_Template->getKeywords());

  if (!empty($CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntro() )) {
    ob_start();
?>
<!DOCTYPE html>
<html <?php echo CLICSHOPPING::getDef('html_params'); ?>>
  <head>
    <meta charset="<?php echo CLICSHOPPING::getDef('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="<?php echo HTTP::getShopUrlDomain() ;?>">
    <title><?php echo HTML::outputProtected($title);?></title>
    <meta name="description" content="<?php echo HTML::outputProtected($description);?>" />
    <meta name="keywords"  content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getKeywords());?>" />
    <meta name="news_keywords" content="<?php echo HTML::outputProtected($keywords);?>" />
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
      
      if (!empty($CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntroTime() )) {
?>
<meta http-equiv="refresh" content="<?php echo $CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntroTime(); ?> ;url=<?php echo  HTTP::getShopUrlDomain(); ?>">
<?php
    }
?>
  <style>
* {margin: 0;}
html, body {height: 100%;}
  </style>
</head>
<body>
    <div class="wrapperIntroduction" id="wrapperIntroduction">
      <div class="Pageintroduction" id="Pageintroduction"><?php echo $CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntro(); ?></div>
      <div class="push" id="push"></div>
    </div>
    <div class="PageintroductionAccessCatalog footerIntroduction" id="PageintroductionAccessCatalog"><a href="<?php echo HTTP::getShopUrlDomain(); ?>"><?php echo CLICSHOPPING::getDef('access_catalog', ['store_name' => STORE_NAME]); ?></a></div>

<?php
    require_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Sites/Shop/Templates/Default/footer.php');
    ob_end_flush();
  } else {
    HTTP::redirect(CLICSHOPPING::link('index.php'));
  }
?>
<?php
    echo $CLICSHOPPING_Template->getBlocks('footer_scripts');

    $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/Footer/';

    if (is_dir($source_folder)) {
      $files_get_output = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'FooterOutput*');
      $files_get_call = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'FooterCall*');

      foreach ($files_get_output as $value) {
        if (!empty($value['name'])) {
          echo $CLICSHOPPING_Hooks->output('Footer', $value['name'], null, 'display');
        }
      }

      foreach ($files_get_call as $value) {
        if (!empty($value['name'])) {
          $CLICSHOPPING_Hooks->call('Footer', $value['name']);
        }
      }
    }
?>
  </body>
</html>