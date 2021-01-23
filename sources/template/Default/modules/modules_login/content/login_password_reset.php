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

  echo $form;
?>
  <div class="col-md-<?php echo $content_width; ?>">
    <div class="separator"></div>
    <div><?php echo CLICSHOPPING::getDef('text_main'); ?></div>
    <div class="separator"></div>

    <div class="row">
      <div class="col-md-7">
        <div class="form-group row">
          <label for="inputPassword" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password'); ?></label>
          <div class="col-md-6">
            <?php echo HTML::inputField('password', null, 'required aria-required="true" id="inputPassword" aria-describedby="' . CLICSHOPPING::getDef('module_login_connexion_entry_password') . '" placeholder="' . CLICSHOPPING::getDef('module_login_connexion_entry_password') . '"', 'password'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-7">
        <div class="form-group row">
          <label for="inputConfirmation" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password_confirmation'); ?></label>
          <div class="col-md-6">
            <?php echo HTML::inputField('confirmation', null, 'required aria-required="true" id="inputConfirmation" autocomplete="new-password" placeholder="' . CLICSHOPPING::getDef('entry_password_confirmation') . '"', 'password'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="separator"></div>
    <div class="control-group">
      <div>
        <div class="buttonSet">
          <span class="float-end"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
<?php
  echo  $endform;