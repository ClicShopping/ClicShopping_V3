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
  use ClicShopping\OM\CLICSHOPPING;
/*
  if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) {
      $conn = 'https';
  } else {
      $conn = 'http';
  }

  $www_location = $conn . '://' . $_SERVER['HTTP_HOST'];

  if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strpos($www_location, 'install'));

  $dir_fs_www_root = dirname(dirname(CLICSHOPPING::BASE_DIR)) . '/';
*/
?>


<div class="card">
  <div class="card-header">
    <?php echo TEXT_TITLE_WELCOME; ?>
  </div>
  <div class="card-block">
    <p class="card-text">
      <form action="index.php" method="get">
        <?php echo HTML::selectMenu('language', $languages_array, $language, 'onChange="this.form.submit();"'); ?>
      </form>
    </p>
  </div>
</div>

<div class="separator"></div>
<p><?php echo TEXT_LICENCE; ?></p>

<div class="separator"></div>
<div class="card">
  <div class="card-header">
    License
  </div>
  <div class="card-block">
    <p class="card-text col-md-12">
      <?php include_once('license.txt'); ?>
    </p>
  </div>
</div>

<div class="separator"></div>
<?php echo HTML::form('form', 'verify.php'); ?>
  <div class="col-md-12 text-md-right">
    <?php echo HTML::button(TEXT_ACCEPT_LICENCE, null, null, 'success'); ?>
  </div>
</form>

