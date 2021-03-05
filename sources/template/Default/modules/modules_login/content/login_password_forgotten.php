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
            <label for="inputEmail" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_email_address'); ?></label>
            <div class="col-md-6">
              <?php echo HTML::inputField('email_address', null, 'required aria-required="true" id="inputEmail" aria-describedby="' . CLICSHOPPING::getDef('module_login_connexion_entry_email_address') . '" placeholder="' . CLICSHOPPING::getDef('module_login_connexion_entry_email_address') . '"', 'email'); ?>
            </div>
          </div>
        </div>
      </div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div class="control-group">
        <div>
          <div class="buttonSet">
            <span class="col-md-2"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null, 'Account&LogIn'), 'primary');  ?></span>
            <span class="col-md-2 float-end text-end"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'success');  ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  echo  $endform;

