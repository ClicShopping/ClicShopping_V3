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

  $CLICSHOPPING_StatsProductsViewed = Registry::get('StatsProductsViewed');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('StatsProductsViewed')) {
    echo $CLICSHOPPING_MessageStack->get('StatsProductsViewed');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/stats_products_viewed.gif', $CLICSHOPPING_StatsProductsViewed->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_StatsProductsViewed->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_StatsProductsViewed->getDef('text_stats_products_viewed') ; ?></strong></div>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>

        <div class="col-md-12">
          <div class="form-group">
            <div class="col-md-12">
              <?php echo $CLICSHOPPING_StatsProductsViewed->getDef('text_intro');  ?>
            </div>
          </div>
          <div class="separator"></div>

          <div class="col-md-12 text-md-center">
            <div class="form-group">
              <div class="col-md-12">
<?php
  echo HTML::form('configure', CLICSHOPPING::link('index.php', 'A&Report\StatsProductsViewed&Configure'));
  echo HTML::button($CLICSHOPPING_StatsProductsViewed->getDef('button_configure'), null, null, 'primary');
  echo '</form>';
?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
