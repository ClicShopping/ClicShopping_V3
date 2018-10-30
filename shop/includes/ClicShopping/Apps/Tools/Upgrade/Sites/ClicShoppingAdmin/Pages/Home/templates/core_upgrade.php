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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Upgrade = Registry::get('Upgrade');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if ($CLICSHOPPING_MessageStack->exists('header')) {
    echo $CLICSHOPPING_MessageStack->get('header');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <div class="col-md-1 logiHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/modules_modules_products_featured.gif',$CLICSHOPPING_Upgrade->getDef('heading_title'), '40', '40'); ?></div>
            <div class="col-md-3 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('heading_title'); ?></div>
            <div class="col-md-8 text-md-right"><?php echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_back'), null, $CLICSHOPPING_Upgrade->link('Upgrade'), 'primary') . '&nbsp;'; ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="separator"></div>
    <div stype ="padding-top:5rem"><?php echo $CLICSHOPPING_Upgrade->getDef('text_step_upgrade'); ?></div>
    <div class="separator"></div>
<?php
  if (!FileSystem::isWritable(CLICSHOPPING::BASE_DIR . 'Work/OnlineUpdates')) {
?>
    <div class="alert alert-danger" role="alert">
      <p><?php echo $CLICSHOPPING_Upgrade->getDef('error_directory_not_writable'); ?></p>
    </div>
<?php
  } else {
?>
    <div class="separator"></div>
<?php
  }
?>
  </div>

