<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="contentText">
      <div class="modulesCreateAccountProIntroductionTextLogin"><?php echo sprintf(CLICSHOPPING::getDef('module_create_account_pro_introduction_text_origin_login', ['store_name' => STORE_NAME]), CLICSHOPPING::link(null, 'Account&LogIn&' . CLICSHOPPING::getAllGET(['Account', 'LogIn']))); ?></div>
<?php 
// Gestion de l'apparition des boutons en fonction du mode de vente voulu
  if ((MODE_MANAGEMENT_B2C_B2B == 'B2C') && (MODE_B2B_B2C == 'true')) {
?>
      <div class="modulesCreateAccountProIntroductionTextB2B">
        <?php echo CLICSHOPPING::getDef('module_create_account_pro_introduction_text_b2b') . '  ' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&Create'), 'info', null, 'sm'); ?>
      </div>
<?php
  } else if ((MODE_MANAGEMENT_B2C_B2B == 'B2C_B2B') && (MODE_B2B_B2C == 'true')) {
?>
      <div class="modulesCreateAccountProIntroductionTextB2bB2c"><?php echo CLICSHOPPING::getDef('module_create_account_pro_introduction_text_b2b') . '  ' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&Create'), 'info', null, 'sm'); ?></div>
<?php
  } else {
?>
      <div></div>
<?php
  }
?>
      <div class="separator"></div>
      <div class="hr"></div>
    </div>
  </div>
  <div class="separator"></div>
</div>