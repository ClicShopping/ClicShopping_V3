<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTTP;

  http_response_code(404);
?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Error - Page Not Found</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

  <script src="https://kit.fontawesome.com/89fdf54890.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="text-md-center" style="padding-top:150px;">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="text-md-center" style="padding-top:50px; padding-bottom:20px;">
          <h1><?php echo HTTP::redirect(HTTP::getShopUrlDomain() . 'index.php'); ?></h1></div>
        <div class="error-template">
          <h1>Oops!</h1>
          <h2>404 Not Found</h2>
          <div class="error-details">
            Sorry, an error has occured, Requested page not found!
            <h1>This Page is Missing</h1>
            <p>It looks like this page is missing. Please continue back to our website and try again.</p>
            <p style="margin-top: 40px;">
              <php echo HTML::button(
              'Return to website', null, CLICSHOPPING::link(), null, 'primary'); ?>
            </p>

          </div>
          <div class="error-actions">
            <br/><br/>
            <i class="fas fa-home"></i> <span style="text-decoration:none;"><a
                href="<?php echo HTTP::redirect(HTTP::getShopUrlDomain() . 'index.php'); ?>">Go to the web store</a></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>
