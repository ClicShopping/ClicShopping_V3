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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;
  use ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin\SeoAdmin;

  use ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin\ManufacturerAdmin;

  $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

  Registry::set('ManufacturerAdmin', new ManufacturerAdmin());
  $CLICSHOPPING_ManufacturerAdmin = Registry::get('ManufacturerAdmin');

  $form_action = 'Insert';
  $variable = '';

  if ( (isset($_GET['Edit']) && isset($_GET['mID']) && !empty($_GET['mID']))) {
    $form_action = 'Update';
    $variable =  '&mID=' . $_GET['mID'];
  }

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

  echo HTMLOverrideAdmin::getCkeditor();
?>

  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/manufacturers.gif', $CLICSHOPPING_Manufacturers->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Manufacturers->getDef('heading_title'); ?></span>
            <span class="col-md-7 text-md-right">
<?php
  echo HTML::form ('manufacturers',  $CLICSHOPPING_Manufacturers->link('Manufacturers&' . $form_action . $variable) );
  if ($form_action == 'Update') echo HTML::hiddenField('manufacturers_id', $_GET['mID']);

  echo HTML::button($CLICSHOPPING_Manufacturers->getDef('button_cancel'), null, $CLICSHOPPING_Manufacturers->link('Manufacturers&page=' . $page . $variable), 'warning') .'&nbsp;';
  echo (($form_action == 'Insert') ? HTML::button($CLICSHOPPING_Manufacturers->getDef('button_insert'), null, null, 'success') : HTML::button($CLICSHOPPING_Manufacturers->getDef('button_update'), null, null, 'success'));
?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
<?php
  if ( (isset($_GET['Edit']) && isset($_GET['mID']) && !empty($_GET['mID']))) {

      $Qmanufacturers = $CLICSHOPPING_Manufacturers->db->prepare('select m.manufacturers_id,
                                                                         m.manufacturers_name,
                                                                         m.manufacturers_image,
                                                                         m.date_added,
                                                                         m.last_modified,
                                                                         md.manufacturer_description,
                                                                         md.manufacturers_url,
                                                                         md.manufacturer_seo_title,
                                                                         md.manufacturer_seo_description,
                                                                         md.manufacturer_seo_keyword,
                                                                         m.manufacturers_id,
                                                                         m.suppliers_id
                                                                  from :table_manufacturers  m,
                                                                       :table_manufacturers_info md
                                                                  where m.manufacturers_id = md.manufacturers_id
                                                                  and md.languages_id = :languages_id
                                                                  and m.manufacturers_id = :manufacturers_id
                                                                ');
      $Qmanufacturers->bindValue(':languages_id', (int)$CLICSHOPPING_Language->getId() );
      $Qmanufacturers->bindValue(':manufacturers_id', (int)$_GET['mID']);
      $Qmanufacturers->execute();


    $mInfo = new ObjectInfo($Qmanufacturers->toArray());

  } else {
    $mInfo = new ObjectInfo(array());
  }
?>
    <div id="manufacturersTabs" style="overflow: auto;">
      <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist"  id="myTab">
        <li class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Manufacturers->getDef('tab_general') . '</a>'; ?></li>
        <li class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Manufacturers->getDef('tab_description'); ?></a></li>
        <li class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Manufacturers->getDef('tab_visuel'); ?></a></li>
        <li class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Manufacturers->getDef('tab_seo'); ?></a></li>
      </ul>

      <div class="tabsClicShopping">
        <div class="tab-content">
<?php
// -- ------------------------------------------------------------ //
// --          ONGLET Information Général de la Marque          //
// -- ------------------------------------------------------------ //
?>
          <div class="tab-pane active" id="tab1">
            <div class="col-md-12 mainTitle">
              <div class="float-md-left"><?php echo $CLICSHOPPING_Manufacturers->getDef('title_manufacturer_general'); ?></div>
            </div>
            <div class="adminformTitle">

              <div class="row" id="manufacturerName">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_name'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_name'); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::inputField('manufacturers_name', $mInfo->manufacturers_name, 'required aria-required="true" id="manufacturers_name" placeholder="' . $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_name') . '"', 'manufacturers_name'); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="separator"></div>
              <div class="row">
                <div class="col-md-12">
                   <span class="col-md-2"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_url'); ?></span>
                </div>
              </div>
<?php
    $languages = $CLICSHOPPING_Language->getLanguages();
    for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
              <div class="form-group row">
                <label for="code" class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                <div class="col-md-5">
                  <?php echo HTML::inputField('manufacturers_url[' . $languages[$i]['id'] . ']', $CLICSHOPPING_ProductsAdmin->getManufacturerUrl($mInfo->manufacturers_id, $languages[$i]['id'])); ?>
                </div>
              </div>
<?php
    }

  $Qsuppliers = $CLICSHOPPING_Manufacturers->db->prepare('select suppliers_id,
                                                                 suppliers_name
                                                           from :table_suppliers
                                                           order by suppliers_name
                                                          ');

  $Qsuppliers->execute();

  if ($Qsuppliers->rowCount() > 0) {
?>

              <div class="separator"></div>
              <div class="row" id="supplierName">
                <label for="code" class="col-2 col-form-label"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_suppliers_suppliers_name'); ?></label>
                <div class="col-md-5">
<?php
    $suppliers_name_array[] = ['id' => 0,
                               'text' => CLICSHOPPING::getDef('text_select')
                              ];



    while($Qsuppliers->fetch()) {
      $suppliers_name_array[] = ['id' => $Qsuppliers->valueInt('suppliers_id'),
                                 'text' => $Qsuppliers->value('suppliers_name')
                                ];
    }


    echo HTML::selectField('suppliers_id', $suppliers_name_array, $mInfo->suppliers_id);
?>
                </div>
              </div>
<?php
  }
?>
            </div>
          </div>
<!-- //################################################################################################################ -->
<!--          ONGLET Information description       //-->
<!-- //################################################################################################################ -->
          <div class="tab-pane" id="tab2">
            <div class="col-md-12 mainTitle">
              <span><?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_description'); ?></span>
            </div>
            <div class="adminformTitle" id="manufactuerDescription">
<?php
    echo HTMLOverrideAdmin::getCkeditor();

    for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group row">
                    <label for="Code" class="col-1 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <div class="col-md-8">
                      <?php echo HTMLOverrideAdmin::textAreaCkeditor('manufacturer_description[' . $languages[$i]['id'] . ']', 'soft', '750', '300', (isset($manufacturer_description[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($manufacturer_description[$languages[$i]['id']])) : $CLICSHOPPING_ManufacturerAdmin->getManufacturerDescription($mInfo->manufacturers_id, $languages[$i]['id']))); ?>
                    </div>
                  </div>
                </div>
              </div>
<?php
    }
?>
            </div>
            <div class="separator"></div>

            <div class="alert alert-info" role="alert">
              <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Manufacturers->getDef('title_help_description')) . ' ' . $CLICSHOPPING_Manufacturers->getDef('title_help_description') ?></div>
              <div class="separator"></div>
              <div><?php echo $CLICSHOPPING_Manufacturers->getDef('text_help_clone'); ?></div>
              <div class="separator"></div>
              <div class="row">
                <span class="col-md-12">
                  <blockquote><i><a data-toggle="modal" data-target="#myModalWysiwyg"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_help_wysiwyg'); ?></a></i></blockquote>
                  <div class="modal fade" id="myModalWysiwyg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_help_wysiwyg'); ?></h4>
                        </div>
                        <div class="modal-body" style="text-align:center;">
                          <img class="img-fluid" src="<?php echo  $CLICSHOPPING_Template->getImageDirectory() . '/wysiwyg.png' ;?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </span>
              </div>
            </div>
          </div>

<!-- //################################################################################################################ -->
<!--          ONGLET Information visuelle          //-->
<!-- //################################################################################################################ -->
          <div class="tab-pane" id="tab3">
            <div class="mainTitle"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturer_image'); ?></div>
            <div class="adminformTitle" id="manufacturerImage">
                <div class="row">
                  <div class="col-md-12">
                    <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/banner_manager.gif', $CLICSHOPPING_Manufacturers->getDef('text_products_image_vignette'), '40', '40'); ?></span>
                    <span class="col-md-3 main"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_products_image_vignette'); ?></span>
                    <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/images_product.gif', $CLICSHOPPING_Manufacturers->getDef('text_products_image_visuel'), '40', '40'); ?></span>
                    <span class="col-md-7 main"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_products_image_visuel'); ?></span>
                  </div>
                  <div class="col-md-12">
                     <div class="adminformAide">
                      <div class="row">
                        <span class="col-md-4 text-md-center float-md-left"><?php echo HTMLOverrideAdmin::fileFieldImageCkEditor('manufacturers_image', null, '212', '212'); ?></span>
                        <span class="col-md-8 text-md-center float-md-right">
                        <div class="col-md-12"><?php echo $CLICSHOPPING_ProductsAdmin->getInfoImage($mInfo->manufacturers_image, $CLICSHOPPING_Manufacturers->getDef('text_products_image_vignette')); ?></div>
                        <div class="col-md-12 text-md-right">
                          <?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturers_image_delete') . ' ' . HTML::checkboxField('delete_image', 'yes', false); ?>
                        </div>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <div class="separator"></div>
            <div class="alert alert-info" role="alert">
              <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Manufacturers->getDef('title_help_image')) . ' ' . $CLICSHOPPING_Manufacturers->getDef('title_help_image') ?></div>
              <div class="separator"></div>
              <div><?php echo $CLICSHOPPING_Manufacturers->getDef('help_image_manufacturers'); ?></div>
            </div>
          </div>
<!-- //################################################################################################################ -->
<!--          ONGLET SEO          //-->
<!-- //################################################################################################################ -->
<!-- decompte caracteres -->
<!--<script type="text/javascript" src="../ext/javascript/charcount/charCount.js'; ?>" ></script>-->
<script type="text/javascript">
  $(document).ready(function(){
<?php
       for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
    //default title
    $("#default_title_<?php echo $i?>").charCount({
      allowed: 70,
      warning: 20,
      counterText: ' Max : '
    });

    //default_description
    $("#default_description_<?php echo $i?>").charCount({
      allowed: 150,
      warning: 20,
      counterText: 'Max : '
    });

    //default tag
    $("#default_tag_<?php echo $i?>").charCount({
      allowed: 70,
      warning: 20,
      counterText: ' Max : '
    });

<?php
       }
?>
  });
</script>

          <div class="tab-pane" id="tab4">
            <div class="col-md-12 mainTitle">
              <div class="float-md-left"><?php echo $CLICSHOPPING_Manufacturers->getDef('title_manufacturer_seo'); ?></div>
            </div>
            <div class="adminformTitle">
              <div>&nbsp;</div>
              <div class="row">
                <div class="col-md-12 text-md-center">
                  <span class="col-md-3"></span>
                  <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"><?php echo $CLICSHOPPING_Manufacturers->getDef('keywords_google_trend'); ?></a></span>
                  <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank"><?php echo $CLICSHOPPING_Manufacturers->getDef('analysis_google_tool'); ?></a></span>
                </div>
              </div>
<?php
    for ($i=0, $n=count($languages); $i<$n; $i++) {
?>

              <div class="row">
                <div class="col-md-1">
                  <div class="form-group row">
                    <label for="Code" class="col-1 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturer_seo_title'); ?>" class="col-1 col-form-label"><?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturer_seo_title'); ?></label>
                    <div class="col-md-8">
                      <?php echo HTML::inputField('manufacturer_seo_title[' . $languages[$i]['id'] . ']', (($manufacturer_seo_title[$languages[$i]['id']]) ? $manufacturer_seo_title[$languages[$i]['id']] : SeoAdmin::getManufacturerSeoTitle($mInfo->manufacturers_id, $languages[$i]['id'])),'maxlength="70" size="77" id="default_title_'.$i.'"', false); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Manufacturers->getDef('text_manufacturer_seo_description'); ?>" class="col-1 col-form-label"><?php echo $CLICSHOPPING_Manufacturers->getDef('title_manufacturer_seo_description'); ?></label>
                    <div class="col-md-8">
                      <?php echo HTML::textAreaField('manufacturer_seo_description[' . $languages[$i]['id'] . ']', (isset($manufacturer_seo_description[$languages[$i]['id']]) ? $manufacturer_seo_description[$languages[$i]['id']] : SeoAdmin::getManufacturerSeoDescription($mInfo->manufacturers_id, $languages[$i]['id'])), '75', '2', 'id="default_description_'.$i.'"'); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Manufacturers->getDef('title_manufacturer_seo_keywords'); ?>" class="col-1 col-form-label"><?php echo $CLICSHOPPING_Manufacturers->getDef('title_manufacturer_seo_keywords'); ?></label>
                    <div class="col-md-8">
                      <?php echo HTML::textAreaField('manufacturer_seo_keyword[' . $languages[$i]['id'] . ']', (isset($manufacturer_seo_keyword[$languages[$i]['id']]) ? $manufacturer_seo_keyword[$languages[$i]['id']] : SeoAdmin::getManufacturerSeoKeyword($mInfo->manufacturers_id, $languages[$i]['id'])), '75', '5'); ?>
                    </div>
                  </div>
                </div>
              </div>

<?php
    }
?>
            </div>
            <div class="separator"></div>
            <div class="alert alert-info" role="alert">
              <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Manufacturers->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_Manufacturers->getDef('title_help_submit') ?></div>
              <div class="separator"></div>
              <div><?php echo $CLICSHOPPING_Manufacturers->getDef('help_submit'); ?></div>
            </div>
          </div>
        <div class="separator"></div>
        <?php echo $CLICSHOPPING_Hooks->output('Catalog', 'ManufacturersTab4', null, 'display'); ?>
      </div>
    </div>
  </div>
</form>

</div>

