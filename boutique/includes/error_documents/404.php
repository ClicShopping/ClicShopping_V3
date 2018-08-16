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

  http_response_code(404);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Error - Page Not Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
  </head>
  <body>
    <div class="text-md-center" style="padding-top:150px;">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-md-center" style="padding-top:50px; padding-bottom:20px;"><h1><?php echo HTTP::redirect(HTTP::getShopUrlDomain() . 'index.php'); ?></h1></div>
            <div class="error-template">
              <h1>Oops!</h1>
              <h2>404 Not Found</h2>
              <div class="error-details">
                Sorry, an error has occured, Requested page not found!
                <h1>This Page is Missing</h1>
                  <p>It looks like this page is missing. Please continue back to our website and try again.</p>
                  <p style="margin-top: 40px;"><php echo HTML::button('Return to website', null, CLICSHOPPING::link('index.php'), null, 'primary'); ?></p>

              </div>
              <div class="error-actions">
                <br /><br />
                <i class="fas fa-home"></i> <span style="text-decoration:none;"><a href="<?php echo HTTP::redirect(HTTP::getShopUrlDomain() . 'index.php');; ?>">Go to the web store</a></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
