<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Marketing\SEO\Classes\ClicShoppingAdmin\SeoAdmin;

  $CLICSHOPPING_SEO = Registry::get('SEO');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('SEO')) {
    echo $CLICSHOPPING_MessageStack->get('SEO');
  }

  $Qseo = $CLICSHOPPING_SEO->db->prepare('select  p.submit_id,
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
                                                  p.submit_language_reviews_description,
                                                  p.submit_language_favorites_title,
                                                  p.submit_language_favorites_keywords,
                                                  p.submit_language_favorites_description,
                                                  p.submit_language_featured_title,
                                                  p.submit_language_featured_keywords,
                                                  p.submit_language_featured_description,
                                                  p.submit_defaut_language_title_h1       
                                         from :table_submit_description p
                                         where p.submit_id = 1
                                        ');
  $Qseo->execute();

  $seoBject = $Qseo->toArray();

  if (\is_array($seoBject)){
    $seo = new ObjectInfo($Qseo->toArray());

    $languages = $CLICSHOPPING_Language->getLanguages();
  
    echo HTML::form('seo', $CLICSHOPPING_SEO->link('SEO&Update'));
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/referencement.gif', $CLICSHOPPING_SEO->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_SEO->getDef('heading_title'); ?></span>
          <span
            class="col-md-6 text-end"><?php echo HTML::button($CLICSHOPPING_SEO->getDef('button_update'), null, null, 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- ############################################################# //-->
  <!--          ONGLET Information General de la Page Accueil          //-->
  <!-- ############################################################# //-->
  <div id="pagesSubmitTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-bs-toggle="tab" class="nav-link active">' . $CLICSHOPPING_SEO->getDef('tab_submit_default') . '</a>'; ?></li>
      <li
        class="nav-item"><?php echo '<a href="#tab2" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_products_info'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab3" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_products_new'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab4" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_specials'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab5" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_reviews'); ?></a></li>
      <li
         class="nav-item"><?php echo '<a href="#tab6" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_favorites'); ?></a></li>
      <li
         class="nav-item"><?php echo '<a href="#tab7" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_featured'); ?></a></li>
      <li
         class="nav-item"><?php echo '<a href="#tab8" role="tab" data-bs-toggle="tab" class="nav-link">' . $CLICSHOPPING_SEO->getDef('tab_submit_sitemap'); ?></a></li>
    </ul>


    <div class="tabsClicShopping">
      <div class="tab-content">
        <!-- ############################################################# //-->
        <!--          ONGLET Information General                          //-->
        <!-- ############################################################# //-->
        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_default'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>
            <?php
              for ($i = 0, $n = \count($languages); $i < $n; $i++) {
            ?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_defaut_language_title_h1'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_defaut_language_title_h1'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_defaut_language_title_h1_[' . $languages[$i]['id'] . ']', ($submit_defaut_language_title_h1[$languages[$i]['id']] ?? SeoAdmin::getSeoDefaultLanguageTitleH1($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="default_title_h1_' .$i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_defaut_language_title_h1') . '"', true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_title'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_defaut_language_title_[' . $languages[$i]['id'] . ']', ($submit_defaut_language_title[$languages[$i]['id']] ?? SeoAdmin::getSeoDefaultLanguageTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="default_title_' .$i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_default_language_title') . '"', true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_description'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_description'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_defaut_language_description_[' . $languages[$i]['id'] . ']', ($submit_defaut_language_description[$languages[$i]['id']] ?? SeoAdmin::getSeoDefaultLanguageDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="default_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_default_language_description') . '"') . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_keywords'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_defaut_language_keywords_[' . $languages[$i]['id'] . ']', ($submit_defaut_language_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoDefaultLanguageKeywords($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="submit_defaut_language_keywords' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_default_language_keywords') . '"', true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_footer'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_default_language_footer'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_defaut_language_footer_[' . $languages[$i]['id'] . ']', ($submit_defaut_language_footer[$languages[$i]['id']] ?? SeoAdmin::getSeoDefaultLanguageFooter($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="submit_defaut_language_footer' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_default_language_footer') . '"', true) . '&nbsp;'; ?>
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
            <div><?php echo '<h4><i class="bi bi-question-circle" title="' .$CLICSHOPPING_SEO->getDef('title_help_submit') . '"></i></h4> ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>
        <!-- ############################################################# //-->
        <!--     ONGLET Information General de page information produit   //-->
        <!-- ############################################################# //-->
        <div class="tab-pane" id="tab2">
          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_products_info'); ?></div>
          </div>

          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>
            <?php
              for ($i = 0, $n = \count($languages); $i < $n; $i++) {
              ?>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="lang1"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_title'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_title'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::inputField('submit_language_products_info_title_[' . $languages[$i]['id'] . ']', ($submit_language_products_info_title[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsInfoTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="products_info_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_title') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_description'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_description'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::textAreaField('submit_language_products_info_description_[' . $languages[$i]['id'] . ']', ($submit_language_products_info_description[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsInfoDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="products_info_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_description') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_keywords'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_keywords'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::textAreaField('submit_language_products_info_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_products_info_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsInfoKeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="submit_language_products_info_keywords' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_info_keywords') . '"') . '&nbsp;'; ?>
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
            <div><?php echo '<h4><i class="bi bi-question-circle" title="' .$CLICSHOPPING_SEO->getDef('title_help_submit') . '"></i></h4> ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>

        <!-- ############################################################# //-->
        <!--          ONGLET Information General de la page nouveautes          //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab3">

          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_products_new'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>

            <?php
            for ($i = 0, $n = \count($languages); $i < $n; $i++) {
              ?>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="lang1"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_title'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_title'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::inputField('submit_language_products_new_title_[' . $languages[$i]['id'] . ']', ($submit_language_products_new_title[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsNewTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="products_new_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_title') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_description'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_description'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::textAreaField('submit_language_products_new_description_[' . $languages[$i]['id'] . ']', ($submit_language_products_new_description[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsNewDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="products_new_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_description') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_keywords'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_keywords'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::textAreaField('submit_language_products_new_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_products_new_description[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsNewKeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="submit_language_products_new_keywords' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_new_keywords') . '"') . '&nbsp;'; ?>
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
            <div><?php echo '<h4><i class="bi bi-question-circle" title="' .$CLICSHOPPING_SEO->getDef('title_help_submit') . '"></i></h4> ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>

        <!-- ############################################################# //-->
        <!--          ONGLET Information General de la page promotion          //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab4">

          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_specials'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>

            <?php
              for ($i = 0, $n = \count($languages); $i < $n; $i++) {
                ?>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="lang1"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_title'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_title'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::inputField('submit_language_special_title_[' . $languages[$i]['id'] . ']', ($submit_language_special_title[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsSpecialsTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="specials_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_specials_title') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_description'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_description'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::textAreaField('submit_language_special_description_[' . $languages[$i]['id'] . ']', ($submit_language_special_description[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsSpecialsDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="specials_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_specials_description') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_keywords'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_specials_keywords'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::textAreaField('submit_language_special_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_special_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsSpecialskeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="submit_language_products_info_description' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_specials_keywords') . '"') . '&nbsp;'; ?>
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
            <div><?php echo '<h4><i class="bi bi-question-circle" title="' .$CLICSHOPPING_SEO->getDef('title_help_submit') . '"></i></h4> ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>

        <!-- ############################################################# //-->
        <!--          ONGLET Information  Commentaires                    //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab5">
          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_reviews'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>

            <?php
              for ($i = 0, $n = \count($languages); $i < $n; $i++) {
                ?>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="lang1"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_title'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_title'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::inputField('submit_language_reviews_title_[' . $languages[$i]['id'] . ']', ($submit_language_reviews_title[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsSpecialsTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="reviews_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_title') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_description'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_description'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::textAreaField('submit_language_reviews_description_[' . $languages[$i]['id'] . ']', ($submit_language_reviews_description[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsReviewsDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="reviews_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_description') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_keywords'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_keywords'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::textAreaField('submit_language_reviews_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_reviews_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoProductsReviewsKeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="text_submit_language_reviews_description' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_reviews_keywords') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
          </div>
        </div>

        <!-- ############################################################# //-->
        <!--          ONGLET Information  Favorites                    //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab6">
          <div class="col-md-12 mainTitle">
            <div
               class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_favorites'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                <span class="col-md-3"></span>
                <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                          rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>

            <?php
            for ($i = 0, $n = \count($languages); $i < $n; $i++) {
              ?>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="lang1"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label
                          for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_favorites_title'); ?>"
                          class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_favorites_title'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::inputField('submit_language_favorites_title_[' . $languages[$i]['id'] . ']', ($submit_language_favorites_title[$languages[$i]['id']] ?? SeoAdmin::getSeoFavoritesTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="favorites_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_favorites_title') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_description'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_description'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::textAreaField('submit_language_favorites_description_[' . $languages[$i]['id'] . ']', ($submit_language_favorites_description[$languages[$i]['id']] ?? SeoAdmin::getSeoFavoritesDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="favorites_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_description') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_keywords'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_keywords'); ?></label>
                      <div class="col-md-7">
                        <?php echo HTML::textAreaField('submit_language_favorites_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_favorites_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoFavoritesKeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="text_submit_language_reviews_description' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_favorites_keywords') . '"') . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php
            }
            ?>
         </div>
        </div>


        <!-- ############################################################# //-->
        <!--          ONGLET Information  featured                    //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab7">
          <div class="col-md-12 mainTitle">
            <div
              class="float-start"><?php echo $CLICSHOPPING_SEO->getDef('text_pages_submit_information_featured'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12 text-center">
                  <span class="col-md-3"></span>
                  <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                            rel="noreferrer"><?php echo $CLICSHOPPING_SEO->getDef('keywords_google_trend'); ?></a></span>
              </div>
            </div>

            <?php
            for ($i = 0, $n = \count($languages); $i < $n; $i++) {
              ?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="lang1"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label
                        for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_featured_title'); ?>"
                        class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_products_featured_title'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::inputField('submit_language_featured_title_[' . $languages[$i]['id'] . ']', ($submit_language_featured_title[$languages[$i]['id']] ?? SeoAdmin::getSeoFeaturedTitle($seo->submit_id, $languages[$i]['id'])), 'class="form-control" id="featured_title_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_products_featured_title') . '"', true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_featured_description'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_featured_description'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::textAreaField('submit_language_featured_description_[' . $languages[$i]['id'] . ']', ($submit_language_featured_description[$languages[$i]['id']] ?? SeoAdmin::getSeoFeaturedDescription($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="featured_description_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_featured_description') . '"') . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_featured_keywords'); ?>"
                       class="col-5 col-form-label"><?php echo $CLICSHOPPING_SEO->getDef('text_submit_language_featured_keywords'); ?></label>
                  <div class="col-md-7">
                    <?php echo HTML::textAreaField('submit_language_featured_keywords_[' . $languages[$i]['id'] . ']', ($submit_language_featured_keywords[$languages[$i]['id']] ?? SeoAdmin::getSeoFeaturedkeywords($seo->submit_id, $languages[$i]['id'])), '75', '2', 'class="form-control" id="featured_keywords_' . $i . '" placeholder="' . $CLICSHOPPING_SEO->getDef('text_submit_language_featured_keywords') . '"') . '&nbsp;'; ?>
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
            <div><?php echo '<h4><i class="bi bi-question-circle" title="' .$CLICSHOPPING_SEO->getDef('title_help_submit') . '"></i></h4> ' . $CLICSHOPPING_SEO->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_SEO->getDef('help_submit'); ?></div>
          </div>
        </div>
      </div>

        <!-- ############################################################# //-->
        <!--          ONGLET Information  featured                    //-->
        <!-- ############################################################# //-->

        <div class="tab-pane" id="tab8">
            <div class="adminformTitle">
                <div class="row">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <td>
                            <table class="table table-sm table-hover table-striped">
                                <thead>
                                <tr class="dataTableHeadingRow">
                                    <td>Link</td>
                                    <td>Description</td>
                                    <td>google</td>
                                    <td>bing</td>
                                </tr>
                                <td></td>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapProducts', 'Products Sitemap', 'target="_blank"'); ?></td>
                                    <td>Products Sitemap</td>
                                    <td><?php echo HTML::link('https://search.google.com/search-console/about', 'Google Search Console', 'target="_blank"'); ?></td>
                                    <td><?php echo HTML::link('https://www.bing.com/webmasters/', 'Bing Console', 'target="_blank"'); ?></td>
                                </tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapIndex', 'Index Sitemap', 'target="_blank"'); ?></td>
                                    <td>Index Sitemap</td>
                                    <td></td>
                                    <td></td>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapCategories', 'Categories Sitemap', 'target="_blank"'); ?></td>
                                    <td>Categories Sitemap</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapFavorites', 'Favorites Sitemap', 'target="_blank"'); ?></td>
                                    <td>Favorites Sitemap</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapFeatured', 'Featured Sitemap', 'target="_blank"'); ?></td>
                                    <td>Featured Sitemap</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapManufacturers', 'Manufacturers Sitemap', 'target="_blank"'); ?></td>
                                    <td>Manufacturers Sitemap</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Sitemap&GoogleSitemapSpecials', 'Specials Sitemap', 'target="_blank"'); ?></td>
                                    <td>Specials Sitemap</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link"></i> <?php echo HTML::link(HTTP::getShopUrlDomain() . 'index.php?Info&RSS', 'Rss', 'target="_blank"'); ?></td>
                                    <td>RSS</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </table>
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
<?php
  }