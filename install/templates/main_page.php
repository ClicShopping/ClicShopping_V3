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

   $languages_array = array(array('id' => 'english', 'text' => 'English'),
                            array('id' => 'french', 'text' => 'Francais'),
                    );

 require_once('includes/languages/' . $language . '.php');

  $template = 'main_page';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="robots" content="noindex,nofollow">
  <title>ClicShopping, Social E-Commerce B2B/B2C Open Source Solutions</title>

  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
  <meta name="generator" content="ClicShopping, Social E-Commerce B2B/B2C Open Source Solutions /">

  <!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet preload" as="style" href="../ext/javascript/bootstrap/css/bootstrap_icons_1.2.1.css" media="screen, print">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <meta name="robots" content="noindex,nofollow">

  <link rel="stylesheet" href="templates/main_page/stylesheet.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row" style="margin-top: 10px; margin-bottom: 20px;">
      <div class="col-sm-6">
        <a href="index.php"><img src="../images/logo_clicshopping_1.png" border="0" width="200" height="90" title="ClicShopping" alt="ClicShopping" style="margin: 10px 10px 0px 10px;" /></a>
      </div>

      <div id="headerShortcuts" class="col-sm-6 text-end">
        <ul class="list-unstyled list-inline">
          <li><a href="https://www.clicshopping.org" target="_blank">ClicShopping Website</a></li>
          <li><a href="https://www.clicshopping.org" target="_blank">Support</a></li>
          <li><a href="https://clicshopping.org" target="_blank">Documentation</a></li>
        </ul>
      </div>
    </div>

    <?php require_once('templates/pages/' . $page_contents); ?>

    <div class="row">
      <div class="col-md-12">
        <footer>
          <div  style="padding-top:1rem;">
            <div class="card">
              <div class="card-footer">
                <div class="text-center">
                  <small>Copyright &copy; 2008-<?php echo date('Y'); ?> <a href="http://www.clicshopping.org" target="_blank" rel="noreferrer">ClicShopping(TM)</a> - Brand deposed at INPI</small>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script></script></body>
</html>
