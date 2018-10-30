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

  echo $form;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="page-header"><h1><?php echo CLICSHOPPING::getDef('heading_title_password'); ?></h1></div>
  <div class="separator"></div>

  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="CurrentPassword" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password_current'); ?></label>
        <div class="col-md-8">
<?php
  echo HTML::inputField('password_current', null, 'required aria-required="true" autofocus="autofocus" id="CurrentPassword" aria-describedby="' . CLICSHOPPING::getDef('entry_password_current') . '" placeholder="' . CLICSHOPPING::getDef('entry_password_current') . '"', 'password');
  if (!is_null(CLICSHOPPING::getDef('entry_password_current_text'))) echo '<span class="form-text">' . CLICSHOPPING::getDef('entry_password_current_text') . '</span>';
?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="inputPasswordNew" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password_new'); ?></label>
        <div class="col-md-8">
          <?php
            echo HTML::inputField('password_new', null, 'required aria-required="true" id="inputPasswordNew" aria-describedby="' . CLICSHOPPING::getDef('entry_password_new') . '" placeholder="' . CLICSHOPPING::getDef('entry_password_new') . '"  minlength="' . ENTRY_PASSWORD_MIN_LENGTH . '"', 'password');
            if (!is_null(CLICSHOPPING::getDef('entry_password_current_text'))) echo '<span class="form-text">' . CLICSHOPPING::getDef('entry_password_current_text') . '</span>';
          ?>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="inputPasswordConfirmation" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password_confirmation'); ?></label>
        <div class="col-md-8">
<?php
  echo HTML::inputField('password_confirmation', null, 'required aria-required="true" id="inputPasswordConfirmation" aria-describedby="' . CLICSHOPPING::getDef('entry_password_confirmation') . '" placeholder="' . CLICSHOPPING::getDef('entry_password_confirmation') . '"  minlength="' . ENTRY_PASSWORD_MIN_LENGTH . '"', 'password');
  if (!is_null(CLICSHOPPING::getDef('entry_password_current_text'))) echo '<span class="form-text">' . CLICSHOPPING::getDef('entry_password_current_text') . '</span>';
?>
        </div>
      </div>
    </div>
  </div>

   <div class="col-md-12">
    <div class="control-group">
      <div class="controls">
        <div class="buttonSet">
          <span class="col-md-2"><?php echo $back_button;  ?></span>
          <span class="col-md-2 float-md-right text-md-right"><?php echo $process_button;  ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
  echo $endform;