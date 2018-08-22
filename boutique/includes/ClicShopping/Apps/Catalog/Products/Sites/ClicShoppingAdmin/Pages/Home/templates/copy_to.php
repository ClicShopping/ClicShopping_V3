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
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Products = Registry::get('Products');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

  $Qproducts = $CLICSHOPPING_Products->db->prepare('select  p.products_id,
                                                             pd.products_name
                                                     from :table_products p,
                                                          :table_products_description pd,
                                                          :table_products_to_categories p2c
                                                     where p.products_id = pd.products_id
                                                     and pd.language_id = :language_id
                                                     and p.products_id = p2c.products_id
                                                     and p.products_id = :products_id
                                                    ');

  $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
  $Qproducts->bindInt(':products_id', (int)$_GET['pID']);

  $Qproducts->execute();

  $pInfo = new ObjectInfo($Qproducts->toArray());
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/produit.gif', $CLICSHOPPING_Products->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Products->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>

    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Products->getDef('text_info_heading_copy_to'); ?></strong></div>
    <?php echo HTML::form('copy_to', $CLICSHOPPING_Products->link('Products&CopyConfirm&cPath=' . $cPath)) . HTML::hiddenField('products_id', $pInfo->products_id) . HTML::hiddenField('current_category_id', $current_category_id); ?>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $CLICSHOPPING_Products->getDef('text_info_copy_to_intro'); ?><br/><br/></div>
        <div class="separator"></div>
        <div class="col-md-12">
          <span class="col-sm-2"><?php echo $CLICSHOPPING_Products->getDef('text_info_current_categories'); ?></span>
          <span class="col-sm-10"><strong><?php echo $CLICSHOPPING_CategoriesAdmin->getOutputGeneratedCategoryPath($pInfo->products_id, 'product'); ?> </strong></span>
        </div>
        <div class="separator"></div>
        <div class="col-md-12">
          <span class="col-sm-2"><?php echo $CLICSHOPPING_Products->getDef('text_categories'); ?></span>
          <span class="col-sm-4"><br /><?php echo HTML::selectMenu('categories_id', $CLICSHOPPING_CategoriesAdmin->getCategoryTree(), $current_category_id); ?></span>
        </div>
        <div class="col-md-12">
          <span class="col-sm-2"><?php echo $CLICSHOPPING_Products->getDef('text_how_to_copy'); ?></span>
          <span class="col-sm-4"><br /><?php echo HTML::radioField('copy_as', 'link', true) . ' ' . $CLICSHOPPING_Products->getDef('text_copy_as_link') . '<br />' . HTML::radioField('copy_as', 'duplicate') . ' ' . $CLICSHOPPING_Products->getDef('text_copy_as_duplicate'); ?></span>
        </div>
        <div class="separator"></div>
        <div class="col-md-12 text-md-center">
          <span><br /><?php echo HTML::button($CLICSHOPPING_Products->getDef('button_copy'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Products->getDef('button_cancel'), null, $CLICSHOPPING_Products->link('Products&cPath=' . $cPath . '&pID=' . $pInfo->products_id), 'warning', null, 'sm'); ?></span>
        </div>
      </div>
    </div>
    </form>
  </div>

