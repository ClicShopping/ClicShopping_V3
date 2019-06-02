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

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <script src="https://kit.fontawesome.com/89fdf54890.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

  <meta name="robots" content="noindex,nofollow">

  <link rel="stylesheet" href="templates/main_page/stylesheet.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row" style="margin-top: 10px; margin-bottom: 20px;">
      <div class="col-sm-6">
        <a href="index.php"><img src="../images/logo_clicshopping_1.png" border="0" width="200" height="90" title="ClicShopping" style="margin: 10px 10px 0px 10px;" /></a>
      </div>

      <div id="headerShortcuts" class="col-sm-6 text-md-right">
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
                <div class="text-md-center">
                  <small>Copyright &copy; 2008-<?php echo date('Y'); ?> <a href="http://www.clicshopping.org" target="_blank" rel="noreferrer">ClicShopping(TM)</a> - Brand deposed at INPI</small>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
