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

  $CLICSHOPPING_SEO = Registry::get('SEO');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('SEO')) {
    echo $CLICSHOPPING_MessageStack->get('SEO');
  }

  $Qpage = $CLICSHOPPING_SEO->db->prepare('select  p.submit_id,
                                                  p.language_id,
                                                  p.submit_defaut_language_title,
                                                  p.submit_defaut_language_keywords,
                                                  p.submit_defaut_language_description,
                                                  p.submit_defaut_language_footer,
                                                  p.submit_language_products_info_title,
                                                  p.submit_language_products_info_keywords,
                                                  p.submit_language_products_info_description,
                                                  p.submit_language_products_new_title,
                                                  p.submit_language_products_new_keywords,
                                                  p.submit_language_products_new_description,
                                                  p.submit_language_special_title,
                                                  p.submit_language_special_keywords,
                                                  p.submit_language_special_description,
                                                  p.submit_language_reviews_title,
                                                  p.submit_language_reviews_keywords,
                                                  p.submit_language_reviews_description
                                         from :table_submit_description p
                                         where p.submit_id = 1
                                        ');
  $Qpage->execute();

  while($Qpage->fetch())  {

    $languageid = $Qpage->valueInt('language_id');
    $submit_defaut_language_title[$languageid]  = $Qpage->value('submit_defaut_language_title');
    $submit_defaut_language_keywords[$languageid]  = $Qpage->value('submit_defaut_language_keywords');
    $submit_defaut_language_description[$languageid]  = $Qpage->value('submit_defaut_language_description');
    $submit_defaut_language_footer[$languageid]  = $Qpage->value('submit_defaut_language_footer');
    $submit_language_products_info_title[$languageid]  = $Qpage->value('submit_language_products_info_title');
    $submit_language_products_info_keywords[$languageid] = $Qpage->value('submit_language_products_info_keywords');
    $submit_language_products_info_description[$languageid] = $Qpage->value('submit_language_products_info_description');
    $submit_language_products_new_title[$languageid] = $Qpage->value('submit_language_products_new_title');
    $submit_language_products_new_keywords[$languageid]  = $Qpage->value('submit_language_products_new_keywords');
    $submit_language_products_new_description[$languageid] 	= $Qpage->value('submit_language_products_new_description');
    $submit_language_special_title[$languageid]  = $Qpage->value('submit_language_special_title');
    $submit_language_special_keywords[$languageid] = $Qpage->value('submit_language_special_keywords');
    $submit_language_special_description[$languageid] = $Qpage->value('submit_language_special_description');
    $submit_language_reviews_title[$languageid]  = $Qpage->value('submit_language_reviews_title');
    $submit_language_reviews_keywords[$languageid]  = $Qpage->value('submit_language_reviews_keywords');
    $submit_language_reviews_description [$languageid] = $Qpage->value('submit_language_reviews_description');
  }

  $languages = $CLICSHOPPING_Language->getLanguages();

  echo HTML::form('seo', $CLICSHOPPING_SEO->link('SEO&Update'));
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/referencement.gif', $CLICSHOPPING_SEO->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_SEO->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-md-right"><?php echo  HTML::button($CLICSHOPPING_SEO->getDef('button_update'), null, null, 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- ############################################################# //-->
  <!--          ONGLET Information General de la Page Accueil          //-->
  <!-- ############################################################# //-->

  <!-- decompte caracteres -->

  <script type="text/javascript">
    $(document).ready(function(){
<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
      //default title
      $("#default_title_<?php echo $i; ?>").charCount({
        allowed: 70,
        warning: 20,
        counterText: ' Max : '
      });

      //default_description
      $("#default_description_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });

      //products_info title
      $("#products_info_title_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });

      //products_info description
      $("#products_info_description_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });

//products_new title
      $("#products_new_title_<?php echo $i; ?>").charCount({
        allowed: 70,
        warning: 20,
        counterText: ' Max : '
      });

// products_new description
      $("#products_new_description_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });

//specials title
      $("#specials_title_<?php echo $i; ?>").charCount({
        allowed: 70,
        warning: 20,
        counterText: ' Max : '
      });

//specials description
      $("#specials_description_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });

//reviews title
      $("#reviews_title_<?php echo $i; ?>").charCount({
        allowed: 70,
        warning: 20,
        counterText: ' Max : '
      });

//reviews description
      $("#reviews_description_<?php echo $i; ?>").charCount({
        allowed: 150,
        warning: 20,
        counterText: 'Max : '
      });
<?php
  }
?>
    });
  </script>

  <div id="pagesSubmitTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_SEO->getDef('tab_submit_default') . '</a>'; ?></li>
      <li class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_products_info'); ?></a></li>
      <li class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_products_new'); ?></a></li>
      <li class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_specials'); ?></a></li>
      <li class="nav-item"><?php echo '<a href="#tab5" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_reviews'); ?></a></li>
    </ul>


    <div class="tabsClicShopping">
      <div class="tab-content">
<!-- ############################################################# //-->
<!--          ONGLET Information General                          //-->
<!-- ############################################################# //-->
        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_default'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-md-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>
<?php


  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_defaut_language_title_'.$languages[$i]['id'], $submit_defaut_language_title[$languages[$i]['id']], 'maxlength="70" size="77" id="default_title_'.$i.'"', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_description'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_defaut_language_description_'.$languages[$i]['id'],  (isset($submit_defaut_language_description[$languages[$i]['id']]) ? $submit_defaut_language_description[$languages[$i]['id']] : $submit_defaut_language_description), '75', '2', 'id="default_description_'.$i.'"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_keywords'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_defaut_language_keywords_'.$languages[$i]['id'], (isset($submit_defaut_language_keywords[$languages[$i]['id']]) ? $submit_defaut_language_keywords[$languages[$i]['id']] : $submit_defaut_language_keywords), '150', '2'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_footer'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_footer'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_defaut_language_footer_'.$languages[$i]['id'],(isset($submit_defaut_language_footer[$languages[$i]['id']]) ? $submit_defaut_language_footer[$languages[$i]['id']] : $submit_defaut_language_footer ), '150', '2'); ?>
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
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_SEO->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>
<!-- ############################################################# //-->
<!--     ONGLET Information General de page information produit   //-->
<!-- ############################################################# //-->
        <div class="tab-pane" id="tab2">

          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_products_info'); ?></div>
          </div>

          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-md-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>
<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang1" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_language_products_info_title_'.$languages[$i]['id'], $submit_language_products_info_title[$languages[$i]['id']], 'maxlength="50" size="77" id="products_info_title_'.$i.'"', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_description'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::textAreaField('submit_language_products_info_description_'.$languages[$i]['id'], (isset($submit_language_products_info_description[$languages[$i]['id']]) ? $submit_language_products_info_description[$languages[$i]['id']] : $submit_language_products_info_description ), '75', '2', 'id="products_info_description_'.$i.'"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_keywords'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_language_products_info_keywords_'.$languages[$i]['id'], (isset($submit_language_products_info_keywords[$languages[$i]['id']]) ? $submit_language_products_info_keywords[$languages[$i]['id']] : $submit_language_products_info_keywords), '150', '2'); ?>
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
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_SEO->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>

<!-- ############################################################# //-->
<!--          ONGLET Information General de la page nouveautes          //-->
<!-- ############################################################# //-->

        <div class="tab-pane" id="tab3">

          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_products_new'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-md-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>

<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang1" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_language_products_new_title_'.$languages[$i]['id'], $submit_language_products_new_title[$languages[$i]['id']], 'maxlength="50" size="77" id="products_new_title_'.$i.'"', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_description'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::textAreaField('submit_language_products_new_description_'.$languages[$i]['id'],  (isset($submit_language_products_new_description[$languages[$i]['id']]) ? $submit_language_products_new_description[$languages[$i]['id']] : $submit_language_products_new_description), '75', '2', 'id="products_new_description_'.$i.'"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_keywords'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_language_products_new_keywords_'.$languages[$i]['id'],(isset($submit_language_products_new_keywords[$languages[$i]['id']]) ? $submit_language_products_new_keywords[$languages[$i]['id']] : $submit_language_products_new_keywords), '150', '2'); ?>
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
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_SEO->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>

<!-- ############################################################# //-->
<!--          ONGLET Information General de la page promotion          //-->
<!-- ############################################################# //-->

        <div class="tab-pane" id="tab4">

          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_specials'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-md-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>

<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang1" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_language_special_title_'.$languages[$i]['id'], $submit_language_special_title[$languages[$i]['id']], 'maxlength="50" size="77" id="specials_title_'.$i.'"', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_description'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::textAreaField('submit_language_special_description_'.$languages[$i]['id'],(isset($submit_language_special_description[$languages[$i]['id']]) ? $submit_language_special_description[$languages[$i]['id']] : $submit_language_special_description), '75', '2', 'id="specials_description_'.$i.'"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_keywords'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_language_special_keywords_'.$languages[$i]['id'],(isset($submit_language_special_keywords[$languages[$i]['id']]) ? $submit_language_special_keywords[$languages[$i]['id']] : $submit_language_special_keywords), '150', '2'); ?>
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
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_SEO->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>

        </div>

<!-- ############################################################# //-->
<!--          ONGLET Information  Commentaires                    //-->
<!-- ############################################################# //-->

        <div class="tab-pane" id="tab5">
          <div class="col-md-12 mainTitle">
            <div class="float-md-left"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_reviews'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-md-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
                <span class="col-md-3"><a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank" rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('analysis_google_tool'); ?></a></span>
              </div>
            </div>

<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang1" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_reviews_title'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_reviews_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_language_reviews_title_'.$languages[$i]['id'], $submit_language_reviews_title[$languages[$i]['id']], 'maxlength="50" size="77" id="reviews_title_'.$i.'"', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_description'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_description'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::textAreaField('submit_language_reviews_description_'.$languages[$i]['id'], (isset($submit_language_reviews_description[$languages[$i]['id']]) ? $submit_language_reviews_description[$languages[$i]['id']] : $submit_language_reviews_description), '75', '2', 'id="reviews_description_'.$i.'"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_keywords'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_language_reviews_keywords_'.$languages[$i]['id'], (isset($submit_language_reviews_keywords[$languages[$i]['id']]) ? $submit_language_reviews_keywords[$languages[$i]['id']] : $submit_language_reviews_keywords), '150', '5'); ?>
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
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_SEO->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
<?php
  //***********************************
  // extension
  //***********************************
  echo $CLICSHOPPING_Hooks->output('Seo', 'PageTab', null, 'display');
?>
      </div>
    </div>
  </div>
  </form>
</div>