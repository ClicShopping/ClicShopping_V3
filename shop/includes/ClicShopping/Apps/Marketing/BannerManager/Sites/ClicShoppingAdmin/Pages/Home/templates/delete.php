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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  $Qbanner = $CLICSHOPPING_BannerManager->db->get('banners', ['banners_title',
                                                      'banners_id',
                                                      'languages_id'
                                                      ],
                                                      [
                                                       'banners_id' => (int)$_GET['bID']
                                                      ]
                                          );

  $bInfo = new ObjectInfo($Qbanner->toArray());
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/banner_manager.gif', $CLICSHOPPING_BannerManager->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_BannerManager->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle"><strong><?php echo $bInfo->banners_title ; ?></strong></div>
  <?php echo HTML::form('banners', $CLICSHOPPING_BannerManager->link('BannerManager&DeleteConfirm&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'bID=' . $bInfo->banners_id)); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_BannerManager->getDef('text_info_delete_intro'); ?><br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12">
        <span class="col-md-3"><?php echo $bInfo->banners_title ; ?></span>
      </div>
      <div class="separator"></div>
      <div class="col-md-12">
        <span class="col-md-3"><?php echo HTML::checkboxField('delete_image', 'on', true) . ' ' . $CLICSHOPPING_BannerManager->getDef('text_info_delete_image'); ?></span>
      </div>
      <div class="col-md-12 text-md-center">
        <br /><?php echo HTML::button($CLICSHOPPING_BannerManager->getDef('button_delete'), null,null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_BannerManager->getDef('button_cancel'), null, CLICSHOPPING::link('BannerManager&page=' . $_GET['page'] . '&bID=' . $_GET['bID']), 'warning', null, 'sm'); ?>
      </div>
    </div>
  </div>
  </form>
</div>