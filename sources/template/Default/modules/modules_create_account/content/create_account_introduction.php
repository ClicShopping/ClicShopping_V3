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
<div class="col-md-<?php echo $content_width; ?>" id="RowContentintroduction1">
  <div class="separator"></div>
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="modulesCreateAccountIntroductionTextLogin"><?php echo sprintf(CLICSHOPPING::getDef('module_create_account_introduction_text_origin_login', ['store_name' => STORE_NAME]), CLICSHOPPING::link(null, 'Account&LogIn&' . CLICSHOPPING::getAllGET(['Account', 'LogIn']))); ?></div>
<?php
// Gestion de l'apparition des boutons en fonction du mode de vente voulu
  if ((MODE_MANAGEMENT_B2C_B2B == 'B2B') && (MODE_B2B_B2C == 'True')) {
?>
      <div class="modulesCreateAccountIntroductionTextB2B">
        <?php echo CLICSHOPPING::getDef('module_create_account_introduction_text_b2b') . '  ' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&CreatePro'), 'info', null,'sm'); ?>
      </div>
<?php
  } elseif ((MODE_MANAGEMENT_B2C_B2B == 'B2C_B2B') && (MODE_B2B_B2C == 'True')) {
?>
      <div class="modulesCreateAccountIntroductionTextB2bB2c"><?php echo  '<br />' . CLICSHOPPING::getDef('module_create_account_introduction_text_b2b')  . '  ' . HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&CreatePro'), 'info', null,'sm'); ?></div>
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