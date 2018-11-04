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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Members = Registry::get('Members');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

// Permettre l'utilisation de l'approbation des comptes en mode B2B
  if (defined('B2C') && B2C == 'true') {
    CLICSHOPPING::redirect('index.php');
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/client_attente.gif', $CLICSHOPPING_Members->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Members->getDef('heading_title'); ?></span>
<?php
  if (MEMBER == 'true') {
?>
           <span class="col-md-2">
            <div class="form-group">
              <div class="controls">
<?php
    echo HTML::form('search', $CLICSHOPPING_Members->link('Members'), 'post', 'class="form-inline"', ['session_id' => true]);
    echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Members->getDef('heading_title_search').'"');
?>
                </form>
              </div>
            </div>
          </span>
<?php
  if (!is_null($_POST['search'])) {
?>
    <span class="col-md-3 text-md-right"><?php echo HTML::button($CLICSHOPPING_Members->getDef('button_reset'), null, CLICSHOPPING::link('members.php', null), 'warning'); ?></span>
<?php
  }
?>

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
            <th><?php echo $CLICSHOPPING_Members->getDef('table_heading_lastname'); ?></th>
            <th><?php echo $CLICSHOPPING_Members->getDef('table_heading_firstname'); ?></th>
            <th><?php echo $CLICSHOPPING_Members->getDef('table_heading_company'); ?></th>
            <th class="text-md-right"><?php echo $CLICSHOPPING_Members->getDef('table_heading_account_created'); ?></th>
            <th class="text-md-right"><?php echo $CLICSHOPPING_Members->getDef('table_heading_action'); ?>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
<?php
  $search = '';
  if ( ($_POST['search']) && (!is_null($_POST['search'])) ) {
    $keywords = HTML::sanitize($_POST['search']);

    $Qcustomers = $CLICSHOPPING_Members->db->prepare('select  SQL_CALC_FOUND_ROWS  c.customers_id,
                                                                             c.customers_lastname,
                                                                             c.customers_firstname,
                                                                             a.entry_company,
                                                                             c.customers_email_address,
                                                                             a.entry_country_id,
                                                                             c.customers_group_id
                                              from :table_customers c left join :table_address_book a on c.customers_id = a.customers_id
                                              where (c.customers_lastname like :keywords
                                                      or c.customers_firstname like :keywords
                                                      or c.customers_email_address like :keywords
                                                    )
                                              and member_level = 0
                                              and c.customers_default_address_id = a.address_book_id
                                              order by c.customers_lastname,
                                                       c.customers_firstname
                                              limit :page_set_offset,
                                                    :page_set_max_results
                                              ');

    $Qcustomers->bindvalue(':keywords', '%' . $keywords . '%');
    $Qcustomers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qcustomers->execute();


  } else {

    $Qcustomers = $CLICSHOPPING_Members->db->prepare('select  SQL_CALC_FOUND_ROWS  c.customers_id,
                                                                             c.customers_lastname,
                                                                             c.customers_firstname,
                                                                             a.entry_company,
                                                                             c.customers_email_address,
                                                                             a.entry_country_id,
                                                                             c.customers_group_id
                                              from :table_customers c left join :table_address_book a on c.customers_id = a.customers_id
                                              where member_level = 0
                                              and c.customers_default_address_id = a.address_book_id
                                              order by c.customers_lastname,
                                                       c.customers_firstname
                                              limit :page_set_offset,
                                                    :page_set_max_results
                                              ');

    $Qcustomers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qcustomers->execute();
  }

  $listingTotalRow = $Qcustomers->getPageSetTotalRows();


  if ($listingTotalRow > 0) {

  while ($Qcustomers->fetch()) {

    $Qinfo = $CLICSHOPPING_Members->db->prepare('select customers_info_date_account_created as date_account_created,
                                                 customers_info_date_account_last_modified as date_account_last_modified,
                                                 customers_info_date_of_last_logon as date_last_logon,
                                                 customers_info_number_of_logons as number_of_logons
                                           from :table_customers_info
                                           where customers_info_id = :customers_info_id
                                          ');
    $Qinfo->bindInt(':customers_info_id', (int)$Qcustomers->valueInt('customers_id'));
    $Qinfo->execute();

    if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qcustomers->valueInt('customers_id')))) && !isset($lInfo) && (substr($action, 0, 3) != 'new')) {

      $lInfo = new ObjectInfo($Qinfo->toArray());

      $Qcountry = $CLICSHOPPING_Members->db->prepare('select countries_name
                                               from :table_countries
                                               where countries_id = :countries_id
                                              ');
      $Qcountry->bindInt(':countries_id', $Qcustomers->valueInt('entry_country_id'));
      $Qcountry->execute();

      $Qreviews = $CLICSHOPPING_Members->db->prepare('select count(*) as number_of_reviews
                                               from :table_reviews
                                               where customers_id = :customers_id
                                              ');
      $Qreviews->bindInt(':customers_id', $Qcustomers->valueInt('customers_id'));
      $Qreviews->execute();

      $customer_info = array_merge($Qcountry->toArray(), $Qinfo->toArray(), $Qreviews->toArray());

      $cInfo_array = array_merge($Qcustomers->toArray(), (array)$customer_info);
      $cInfo = new ObjectInfo($cInfo_array);
    }
?>
        <th scope="row"><?php echo $Qcustomers->value('customers_lastname'); ?></th>
        <td><?php echo $Qcustomers->value('customers_firstname'); ?></td>
        <td><?php echo $Qcustomers->value('entry_company'); ?></td>
        <td class="text-md-right"><?php echo DateTime::toShort($Qinfo->value('date_account_created')); ?></td>
        <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_Members->link('AcceptMembers&cID=' . $Qcustomers->valueInt('customers_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/activate.gif', $CLICSHOPPING_Members->getDef('icon_activate')));
      echo '&nbsp;';
      echo HTML::link(CLICSHOPPING::link('index.php','A&Customers&Edit&cID=' . $Qcustomers->valueInt('customers_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Members->getDef('icon_edit_customer')));
      echo '&nbsp;';
      echo HTML::link(CLICSHOPPING::link('index.php','A&Communication\EMail&EMail&customer=' . $Qcustomers->value('customers_email_address')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/email.gif', $CLICSHOPPING_Members->getDef('icon_email')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Members->link('Delete&cID=' . $Qcustomers->valueInt('customers_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Members->getDef('button_delete')));
      echo '&nbsp;';
?>
        </td>
      </tr></form>
<?php
   }
?>
    </tbody>
<?php
  } // end $listingTotalRow
?>
  </table></td>
</table>
<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcustomers->getPageSetLabel($CLICSHOPPING_Members->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qcustomers->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
    }
  } else {

?>
  <div class="alert alert-warning">
    <?php echo $CLICSHOPPING_Members->getDef('member_desactivated'); ?>
  </div>
<?php
  }
?>
<!-- body_eof //-->
</div>