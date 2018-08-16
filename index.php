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

// Chemin absolue du répertoire absolue
  $cwd = getcwd();

// Chemin relatif à la boutique
  chdir($cwd . '/boutique/');

// Appel aux fonctions de OPC
  include('includes/application_top.php');

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Db = Registry::get('Db');

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

     $CLICSHOPPING_Template->setTitle($tags_array['title'] . ' ' . $CLICSHOPPING_Template->getTitle());
     $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ' ' . $CLICSHOPPING_Template->getDescription());
     $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ' ' . $CLICSHOPPING_Template->getKeywords());

  if (!empty($CLICSHOPPING_PageManagerShop->pageManagerDisplayPageIntro() )) {
    ob_start();

?>
<!DOCTYPE html>
<html <?php echo CLICSHOPPING::getDef('html_params'); ?>>
  <head>
    <meta charset="<?php echo CLICSHOPPING::getDef('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="<?php echo HTTP::getShopUrlDomain() ;?>">
    <title><?php echo HTML::outputProtected($CLICSHOPPING_Template->getTitle());?></title>
    <meta name="Description" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getDescription());?>" />
    <meta name="Keywords" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getKeywords());?>" />
    <meta name="news_keywords" content="<?php echo HTML::outputProtected($CLICSHOPPING_Template->getNewsKeywords());?>" />
    <meta name="no-email-collection" content="<?php echo HTTP::typeUrlDomain(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <link rel="icon" type="image/png" href="<?php echo CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path') . 'images/logo_clicshopping.png' ?>" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>

    <link rel="stylesheet" media="screen, print, projection" href="<?php echo $CLICSHOPPING_Template->getTemplategraphism();?>" />

  <?php echo $CLICSHOPPING_Template->getBlocks('header_tags'); ?>

<?php
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
    HTTP::redirect(CLICSHOPPING::link('index.php'));
  }
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

  </html>
</body>