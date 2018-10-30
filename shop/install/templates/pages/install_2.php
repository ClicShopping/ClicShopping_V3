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

use ClicShopping\OM\FileSystem;
use ClicShopping\OM\HTML;
use ClicShopping\OM\CLICSHOPPING;

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
?>

<div class="row">
  <div class="col-sm-9">
    <div class="alert alert-info">
      <h2> <?php echo TEXT_NEW_INSTALLATION; ?></h2>

      <p><?php echo TEXT_STEP_INTRO_STEP4; ?>.</p>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="card">
      <div class="card-header">
        <p>Step 2/4</p>

        <ol>
          <li>Database Server</li>
          <li><strong>&gt; Web Server</strong></li>
          <li>Online Store Settings</li>
          <li>Finished!</li>
        </ol>
      </div>
    </div>
    <br />
    <div class="progress">
      <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">50%</div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">
    <h1><?php echo TEXT_STEP_INTRO_3; ?></h1>

    <form name="install" id="installForm" action="install.php?step=3" method="post">
      <div class="form-group has-feedback">
        <label for="wwwAddress"><?php echo TEXT_STEP_INTRO_4; ?></label>
        <?php echo HTML::inputField('HTTP_WWW_ADDRESS', $www_location, 'required aria-required="true" id="wwwAddress" placeholder="http://"'); ?>
        <span class="help-block"><?php echo TEXT_STEP_HELP_4; ?></span>
      </div>

      <div class="form-group has-feedback">
        <label for="webRoot"<?php echo TEXT_STEP_INTRO_5; ?></label>
        <?php echo HTML::inputField('DIR_FS_DOCUMENT_ROOT', str_replace('\\', '/', FileSystem::displayPath($dir_fs_www_root)), 'required aria-required="true" id="webRoot"'); ?>
        <span class="help-block">The directory where the online store is installed on the server.</span>
      </div>

      <p><?php echo HTML::button('Continue to Step 3', 'triangle-1-e', null, 'success'); ?></p>

<?php
foreach ($_POST as $key => $value) {
    if (($key != 'x') && ($key != 'y')) {
        echo HTML::hiddenField($key, $value);
    }
}
?>

    </form>
  </div>

  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <br />
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          Step 2: Web Server
        </div>
      </div>

      <div class="card-body">
        <p>The web server takes care of serving the pages of your online store to your guests and customers. The web server parameters make sure the links to the pages point to the correct location.</p>
      </div>
    </div>
  </div>
</div>
