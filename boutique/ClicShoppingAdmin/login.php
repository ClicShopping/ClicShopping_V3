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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Is;
  use ClicShopping\OM\Hash;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  use ClicShopping\Sites\ClicShoppingAdmin\ActionRecorderAdmin;

  $login_request = true;

  require('includes/application_top.php');

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Mail = Registry::get('Mail');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

// prepare to logout an active administrator if the login page is accessed again
  if (isset($_SESSION['admin'])) {
    $action = 'logoff';
  }

  if (!is_null($action)) {
    switch ($action) {
      case 'process':
        if (isset($_SESSION['redirect_origin']) && isset($_SESSION['redirect_origin']['auth_user']) && !isset($_POST['username'])) {

          $username = HTML::sanitize($_SESSION['redirect_origin']['auth_user']);
          $password = HTML::sanitize($_SESSION['redirect_origin']['auth_pw']);
        } else {
          $username = HTML::sanitize($_POST['username']);
          $password = HTML::sanitize($_POST['password']);
        }

        Registry::set('ActionRecorderAdmin', new ActionRecorderAdmin('ar_admin_login', null, $username));
        $CLICSHOPPING_ActionRecorder = Registry::get('ActionRecorderAdmin');

        if ($CLICSHOPPING_ActionRecorder->canPerform()) {

          $Qadmin = $CLICSHOPPING_Db->get('administrators', ['id',
                                                             'user_name',
                                                             'user_password',
                                                             'name',
                                                             'first_name',
                                                             'access'
                                                            ],
                                                            [ 'user_name' => $username ]
                                          );

          if ($Qadmin->fetch() !== false) {
            if (Hash::verify($password, $Qadmin->value('user_password'))) {
// migrate old hashed password to new php password_hash
              if (Hash::needsRehash($Qadmin->value('user_password'))) {
                $CLICSHOPPING_Db->save('administrators', ['user_password' => Hash::encrypt($password)],
                                                         ['id' => $Qadmin->valueInt('id')]
                                     );
              }

              $_SESSION['admin'] = ['id' => $Qadmin->valueInt('id'),
                                    'username' => $Qadmin->value('user_name'),
                                    'access' =>  $Qadmin->value('access')
                                    ];

              $CLICSHOPPING_ActionRecorder->_user_id = $_SESSION['admin']['id'];
              $CLICSHOPPING_ActionRecorder->record();

              if (isset($_SESSION['redirect_origin'])) {
                $page = $_SESSION['redirect_origin']['page'];

                $get_string = http_build_query($_SESSION['redirect_origin']['get']);

                unset($_SESSION['redirect_origin']);

                CLICSHOPPING::redirect($page, $get_string);
              } else {
                CLICSHOPPING::redirect('index.php');
              }
            }
          }

          if (isset($_POST['username'])) {
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_invalid_administrator'), 'error');

// send an email if someone try to connect on admin panel without authorization
// get ip and infos

            $ip = $_SERVER['REMOTE_ADDR'];
            $host = @gethostbyaddr($ip);
            $referer = $_SERVER['HTTP_REFERER'];

// build report
            $report = date("D M j G:i:s Y") . "\n\n"  . CLICSHOPPING::getDef('report_access_login');
            $report .= "\n\n" . CLICSHOPPING::getDef('report_sender_ip_address') . $ip;
            $report .= "\n" . CLICSHOPPING::getDef('report_sender_host_name') . $host;
            $report .= "\n" . CLICSHOPPING::getDef('report_sender_username') . $username;
            $report .= "\n" . CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin');
            $report .= "\n\n" . TemplateEmailAdmin::getTemplateEmailTextFooter();
// mail report
            $CLICSHOPPING_Mail->clicMail(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER_EMAIL_ADDRESS,  CLICSHOPPING::getDef('report_email_subject'), $report, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
          }
        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_action_recorder', ['module_action_recorder_admin_login_minutes' => (defined('MODULE_ACTION_RECORDER_ADMIN_LOGIN_MINUTES') ? (int)MODULE_ACTION_RECORDER_ADMIN_LOGIN_MINUTES : 5)]));
        }

        if (isset($_POST['username'])) {
          $CLICSHOPPING_ActionRecorder->record(false);
        }

        break;

      case 'logoff':
        $CLICSHOPPING_Hooks->call('Account', 'LogoutBefore');

        unset($_SESSION['admin']);

        if (isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['PHP_AUTH_PW'])) {
          $_SESSION['auth_ignore'] = true;
        }

        $CLICSHOPPING_Hooks->call('Account', 'LogoutAfter');

        CLICSHOPPING::redirect('index.php');
      break;
      case 'create':
        $Qcheck = $CLICSHOPPING_Db->get('administrators', 'id', null, null, 1);

        if (!$Qcheck->check()) {
          $username = HTML::sanitize($_POST['username']);
          $password = HTML::sanitize($_POST['password']);
          $name = HTML::sanitize($_POST['name']);
          $first_name = HTML::sanitize($_POST['first_name']);

          if (!empty($username) ) {

            $CLICSHOPPING_Db->save('administrators', [
                                                      'user_name' =>  $username,
                                                      'user_password' => Hash::encrypt($password),
                                                      'name' => $name,
                                                      'first_name' => $first_name,
                                                      'access' => 1
                                                      ]
                                  );
          }
        }

        CLICSHOPPING::redirect('login.php');

        break;

      case 'send_password':
        $error = false;

// Recaptcha
        if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_ADMIN_PASSWORD') && CONFIG_ANTISPAM == 'recaptcha') {
          if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_ADMIN_PASSWORD == 'True'  && !empty(MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_PUBLIC_KEY)) {
            $error = $CLICSHOPPING_Hooks->call('AllShop', 'GoogleRecaptchaProcess');
          }
        }

        if ($error === false) {
          $username = HTML::sanitize($_POST['username']);

          $Qcheck = $CLICSHOPPING_Db->prepare('select id
                                               from :table_administrators
                                               where user_name = :user_name
                                               limit 1
                                              ');
          $Qcheck->bindValue(':user_name', $username);
          $Qcheck->execute();

          if ($Qcheck->rowCount() == 1 && Is::email($username)) {

            $new_password = Hash::getRandomString(ENTRY_PASSWORD_MIN_LENGTH);
            $crypted_password = Hash::encrypt($new_password);

            $Qupdate = $CLICSHOPPING_Db->prepare('update :table_administrators
                                                   set user_password = :user_password
                                                   where user_name = :user_name
                                                   limit 1
                                                ');
            $Qupdate->bindValue(':user_password', $crypted_password);
            $Qupdate->bindValue(':user_name', $username);

            $Qupdate->execute();

            $body_subject = CLICSHOPPING::getDef('email_password_reminder_subject', ['store_name' => STORE_NAME]);
            $email_body .=  CLICSHOPPING::getDef('email_password_reminder_body', ['store_name' => STORE_NAME, 'remote_address' => $_SERVER['REMOTE_ADDR'], 'new_password' => $new_password ]).'<br /><br />';
            $email_body .=  TemplateEmailAdmin::getTemplateEmailSignature();
            $email_body .=  TemplateEmailAdmin::getTemplateEmailTextFooter();

            $CLICSHOPPING_Mail->clicMail('', $username, $body_subject, sprintf($email_body, $new_password), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_password_sent'), 'success');
          } else {
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_email_address_found'), 'error, again 1 time before to block your IP address');
          }

          CLICSHOPPING::redirect('login.php');
        }

      break;
    }
  }

  $Qcheck = $CLICSHOPPING_Db->get('administrators', 'id', null, null, 1);

  if (!$Qcheck->check()) {
    $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_create_first_administrator'), 'warning');
  }

  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('header.php'));

  if ($Qcheck->check()) {
    $form_action = 'process';
    $button_text = CLICSHOPPING::getDef('button_login');
  } else {
    $form_action = 'create';
    $button_text = CLICSHOPPING::getDef('button_create_administrator');
  }

  if ($action != 'password') {
?>

    <div id="loginModal"  tabindex="-1" role="document" aria-hidden="true" style="padding-top:10rem">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 style="color:#233C7A;"><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1>
          </div>
          <?php echo HTML::form('login', CLICSHOPPING::link('login.php', 'action=' . $form_action)); ?>
          <div class="modal-body">
            <div class="col-md-12 center-block">
<?php
                if ($form_action == 'create') {
?>
                  <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"></span>
                    <?php echo HTML::inputField('first_name', '', 'placeholder="' . CLICSHOPPING::getDef('text_firstname') . '" required aria-required="true" aria-describedby="basic-addon1"'); ?>
                  </div>
                  <div class="separator"></div>
                  <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"></span>
                    <?php echo HTML::inputField('name', '', 'placeholder="' . CLICSHOPPING::getDef('text_name') . '" required aria-required="true" aria-describedby="basic-addon1"'); ?>
                  </div>
                  <div class="separator"></div>
<?php
                }
?>
              <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"></span>
                <?php echo HTML::inputField('username', '', 'placeholder="' . CLICSHOPPING::getDef('text_username') . '" required aria-required="true" aria-describedby="basic-addon1"'); ?>
              </div>
              <div class="separator"></div>
              <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"></span>
                <?php echo HTML::passwordField('password', '','placeholder="' . CLICSHOPPING::getDef('text_password') . '" required aria-required="true" aria-describedby="basic-addon1"'); ?>
              </div>
              <div class="separator"></div>
              <div class="text-md-right">
                <?php echo HTML::button($button_text, null, null, 'primary', null, null); ?>
              </div>
              <div class="separator"></div>
            </div>
          </div>
          </form>
          <div class="modal-footer">
            <div class="col-md-6">
              <a href="../index.php"><button class="btn float-left" data-dismiss="modal" aria-hidden="true"><?php echo CLICSHOPPING::getDef('header_title_online_catalog'); ?></button></a>
            </div>
            <div class="col-md-6">
              <a href="<?php echo CLICSHOPPING::link('login.php', 'action=password'); ?>"><button class="btn float-right" data-dismiss="modal" aria-hidden="true"><?php echo CLICSHOPPING::getDef('text_new_text_password'); ?></button></a>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
  } else {
?>
    <div id="loginModal"  tabindex="-1" role="document" aria-hidden="true" style="padding-top:10rem">
      <div class="modal-dialog">
        <div class="modal-content">
          <?php echo HTML::form('send_password', CLICSHOPPING::link('login.php', 'action=send_password')); ?>
          <div class="modal-header">
            <h2 style="color:#233C7A;"><?php echo CLICSHOPPING::getDef('heading_title_sent_password'); ?></h2>
          </div>
          <div class="modal-body">
            <div class="col-md-12 center-block">
              <div class="text-danger" style="font-size:12px; padding-bottom:10px;"><?php echo CLICSHOPPING::getDef('text_sent_password'); ?></div>
              <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">@</span>
                <?php echo HTML::inputField('username', '','size="150" placeholder="' . CLICSHOPPING::getDef('text_email_lost_password') . '" required aria-required="true"  aria-describedby="basic-addon1"'); ?>
              </div>
              <div class="separator"></div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-md-6">
<?php
    if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_ADMIN_PASSWORD') && CONFIG_ANTISPAM == 'recaptcha') {
      if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_ADMIN_PASSWORD == 'True'  && !empty(MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_PUBLIC_KEY)) {
        echo $CLICSHOPPING_Hooks->output('AllShop', 'GoogleRecaptchaDisplay');
      }
    }
?>
            </div>
            <div class="col-md-6">
              <a href="<?php echo CLICSHOPPING::link('login.php'); ?>"><button class="btn btn-secondary text-md-left" type="button"><?php echo CLICSHOPPING::getDef('header_title_administration'); ?></button></a>
            </div>
            <div class="col-md-6 text-md-right">
              <?php echo HTML::button(CLICSHOPPING::getDef('button_submit'), null, null, 'primary'); ?>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php
  }
?>
  <div class="clearfix"></div>
<?php
  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('footer.php'));
  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('application_bottom.php'));
