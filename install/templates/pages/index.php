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
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  $configfile_array = [
      CLICSHOPPING::BASE_DIR . 'Conf/global.php',
      CLICSHOPPING::BASE_DIR . 'Conf/ElFinderConfig.php',
      CLICSHOPPING::BASE_DIR . 'Sites/Shop/site_conf.php',
      CLICSHOPPING::BASE_DIR . 'Sites/ClicShoppingAdmin/site_conf.php'
  ];

  foreach ($configfile_array as $key => $f) {
    if (!is_file($f)) {
      continue;
    } elseif (!FileSystem::isWritable($f)) {
      // try to chmod and try again
      @chmod($f, 0777);

      if (!FileSystem::isWritable($f)) {
        continue;
      }
    }

// file exists and is writable
    unset($configfile_array[$key]);
  }

    $directory_array = [CLICSHOPPING::BASE_DIR . 'Work'];

    foreach ($directory_array as $key => $d) {
      if (!is_dir($d)) {
        continue;
      } elseif (!is_writable($d)) {
          continue;
      }

      // Directtory exists and is writable
      unset($directory_array[$key]);
    }

    $warning_array = [];

if (version_compare(phpversion(), '7.4', '<')) {
    $warning_array[] = 'The minimum required PHP version is v7.4 Please ask your host or server administrator to upgrade the PHP version to continue installation.';
}

if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
    $warning_array[] = 'The PDO MySQL driver extension (pdo_mysql) is not installed or enabled in PHP. Please enable it in the PHP configuration to continue installation.';
}

if (!extension_loaded('curl')) {
    $warning_array[] = 'The cURL extension (curl) is not installed or enabled in PHP. Please enable it in the PHP configuration to continue installation.<br />
    You can bypass this process (not recommended) but you can have error more later if you don\'t install Curl. <a href="install.php">Continue the process</a>';
}

if (!extension_loaded('zip')) {
    $warning_array[] = 'The Zip extension (zip) is not installed or enabled in PHP. Please enable it in the PHP configuration to continue installation.<br />
    You can bypass this process (not recommended) but you can have error more later if you don\'t install Zip. <a href="install.php">Continue the process</a>';
}

$https_url = 'https://' . $_SERVER['HTTP_HOST'];

if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
    $https_url .= $_SERVER['REQUEST_URI'];
} else {
    $https_url .= $_SERVER['SCRIPT_FILENAME'];
}
?>

<br />
<div class="separator"></div>
<div class="col-md-1">
  <form action="index.php" method="get">
    <?php echo HTML::selectMenu('language', $languages_array, $language, 'onChange="this.form.submit();"'); ?>
  </form>
</div>
<br /><br />

<div class="alert alert-info" role="alert">
  <h2><?php echo TEXT_TITLE_WELCOME; ?> <small>v<?php echo CLICSHOPPING::getVersion(); ?></small></h2>

  <p><?php echo TEXT_INTRO_WELCOME; ?></p>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">
    <h1><?php echo TEXT_NEW_INSTALLATION; ?></h1>

<?php
if (!empty($warning_array)) {
?>

    <div class="alert alert-danger" role="alert">
      <p><?php echo TEXT_NOTICE; ?></p>

      <ul style="margin-top: 20px; margin-bottom: 20px;">

<?php
    foreach ($warning_array as $key => $value) {
        echo '<li>' . $value . '</li>';
    }
?>

      </ul>

      <p><i>Changing webserver configuration parameters may require the webserver service to be restarted before the changes take affect.</i></p>
    </div>

<?php
}

if (!empty($configfile_array)  || !empty($directory_array)) {
?>

    <div class="alert alert-danger" role="alert">
      <p><?php echo TEXT_NOT_SAVE_PARAMETERS; ?></p>

      <ul style="margin-top: 20px;">

<?php
    foreach ($configfile_array as $file) {
        echo '<li>' . FileSystem::displayPath($file) . '</li>';
    }

    foreach ($directory_array as $dir) {
      echo  '<li>' . $dir . '</li>';
    }
?>

      </ul>
    </div>

<?php
}

if (!empty($configfile_array) || !empty($warning_array) || !empty($directory_array)) {
?>

    <p><a href="index.php" class="btn btn-danger" role="button">Retry Installation</a></p>

<?php
} else {
?>

    <div id="detectHttps" class="alert alert-info" role="alert">
      <p><i class="fas fa-spinner fa-spin fa-fw"></i> Please wait, detecting web server environment..</p>
    </div>

    <div id="jsOn" style="display: none;">
      <p>The web server environment has been verified to proceed with a successful installation and configuration of your online store.</p>

      <div id="httpsNotice" style="display: none;">
        <div class="alert alert-warning" role="alert">
          <p><strong>HTTPS Server Detected</strong></p>

          <p>A HTTPS configured web server has been detected. It is recommended to install your online store in a secure environment. Please click the following <span class="label label-warning">Reload in HTTPS</span> button to reload this installation procedure in HTTPS. If you receive an error, please use your browsers back button to return to this page and continue the installation using the <span class="label label-success">Start the Installation Procedure</span> button below.</p>

          <p><a href="<?= $https_url; ?>" class="btn btn-warning btn-sm" role="button">Reload in HTTPS</a></p>
        </div>
      </div>

      <p><a href="install.php" class="btn btn-success" role="button">Start the Installation Procedure</a></p>
    </div>

    <div id="jsOff">
      <p class="text-danger">Please enable Javascript in your browser to be able to start the installation procedure.</p>
      <p><a href="index.php" class="btn btn-danger" role="button">Retry Installation</a></p>
    </div>

<script>
$(function() {
  $('#jsOff').hide();

  if (document.location.protocol == 'https:') {
    $('#detectHttps').hide();
    $('#jsOn').show();
  } else {
    var httpsCheckUrl = 'rpc.php?action=httpsCheck';

    $.post(httpsCheckUrl, null, function (response) {
      if (('status' in response) && ('message' in response)) {
        if ((response.status == '1') && (response.message == 'success')) {
          $('#detectHttps').hide();
          $('#httpsNotice').show();
          $('#jsOn').show();
        } else {
          $('#detectHttps').hide();
          $('#jsOn').show();
        }
      } else {
        $('#detectHttps').hide();
        $('#jsOn').show();
      }
    }, 'json').fail(function() {
      $('#detectHttps').hide();
      $('#jsOn').show();
    });
  }
});
</script>

<?php
}
?>

  </div>

  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <div class="card">
      <div class="card-header">
        <?php echo TEXT_SERVER_CARACTERISTICS; ?>
      </div>

      <p style="margin: 5px;"><strong>PHP Version</strong></p>

      <table class="table">
        <tbody>
          <tr>
            <td><?php echo PHP_VERSION; ?></td>
            <td class="text-md-right" width="25">
              <?php echo ((version_compare(phpversion(), '7.3.3', '>')) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-danger"></i>'); ?></td>
          </tr>
        </tbody>
      </table>

<?php
if (function_exists('ini_get')) {
?>

      <p style="margin: 5px;"><strong><?php echo TEXT_PHP_SETTINGS; ?></strong></p>

      <table class="table">
        <tbody>
          <tr>
            <td>file_uploads</td>
            <td class="text-md-right"><?php echo (((int)ini_get('file_uploads') === 0) ? 'Off' : 'On'); ?></td>
            <td class="text-md-right"><?php echo (((int)ini_get('file_uploads') === 1) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-danger"></i>'); ?></td>
          </tr>
        </tbody>
      </table>

      <p style="margin: 5px;"><strong><?php echo TEXT_PHP_VERSION; ?></strong></p>

      <table class="table">
        <tbody>
          <tr>
            <td>PDO MySQL / Maria Db</td>
            <td class="text-md-right"><?php echo extension_loaded('pdo') && extension_loaded('pdo_mysql') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-danger"></i>'; ?></td>
          </tr>
          <tr>
            <td>cURL</td>
            <td class="text-md-right"><?php echo extension_loaded('curl') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-warning"></i>'; ?></td>
          </tr>
          <tr>
            <td>Zip</td>
            <td class="text-md-right"><?php echo extension_loaded('zip') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-warning"></i>'; ?></td>
          </tr>
          <tr>
            <td>GD</td>
            <td class="text-md-right"><?php echo extension_loaded('gd') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-warning"></i>'; ?></td>
          </tr>
          <tr>
            <td>OpenSSL</td>
            <td class="text-md-right"><?php echo extension_loaded('openssl') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-exclamation-circle text-warning"></i>'; ?></td>
          </tr>
        </tbody>
      </table>

<?php
}
?>

    </div>
  </div>
</div>
