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
?>
  <div class="col-md-<?php echo $content_width; ?>">
    <div class="row">
      <div class="separator"></div>
      <span class="col-md-4 headerMultiTemplateDefaultLogo"><?php echo $logo_header; ?></span>
      <span class="col-md-8">
        <span class="text-md-right headerMultiTemplateDefaultTitle">
<?php
  if (!$CLICSHOPPING_Customer->isLoggedOn()) {
?>
          <a data-toggle="modal" data-target="#loginModal"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_account_login'); ?></a> |
          <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="myModalLabel"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_account_login') ?></h4>
                </div>
                <div class="modal-body text-md-center">
                  <?php echo $form; ?>
                  <div class="separator"></div>
                  <div class="row">
                    <div class="col-md-12">
                      <label for="inputAddressEmailLogin" class="sr-only"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_header_email_address'); ?></label>
                      <span class="col-md-3 float-md-left text-md-left headerMultiTemplateDefaultLoginText"  id="inputAddressEmailLogin"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_header_email_address'); ?></span>
                      <span class="col-md-9 float-md-right"><?php echo HTML::inputField('email_address', null, 'id="inputAddressEmail" aria-describedby="' . CLICSHOPPING::getDef('modules_header_multi_template_header_email_address') . '" placeholder="' . CLICSHOPPING::getDef('modules_header_multi_template_header_email_address') . '"', 'email'); ?></span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label for="inputAddressPasswordLogin" class="sr-only"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_account_password'); ?></label>
                      <span class="col-md-3 float-md-left text-md-left headerMultiTemplateDefaultPasswordText" id="inputAddressPasswordLogin"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_account_password'); ?></span>
                      <span class="col-md-9 float-md-right"><?php echo HTML::inputField('password', null, 'id="inputAddressPassword" aria-describedby="' . CLICSHOPPING::getDef('modules_header_multi_template_account_password') . '" placeholder="' . CLICSHOPPING::getDef('modules_header_multi_template_account_password') . '"', 'password'); ?></span>
                    </div>
                  </div>
                  <div class="separator"></div>
                  <div>
                    <span class="headerMultiTemplateDefaultPassword col-md-6"><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&PasswordForgotten'), CLICSHOPPING::getDef('modules_header_multi_template_password_forgotten')); ?>
                    <span class="text-md-right col-md-6"><?php echo $login; ?></span>
                  </div>
                  <?php echo $endform; ?>
                </div>
              </div>
            </div>
          </div>
<?php
  } else {
?>
          <span>
            <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&LogOff'), CLICSHOPPING::getDef('modules_header_multi_template_account_logoff'));?> |
<?php
      if ($CLICSHOPPING_Customer->getCustomerGuestAccount($CLICSHOPPING_Customer->getID()) == 0) {
        echo HTML::link(CLICSHOPPING::link(null, 'Account&Main'), CLICSHOPPING::getDef('modules_header_multi_template_my_account')) .  ' | ';
      }
?>
</span>
<?php
  }
  if (!$CLICSHOPPING_Customer->isLoggedOn()) {
?>
          <span><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Login'), CLICSHOPPING::getDef('modules_header_multi_template_create_account')); ?> | </span>
 <?php
  }
?>
          <span><?php echo HTML::link(CLICSHOPPING::link(null, 'Info&Contact'), CLICSHOPPING::getDef('modules_header_multi_template_title_contact_us')); ?></span>
        </span>

        <span class="float-md-right headerMultiTemplateDefaultLanguage"><li class="headerMultiTemplateDefaultLanguage"><?php echo $languages_string; ?></li></span>
        <span class="col-md-6 float-md-left headerMultiTemplateDefaultHeaderSearch" style="padding-top:5rem">
          <?php echo $form_advanced_result; ?>
          <div class="input-group col-md-12 advancedSearchCriteria">
            <label for="inputKeywordsSearchLogin" class="sr-only"><?php echo CLICSHOPPING::getDef('modules_header_multi_template_header_search'); ?></label>
            <?php echo HTML::inputField('keywords', null, 'required aria-required="true" id="inputKeywordsSearchLogin" aria-describedby="' . CLICSHOPPING::getDef('modules_header_multi_template_header_search') . '" placeholder="' . CLICSHOPPING::getDef('modules_header_multi_template_header_search') . '"', 'search'); ?>
            <span id="buttonKeywordsSearch"><?php echo HTML::button(null, 'fas fa-search', null, 'primary', null, 'md'); ?></span>
          </div>
          <?php echo HTML::hiddenField('search_in_description', '1'); ?>
          <?php echo $endform; ?>
        </span>
        <span class="col-md-5 float-md-right headerMultiTemplateDefaultShoppingCartImage"><?php echo $banner_header; ?></span>
      </span>
    </div>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 text-md-right">
        <span class="headerMultiTemplateDefaultShoppingCart"><i class="fas fa-shopping-cart fa-2x" aria-hidden="true"></i></span>
        <span class="headerMultiTemplateDefaultCartLink"><?php echo HTML::link(CLICSHOPPING::link(null, 'Cart'), $shopping_cart); ?></span>
      </div>
    </div>
  </div>
  <div class="separator"></div>
