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

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;
  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Language = Registry::get('Language');

  $QtemplateEmailDescription = $CLICSHOPPING_TemplateEmail->db->prepare('select ted.language_id,
                                                                        ted.template_email_name,
                                                                        ted.template_email_short_description,
                                                                        ted.template_email_description,
                                                                        te.template_email_variable,
                                                                        te.template_email_id,
                                                                        te.customers_group_id
                                                                 from :table_template_email te,
                                                                      :table_template_email_description ted
                                                                 where te.template_email_id = :template_email_id
                                                                 and te.template_email_id = ted.template_email_id
                                                                ');
  $QtemplateEmailDescription->bindInt(':template_email_id',(int)$_GET['tID']);
  $QtemplateEmailDescription->execute();

  $template_email_description = $QtemplateEmailDescription->fetch();

  $tInfo = new ObjectInfo($QtemplateEmailDescription->toArray());
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/mail.gif', $CLICSHOPPING_TemplateEmail->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TemplateEmail->getDef('heading_title'); ?></span>
          <span class="col-md-9 text-md-right">
<?php
  echo HTML::form('template_emails',  $CLICSHOPPING_TemplateEmail->link('TemplateEmail&Update&ID=' . $_GET['tID']));
  echo HTML::hiddenField('template_email', $_GET['tID']);
  echo HTML::button($CLICSHOPPING_TemplateEmail->getDef('button_cancel'), null,  $CLICSHOPPING_TemplateEmail->link('TemplateEmail&page=' . $_GET['page']. '&tID=' . $_GET['tID']), 'warning') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_TemplateEmail->getDef('button_update'), null, null, 'success');

  echo HTMLOverrideAdmin::getCkeditor();
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div>
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist"  id="myTab">
      <li class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_TemplateEmail->getDef('tab_general') . '</a>'; ?></li>
      <li class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_TemplateEmail->getDef('tab_description'); ?></a></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
<!-- ############################################################ //-->
<!--          ONGLET Information General                          //-->
<!-- ############################################################ //-->
        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle"><?php echo $CLICSHOPPING_TemplateEmail->getDef('title_information_name'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="name" class="col-12 col-form-label"><?php echo $CLICSHOPPING_TemplateEmail->getDef('template_email_text_name'); ?>
                </div>
              </div>
            </div>
<?php
  $languages = $CLICSHOPPING_Language->getLanguages();
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-8">
                <div class="form-group row">
                  <label for="code" class="col-3 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  <div class="col-md-7">
                    <?php echo  HTML::inputField('template_email_name[' . $languages[$i]['id'] . ']', (isset($template_email_name[$languages[$i]['id']]) ? $template_email_name[$languages[$i]['id']] : TemplateEmailAdmin::getTemplateEmailName($tInfo->template_email_id, $languages[$i]['id'])), 'maxlength="250", size="50"',  true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>
<?php
  }
?>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="description" class="col-12 col-form-label"><?php echo $CLICSHOPPING_TemplateEmail->getDef('template_email_text_short_description'); ?></label>
                </div>
              </div>
            </div>
<?php
  $languages = $CLICSHOPPING_Language->getLanguages();
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-8">
                <div class="form-group row">
                  <label for="code" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  <div class="col-md-5">
                    <?php echo  HTML::inputField('template_email_short_description[' . $languages[$i]['id'] . ']', (isset($template_email_short_description[$languages[$i]['id']]) ? $template_email_short_description[$languages[$i]['id']] : TemplateEmailAdmin::getTemplateEmailShortDescription($tInfo->template_email_id, $languages[$i]['id'])), 'maxlength="250", size="50"',  true) . '&nbsp;'; ?>
                  </div>
                </div>
              </div>
            </div>
<?php
  }
?>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_TemplateEmail->getDef('help_title_onglet_general')) . ' ' . $CLICSHOPPING_TemplateEmail->getDef('help_title_onglet_general') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_TemplateEmail->getDef('text_help_template'); ?></div>
          </div>
        </div>

<!-- ############################################################ //-->
<!--          ONGLET Information Description                      //-->
<!-- ############################################################ //-->
        <div class="tab-pane" id="tab2">
          <div class="col-md-12 mainTitle"><?php echo $CLICSHOPPING_TemplateEmail->getDef('title_message'); ?></div>
          <div class="adminformTitle">
<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="code" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  <div class="col-md-3">
                    <?php echo HTMLOverrideAdmin::textAreaCkeditor('template_email_description[' . $languages[$i]['id'] . ']', 'soft', '750', '300', (isset($template_email_description[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($template_email_description[$languages[$i]['id']])) : TemplateEmailAdmin::getTemplateEmailDescription($tInfo->template_email_id, $languages[$i]['id'])));?>
                  </div>
                </div>
              </div>
            </div>
<?php
  }
?>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_TemplateEmail->getDef('help_title_onglet_general')) . ' ' . $CLICSHOPPING_TemplateEmail->getDef('help_title_onglet_general') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_TemplateEmail->getDef('text_help_template'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>