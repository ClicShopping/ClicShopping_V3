<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\DateTime;
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
          <li>Last effort and it's Finished!</li>
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
    <h1><?php echo TEXT_TITLE_CONFIGURATION; ?></h1>

    <form name="install" id="installForm" action="install.php?step=4" method="post">
      <div class="p-md-3"></div>
      <div class="card">
        <div class="card-header">
          <div class="h4"><?php echo TEXT_TITLE_SHOP; ?></div>
        </div>
        <div class="card-content">
          <div class="p-md-1"></div>
          <div class="col-md-12"><?PHP ECHO TEXT_INTRO_SHOP; ?></div>
          <div class="p-md-3"></div>
          <div class="col-md-12">
            <label for="storeName" class="col-md-6"><strong><?php echo TEXT_STORE_NAME; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_STORE_NAME', NULL, 'required aria-required="true" id="storeName" placeholder="' . TEXT_STORE_NAME . '"'); ?></span>
            <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i> <small><?php echo TEXT_STORE_HELP; ?></small></span>
          </div>
          <div class="p-md-3"></div>
          <div class="col-md-12">
            <label for="StoreOwnerEmail" class=col-md-6"><strong><?php echo TEXT_STORE_OWNER; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_STORE_OWNER_NAME', null, 'required aria-required="true" id="StoreOwnerEmail" placeholder="'. TEXT_STORE_OWNER . '"'); ?></span>
            <span class="text-danger"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_OWNER_HELP; ?></small></span>
          </div>

          <div class="p-md-3"></div>
          <div class="col-md-12">
            <label for="ownerEmail" class="col-md-6"><strong><?php echo TEXT_STORE_OWNER_EMAIL; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_STORE_OWNER_EMAIL_ADDRESS', null, 'required aria-required="true" id="ownerEmail" placeholder="' . TEXT_STORE_OWNER_EMAIL . '"', 'email'); ?></span>
            <span class="text-danger"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_OWNER_EMAIL_HELP; ?></small></span>
          </div>
        </div>
        <div class="p-md-1"></div>
      </div>


      <div class="p-md-3"></div>
      <div class="card">
        <div class="card-header">
          <div class="h4"></div>
        </div>
        <div class="card-content">
          <div class="p-md-1"></div>
          <div class="col-md-12"><?php echo TEXT_TITLE_ACCESS; ?></div>
          <div class="p-md-3"></div>
          <div class="col-md-12">
            <label for="StoreNameAdmin" class="col-md-6"><strong><?php echo TEXT_STORE_NAME_ADMIN; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_ADMINISTRATOR_NAME', null, 'required aria-required="true" id="StoreNameAdmin" placeholder="' . TEXT_STORE_NAME_ADMIN . '"'); ?></span>
            <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_NAME_ADMIN_HELP; ?></small></span>
          </div>
          <div class="p-md-1"></div>
          <div class="col-md-12">
            <label for="adminFirstName" class="col-md-6"><strong><?php echo TEXT_STORE_FIRST_NAME; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_ADMINISTRATOR_FIRSTNAME', null, 'required aria-required="true" id="adminFirstName" placeholder="' . TEXT_STORE_FIRST_NAME . '"'); ?></span>
              <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_FIRST_NAME_HELP; ?></small></span>
            </span>
          </div>
          <div class="p-md-1"></div>
          <div class="col-md-12">
            <label for="adminUsername" class="col-md-6"><strong><?php echo TEXT_STORE_EMAIL_ADMIN; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_ADMINISTRATOR_USERNAME', NULL, 'required aria-required="true" id="adminUsername" placeholder="' . TEXT_STORE_EMAIL_ADMIN . '"', 'email'); ?></span>
              <span class="text-danger"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_EMAIL_ADMIN_HELP; ?></small></span>
            </span>
          </div>
          <div class="p-md-1"></div>
          <div class="col-md-12">
            <label for="adminPassword" class="col-md-6"><strong><?php echo TEXT_STORE_PASSWORD; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_ADMINISTRATOR_PASSWORD', NULL, 'required aria-required="true" id="adminPassword" placeholder="' . TEXT_STORE_PASSWORD . '"'); ?></span>
            <span class="text-danger"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_PASSWORD_HELP; ?></small></span>
          </div>
        </div>
        <div class="p-md-1"></div>
      </div>


      <div class="p-md-3"></div>
      <div class="card">
        <div class="card-header">
          <div><strong><?php echo TEXT_TITLE_STMP; ?></strong></div>
        </div>
        <div class="card-content">
          <div class="p-md-1"></div>
          <div class="col-md-12"><?php echo TEXT_INTRO_SMTP; ?></div>
          <div class="p-md-3"></div>

          <div class="col-md-12">
            <label for="smtp_host" class="col-md-6"><strong><?php echo TEXT_SMTP_HOST; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_SMTP_HOST', NULL, 'id="smtp_host" placeholder="ex: smtp.gmail.com"'); ?></span>
            <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_SMTP_HOST; ?></small></span>
          </div>

          <div class="p-md-1"></div>
          <div class="col-md-12">
            <div><strong><?php echo TEXT_SMTP_EMAIL_TRANSORT; ?></strong></div>
            <span class="col-md-6">
              <?php
              $smtp_transport_array = array(['id' => 'smtp', 'text' => 'smtp'],
                                            ['id' => 'gmail', 'text' => 'gmail']
                                          );

              echo HTML::selectField('CFG_SMTP_EMAIL_TRANSORT', $smtp_transport_array);
              ?>
              <span class="col-md-12 text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_SMTP_PORT_INFO; ?></small></span>
            </span>
          </div>

          <div class="p-md-1"></div>
          <div class="col-md-12">
            <div><strong><?php echo TEXT_SMTP_PORT; ?></strong></div>
            <span class="col-md-6">
              <?php
              $smtp_port_array = array(['id' => '25', 'text' => '25'],
                                       ['id' => '465', 'text' => '465'],
                                       ['id' => '587', 'text' => '587'],
                                      );

              echo HTML::selectField('CFG_SMTP_PORT', $smtp_port_array);
              ?>
              <span class="col-md-12 text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_SMTP_PORT_INFO; ?></small></span>
            </span>
          </div>

          <div class="p-md-1"></div>
          <div class="col-md-12">
            <label for="smtp_host" class="control-label col-md-6"><strong><?php echo TEXT_SMTP_USERNAME; ?></strong></label>
            <span class="col-md-6"><?php echo HTML::inputField('CFG_SMTP_USER_NAME', NULL, 'id="smtp_host" placeholder="' . TEXT_SMTP_USERNAME . '"'); ?></span>
            <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo 'Veuillez indiquer votre user name concernant votre email. Celuyi-ci n\'est pas forcément en relation avec votre compte d\'administration'; ?></small></span><br /><br />
          </div>

          <div class="p-md-1"></div>
          <div class="col-md-12">
            <div class="form-group has-feedback">
              <label for="smtp_password" class="control-label col-md-6"><strong><?php echo TEXT_SMTP_PASSWORD; ?></strong></label>
              <span class="col-md-6"><?php echo HTML::inputField('CFG_SMTP_PASSWORD', NULL, 'id="smtp_password" placeholder="' . TEXT_SMTP_PASSWORD . '"'); ?></span>
                <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_SMTP_PASSWORD_INFO; ?></small></span><br />
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="p-md-3"></div>
      <div class="card">
        <div class="card-header">
          <div><strong></strong><?php echo TEXT_STORE_TIME_ZONE; ?></strong></div>
        </div>
        <div class="card-content">
          <div class="p-md-1"></div>
          <div class="col-md-12">
            <label for="Zulu" class="control-label col-md-6"></label>
            <span class="col-md-6"><?php echo HTML::selectMenu('TIME_ZONE', DateTime::getTimeZones(), date_default_timezone_get(), 'id="Zulu"'); ?></span>
            <span class="text-primary"><i class="bi bi-asterisk" aria-hidden="true"></i><small><?php echo TEXT_STORE_TIME_ZONE_HELP; ?></small></span>
          </div>
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
  <div class="text-end">
    <p><?php echo HTML::button('Continue to Step 4', 'bi bi-caret-right', null, 'success'); ?></p>
  </div>
</div>
</form>