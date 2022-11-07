<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Common\Topt;

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="LogInAuth" id="LogInAuth">
  <div class="contentContainer">
    <div class="contentText">
      <?php
        if (empty($_SESSION['user_secret'])) {
      ?>
      <div class="page-title loginText"><h1><?php echo CLICSHOPPING::getDef('heading_title_Login_auth'); ?></h1></div>
      <div class="separator"></div>
      <div class="col-md-12 mainLogin"><?php echo CLICSHOPPING::getDef('text_Login_auth_introduction'); ?></div>
      <div class="separator"></div>
      <div class="col-md-12 text-center">
        <p><?php echo CLICSHOPPING::getDef('text_auth_qr_code'); ?></p>
        <?php echo Topt::getImageTopt(CLICSHOPPING_TOTP_SHORT_TILTE, $_SESSION['tfa_secret']); ?>
        <div class="separator"></div>
        <div class="separator"></div>
        <?php echo HTML::form('double_authentification', CLICSHOPPING::link(null, 'Account&LogInAuth&Process'), 'post', 'role="form" id="double_authentfication"', ['tokenize' => true, 'action' => 'process']); ?>
          <div class="row">
            <span class="col-md-3"></span>
            <span class="col-md-3"><?php echo HTML::inputField('tfa_code', null, 'aria-required="true" required placeholder="' . CLICSHOPPING::getDef('text_auth_code') . '"'); ?></span>
            <span class="col-md-3"><label for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null,'success'); ?></label></span>
          </div>
        </form>
      </div>
      <?php
        }
      ?>
    </div>
  </div>
</section>