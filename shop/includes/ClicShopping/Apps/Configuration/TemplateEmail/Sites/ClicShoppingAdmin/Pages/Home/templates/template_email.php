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

  $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $QtemplateEmail = $CLICSHOPPING_TemplateEmail->db->prepare('select ted.language_id,
                                                              ted.template_email_name,
                                                              ted.template_email_short_description,
                                                              te.template_email_variable,
                                                              te.customers_group_id,
                                                              te.template_email_type,
                                                              te.template_email_id
                                                       from  :table_template_email te,
                                                             :table_template_email_description ted
                                                       where  ted.language_id = :language_id
                                                       and te.template_email_id = ted.template_email_id
                                                      ');

  $QtemplateEmail->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
  $QtemplateEmail->execute();
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/mail.gif', $CLICSHOPPING_TemplateEmail->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TemplateEmail->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th><?php echo $CLICSHOPPING_TemplateEmail->getDef('table_heading_template_email_name'); ?></th>
          <th><?php echo $CLICSHOPPING_TemplateEmail->getDef('table_heading_template_email_type'); ?></th>
          <th><?php echo $CLICSHOPPING_TemplateEmail->getDef('table_heading_template_email_description'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_TemplateEmail->getDef('table_heading_template_customer_groups'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_TemplateEmail->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <!-- partie lecture du tableau -->
<?php
  while ($QtemplateEmail->fetch() ) {

    if ($QtemplateEmail->valueInt('template_email_type') == 1) {
      $template_email_type =  $CLICSHOPPING_TemplateEmail->getDef('text_template_email_catalog');

    } elseif ($QtemplateEmail->valueInt('template_email_type') == 0) {
      $template_email_type = $CLICSHOPPING_TemplateEmail->getDef('text_template_email_admin');
    } else {
      $template_email_type = $CLICSHOPPING_TemplateEmail->getDef('text_template_email_admin_catalog');
    }

    if ( $QtemplateEmail->value('customers_group_id') == 0) {
      $template_email_customer_group = $CLICSHOPPING_TemplateEmail->getDef('text_template_email_b2c');
    } elseif ($QtemplateEmail->valueInt('template_email_type') == 1) {
      $template_email_customer_group = $CLICSHOPPING_TemplateEmail->getDef('text_template_email_b2c_b2b');
    }
?>
            <tr>
              <th scope="row"><?php echo $QtemplateEmail->value('template_email_name'); ?></th>
              <td><?php echo $template_email_type; ?></td>
              <td><?php echo $template['template_email_short_description']; ?></td>
              <td class="text-md-center"><?php echo $template_email_customer_group; ?></td>
              <td class="text-md-right">
<?php
  echo HTML::link($CLICSHOPPING_TemplateEmail->link('Edit&page=' . $_GET['page'] . '&tID=' . $QtemplateEmail->valueInt('template_email_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_TemplateEmail->getDef('image_edit')));
?>
              </td>
            </tr>
<?php
  }
?>
        </tbody>
      </table>
    </td>
  </table>
</div>