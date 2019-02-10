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
?>
<div class="col-md-12 text-md-center"><?php echo $menu_footer; ?></div>
<div class="d-flex flex-wrap footerTemplate text-md-left">
  <div class="col-md-<?php echo $content_width; ?>">
    <div class="footerTemplateBox footerTemplateAccount">
      <h2><?php echo CLICSHOPPING::getDef('module_footer_multi_template_account_heading_title'); ?></h2>
      <ul class="list-unstyled">
<?php
  if ($CLICSHOPPING_Customer->isLoggedOn()) {
?>
        <li><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Main'), CLICSHOPPING::getDef('module_footer_multi_template_account_box_account')); ?></li>
        <li><?php echo HTML::link(CLICSHOPPING::link(null,'Account&AddressBook'), CLICSHOPPING::getDef('module_footer_multi_template_account_box_address_book')); ?></li>
        <li><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&History'), CLICSHOPPING::getDef('module_footer_multi_template_account_box_address_book')); ?></li>
<br />
          <a class="btn btn-danger btn-sm btn-block" role="button" href="<?php echo CLICSHOPPING::link(null, 'Account&Logoff') ?>"><i class="fas fa-sign-out-alt"></i><?php echo CLICSHOPPING::getDef('module_footer_multi_template_account_box_logoff') ?></a>
<?php
  } else {
?>
        <li><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Create'), CLICSHOPPING::getDef('module_footer_multi_template_account_box_create_account')); ?></li>
        <li class="col-md-12"><br /><a class="btn btn-success btn-sm btn-block" role="button" href="<?php echo CLICSHOPPING::link(null, 'Account&LogIn'); ?>"><i class="fas fa-sign-in-alt"></i> <?php echo CLICSHOPPING::getDef('module_footer_multi_template_account_box_login') ?></a></li>
<?php
  }
?>
      </ul>
    </div>
  </div>

  <div class="col-md-<?php echo $content_width; ?>">
    <div class="footerTemplateBox generic-text">
      <h2><?php echo CLICSHOPPING::getDef('module_footer_multi_template_text_heading_title'); ?></h2>
      <?php echo CLICSHOPPING::getDef('module_footer_multi_template_text_text'); ?>
    </div>
  </div>

  <div class="col-md-<?php echo $content_width; ?>">
    <div class="footerTemplateBox footerTemplateInformation">
      <h2><?php echo CLICSHOPPING::getDef('module_footer_multi_template_information_heading_title'); ?></h2>
      <span itemscope itemtype="https://schema.org/Organization">
          <link itemprop="url" href="<?php echo CLICSHOPPING::getConfig('http_server', 'Shop'); ?>">
            <ul class="footerTemplateSocial">
<?php
  if (!empty($facebook_url)) {
?>
              <li><a itemprop="Facebook" aria-label="Facebook" href="<?php echo $facebook_url;?>" target="_blank" rel="noreferrer"><i class="fab fa-facebook-f"></i></a></li>
<?php
  }
  if (!empty($twitter_url)) {
?>
              <li><a itemprop="Twitter" aria-label="Twitter" href="<?php echo $twitter_url;?>" target="_blank" rel="noreferrer"><i class="fab fa-twitter"></i></a></li>
<?php
  }
  if (!empty($pinterest_url)) {
?>
              <li><a itemprop="Pinterest" aria-label="Pinterest" href="<?php echo $pinterest_url;?>" target="_blank" rel="noreferrer"><i class="fab fa-pinterest"></i></a></li>
<?php
  }
?>
          </ul>
        </span>
    </div>
    <div class="separator"></div>
    <div class="separator"></div>
  </div>


  <div class="col-md-<?php echo $content_width; ?>">
    <div class="footerTemplateBox footerTemplateContact">
      <h2><?php echo CLICSHOPPING::getDef('module_footer_multi_template_contact_us_email_link'); ?></h2>
      <address>
        <strong><?php echo HTML::outputProtected(STORE_NAME); ?></strong><br />
        <?php echo nl2br(STORE_NAME_ADDRESS); ?><br />
      </address>
      <ul class="list-unstyled">
        <li><?php echo HTML::button(CLICSHOPPING::getDef('module_footer_multi_template_contact_us_email_link'), 'fas fa-paper-plane', 'index.php?Info&Contact', 'info'); ?></li>
      </ul>
    </div>
  </div>
</div>