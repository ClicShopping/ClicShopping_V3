<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Reviews = Registry::get('Reviews');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('reviews')) {
    echo $CLICSHOPPING_MessageStack->get('reviews');
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/reviews.gif', $CLICSHOPPING_Reviews->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Reviews->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Reviews->getDef('text_reviews'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12">
        <div>
          <div class="col-md-12">
            <?php echo $CLICSHOPPING_Reviews->getDef('text_intro'); ?>
          </div>
        </div>
        <div class="separator"></div>
        <div class="col-md-12">
          <div>
            <div class="col-md-12 text-center">
              <?php
                echo HTML::form('configure', CLICSHOPPING::link(null, 'A&Customers\Reviews&Configure'));
                echo HTML::button($CLICSHOPPING_Reviews->getDef('button_configure'), null, null, 'primary');
                echo '</form>';
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

