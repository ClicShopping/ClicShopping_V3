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

  $CLICSHOPPING_Featured = Registry::get('Featured');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('Featured')) {
    echo $CLICSHOPPING_MessageStack->get('Featured');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_options.gif', $CLICSHOPPING_Featured->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Featured->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="alert alert-warning">
      <?php echo $CLICSHOPPING_Featured->getDef('text_intro_fa'); ?>
      <?php echo $CLICSHOPPING_Featured->getDef('return_url', ['return_url_fe' => CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'index.php?Products&Featured']);  ?>
    </div>
    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Featured->getDef('text_products_featured') ; ?></strong></div>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>

        <div class="col-md-12">
          <div class="form-group">
            <div class="col-md-12">
              <?php echo $CLICSHOPPING_Featured->getDef('text_intro');  ?>
            </div>
          </div>
          <div class="separator"></div>
          <div class="col-md-12">
            <div class="form-group">
              <div class="col-md-12 text-md-center">
<?php
  echo HTML::form('configure', CLICSHOPPING::link(null, 'A&Marketing\Featured&Configure'));
  echo HTML::button($CLICSHOPPING_Featured->getDef('button_configure'), null, null, 'primary');
  echo '</form>';
?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
