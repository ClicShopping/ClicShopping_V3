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

use ClicShopping\OM\DateTime;
use ClicShopping\OM\FileSystem;
use ClicShopping\OM\HTML;

$dir_fs_document_root = $_POST['DIR_FS_DOCUMENT_ROOT'];

if ((substr($dir_fs_document_root, -1) != '\\') && (substr($dir_fs_document_root, -1) != '/')) {
    if (strrpos($dir_fs_document_root, '\\') !== false) {
        $dir_fs_document_root .= '\\';
    } else {
        $dir_fs_document_root .= '/';
    }
}
?>

<div class="row">
  <div class="col-sm-9">
    <div class="alert alert-info" role="alert">
      <h2><?php echo TEXT_END_CONFIGURATION; ?></h2>

      <p><?php echo TEXT_INFO_1; ?></p>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="card">
      <div class="card-header">
        <p>Step 3/4</p>

        <ol>
          <li>Database Server</li>
          <li>Web Server</li>
          <li><strong>&gt; Online Store Settings</strong></li>
          <li>Finished!</li>
        </ol>
      </div>
    </div>
    <br />
    <div class="progress">
      <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">75%</div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">
    <h1>Online Store Settings</h1>

    <form name="install" id="installForm" action="install.php?step=4" method="post">

        <div class="form-group has-feedback">
          <label for="storeName" class="control-label col-md-3"><?php echo TEXT_STORE_NAME; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_STORE_NAME', NULL, 'required aria-required="true" id="storeName" placeholder="Your Store Name"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_HELP; ?></small></span><br /><br />
          </div>
        </div>

        <div class="form-group has-feedback">
          <label for="StoreOwnerEmail" class="control-label col-md-3"><?php echo TEXT_STORE_OWNER; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_STORE_OWNER_NAME', null, 'required aria-required="true" id="StoreOwnerEmail" placeholder="'.TEXT_STORE_OWNER.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_OWNER_HELP; ?></small></span><br /><br />
          </div>
        </div>
        <div class="form-group has-feedback">
          <label for="ownerEmail" class="control-label col-md-3"><?php echo TEXT_STORE_OWNER_EMAIL; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_STORE_OWNER_EMAIL_ADDRESS', null, 'required aria-required="true" id="ownerEmail" placeholder="'.TEXT_STORE_OWNER_EMAIL.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_OWNER_EMAIL_HELP; ?></small></span><br /><br />
          </div>
        </div>
        <div class="form-group has-feedback">
          <label for="StoreNameAdmin" class="control-label col-md-3"><?php echo TEXT_STORE_NAME_ADMIN; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_ADMINISTRATOR_NAME', null, 'required aria-required="true" id="StoreNameAdmin" placeholder="'.TEXT_STORE_NAME_ADMIN.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_NAME_ADMIN_HELP; ?></small></span><br /><br />
          </div>
        </div>

        <div class="form-group has-feedback">
          <label for="adminFirstName" class="control-label col-md-3"><?php echo TEXT_STORE_FIRST_NAME; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_ADMINISTRATOR_FIRSTNAME', null, 'required aria-required="true" id="adminFirstName" placeholder="'.TEXT_STORE_FIRST_NAME.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_FIRST_NAME_HELP; ?></small></span><br /><br />
          </div>
        </div>

        <div class="form-group has-feedback">
          <label for="adminUsername" class="control-label col-md-3"><?php echo TEXT_STORE_EMAIL_ADMIN; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_ADMINISTRATOR_USERNAME', NULL, 'required aria-required="true" id="adminUsername" placeholder="'.TEXT_STORE_EMAIL_ADMIN.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_EMAIL_ADMIN_HELP; ?></small></span><br /><br />
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="form-group has-feedback">
          <label for="adminPassword" class="control-label col-md-3"><?php echo TEXT_STORE_PASSWORD; ?></label>
          <div class="col-md-9">
            <?php echo HTML::inputField('CFG_ADMINISTRATOR_PASSWORD', NULL, 'required aria-required="true" id="adminPassword" placeholder="'.TEXT_STORE_PASSWORD.'"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_PASSWORD_HELP; ?></small></span><br /><br />
          </div>
        </div>

<?php
  /*
  if (FileSystem::isWritable($dir_fs_document_root) && FileSystem::isWritable($dir_fs_document_root . 'admin')) {
?>

                <div class="form-group has-feedback">
                  <label for="adminDir">Administration Directory Name</label>
                  <?php echo HTML::inputField('CFG_ADMIN_DIRECTORY', 'admin', 'required aria-required="true" id="adminDir"'); ?>
                  <span class="form-text">This is the directory where the administration section will be installed. You should change this for security reasons.</span>
                </div>

<?php
  }
*/
?>

        <div class="form-group has-feedback">
          <label for="Zulu" class="control-label col-md-3"><?php echo TEXT_STORE_TIME_ZONE; ?></label>
          <div class="col-md-9">
            <?php echo HTML::selectMenu('TIME_ZONE', DateTime::getTimeZones(), date_default_timezone_get(), 'id="Zulu"'); ?>
            <span class="form-control-feedback inputRequirement"><i class="fas fa-asterisk" aria-hidden="true"></i></span>
            <span class="form-text"><small><?php echo TEXT_STORE_TIME_ZONE_HELP; ?></small></span><br /><br />
          </div>
        </div>






<?php
foreach ($_POST as $key => $value) {
    if (($key != 'x') && ($key != 'y')) {
        echo HTML::hiddenField($key, $value);
    }
}
?>

  </div>


  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <br />
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          Step 3: Online Store Settings
        </div>
      </div>

      <div class="card-body">
        <p>Here you can define the name of your online store and the contact information for the store owner.</p>
        <p>The administrator username and password are used to log into the protected administration tool section.</p>
      </div>
    </div>
  </div>
</div>
  <div class="separator"></div>
  <div class="text-md-right">
    <p><?php echo HTML::button('Continue to Step 4', 'triangle-1-e', null, 'success'); ?></p>
  </div>
</div>
</form>