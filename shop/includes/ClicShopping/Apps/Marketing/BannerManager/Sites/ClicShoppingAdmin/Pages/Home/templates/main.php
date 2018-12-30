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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_BannerManager = Registry::get('BannerManager');

  if ($CLICSHOPPING_MessageStack->exists('BannerManager')) {
    echo $CLICSHOPPING_MessageStack->get('BannerManager');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/banner_manager.gif', $CLICSHOPPING_BannerManager->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_BannerManager->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_BannerManager->getDef('text_products_fbanner_manager') ; ?></strong></div>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>

        <div class="col-md-12">
          <div class="form-group">
            <div class="col-md-12">
              <?php echo $CLICSHOPPING_BannerManager->getDef('text_intro');  ?>
            </div>
          </div>

          <div class="separator"></div
          <div class="col-md-12">
            <div class="form-group">
              <div class="col-md-12 text-md-center">
<?php
  echo HTML::form('configure', CLICSHOPPING::link(null, 'A&Marketing\BannerManager&Configure'));
  echo HTML::button($CLICSHOPPING_BannerManager->getDef('button_configure'), null, null, 'primary');
  echo '</form>';
?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
