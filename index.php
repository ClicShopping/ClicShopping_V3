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

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

// Chemin absolue du répertoire absolue
  $cwd = getcwd();

// Chemin relatif à la boutique
  chdir($cwd . '/shop/');

  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/shop/includes/ClicShopping/');

  require(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');

  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

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

// Definition de la variable de gestion des colonnes
    $tags_array = [];

//----------------------------------------------------------------
//         fichier index.php du catalog                         //
//---------------------------------------------------------------
   if (empty($Qsubmit->value('submit_defaut_language_title'))) {
     $tags_array['title'] = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
     $tags_array['title'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
   }

   if (empty($Qsubmit->value('submit_defaut_language_description'))) {
     $tags_array['desc'] = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
     $tags_array['desc'] =HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
   }

   if (empty($Qsubmit->value('submit_defaut_language_keywords'))) {
     $tags_array['keywords'] = CLICSHOPPING::getDef('title', ['store_name' => STORE_NAME]);
   } else {
     $tags_array['keywords'] = HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
   }

  $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ' ' . $CLICSHOPPING_Template->getTitle());
  $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ' ' . $CLICSHOPPING_Template->getDescription());
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
    <meta name="Description" content="<?php echo HTML::outputProtected($description);?>" />
    <meta name="Keywords" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getKeywords());?>" />
    <meta name="news_keywords" content="<?php echo HTML::outputProtected($keywords);?>" />
    <meta name="no-email-collection" content="<?php echo HTTP::typeUrlDomain(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" type="image/png" href="<?php echo CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path') . 'images/logo_clicshopping.png' ?>" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

    <link rel="stylesheet" media="screen, print, projection" href="<?php echo $CLICSHOPPING_Template->getTemplategraphism();?>" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"  defer></script>
<?php
      echo $CLICSHOPPING_Template->getBlocks('header_tags');
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
    <div class="wrapperIntroduction">
      <div class="Pageintroduction"><?php echo $CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntro(); ?></div>
      <div class="push"></div>
    </div>
    <div class="PageintroductionAccessCatalog footerIntroduction"><a href="<?php echo HTTP::getShopUrlDomain(); ?>"><?php echo CLICSHOPPING::getDef('access_catalog', ['store_name' => STORE_NAME]); ?></a></div>

<?php
    require('includes/ClicShopping/Sites/Shop/Templates/Default/footer.php');
    ob_end_flush();
  } else {
    HTTP::redirect(CLICSHOPPING::link());
  }
?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </html>
</body>