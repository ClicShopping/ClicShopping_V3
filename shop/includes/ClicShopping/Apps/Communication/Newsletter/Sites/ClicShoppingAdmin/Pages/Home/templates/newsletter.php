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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;
  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Newsletter\Newsletter as NewsletterModule;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
?>

  <div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/newsletters.gif', $CLICSHOPPING_Newsletter->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Newsletter->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Newsletter->getDef('button_insert'), null, $CLICSHOPPING_Newsletter->link('Insert'), 'success') .'&nbsp;';
  echo HTML::form('delete_all', $CLICSHOPPING_Newsletter->link('Newsletter&DeleteAll&page=' . $page));
?>
            <a onclick="$('delete').prop('action', ''); $('form').submit();" class="button"><span><?php echo HTML::button($CLICSHOPPING_Newsletter->getDef('button_delete'), null, null, 'danger'); ?></span></a>
           </span>
        </div>
      </div>
    </div>
  </div>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card-deck">
          <?php echo $CLICSHOPPING_Hooks->output('Stats', 'StatsCustomersNewsletterBySex'); ?>
        </div>
      </div>
    </div>
    <div class="separator"></div>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <td>
        <table class="table table-sm table-hover table-striped">
          <thead>
            <tr class="dataTableHeadingRow">
              <th width="1" class="text-md-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
              <th><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_newsletters'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_size'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_module'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_language'); ?></th>
<?php
// Permettre l'affichage des groupes en mode B2B
    if (MODE_B2B_B2C == 'true') {
?>
      <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_b2b'); ?></th>
<?PHP
    }
?>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_sent'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_status'); ?></th>
              <th class="text-md-right"><?php echo $CLICSHOPPING_Newsletter->getDef('table_heading_action'); ?>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
<?php
    $Qnewsletters = $CLICSHOPPING_Newsletter->db->prepare('select  SQL_CALC_FOUND_ROWS newsletters_id,
                                                                                 title,
                                                                                 length(content) as content_length,
                                                                                 module,
                                                                                 date_added,
                                                                                 date_sent,
                                                                                 status,
                                                                                 languages_id,
                                                                                 customers_group_id,
                                                                                 locked,
                                                                                 newsletters_accept_file,
                                                                                 newsletters_twitter,
                                                                                 newsletters_customer_no_account
                                                    from :table_newsletters
                                                    order by date_added desc
                                                    limit :page_set_offset, :page_set_max_results
                                                    ');

    $Qnewsletters->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qnewsletters->execute();

    $listingTotalRow = $Qnewsletters->getPageSetTotalRows();

    if ($listingTotalRow > 0) {
      while ($Qnewsletters->fetch()) {
// Permettre l'affichage des groupes en mode B2B
        if (MODE_B2B_B2C == 'true') {

          $QcustomersGroup = $CLICSHOPPING_Newsletter->db->prepare('select customers_group_name
                                                              from :table_customers_groups
                                                              where customers_group_id = :customers_group_id
                                                            ');
          $QcustomersGroup->bindInt(':customers_group_id', $Qnewsletters->valueInt('customers_group_id'));
          $QcustomersGroup->execute();

          $customers_group = $QcustomersGroup->fetch();

          if ($customers_group['customers_group_name'] == '') {
            $customers_group['customers_group_name'] =  $CLICSHOPPING_Newsletter->getDef('text_all_customers');
          }
        }

        if ($Qnewsletters->valueInt('languages_id') != 0) {

          $QnewslettersLanguages = $CLICSHOPPING_Newsletter->db->prepare('select name
                                                                   from :table_languages
                                                                   where languages_id = :language_id
                                                                  ');
          $QnewslettersLanguages->bindInt(':language_id', $Qnewsletters->valueInt('languages_id'));
          $QnewslettersLanguages->execute();

          $newsletters_language = $QnewslettersLanguages->fetch();

        } else {
          $newsletters_language['name'] =  $CLICSHOPPING_Newsletter->getDef('text_all_languages');
        }

        if ((!isset($_GET['nID']) || (isset($_GET['nID']) && ((int)$_GET['nID'] ===  $Qnewsletters->valueInt('newsletters_id')))) && !isset($nInfo)) {
          $nInfo = new ObjectInfo($Qnewsletters->toArray());
        }
?>
                <td>
<?php
        if ($Qnewsletters->value('selected')) {
?>
                  <input type="checkbox" name="selected[]" value="<?php echo $Qnewsletters->valueInt('newsletters_id'); ?>" checked="checked" />
<?php
        } else {
?>
                  <input type="checkbox" name="selected[]" value="<?php echo $Qnewsletters->valueInt('newsletters_id'); ?>" />
<?php
        }
?>
                </td>
                <th scope="row"><?php echo '<a href="' . $CLICSHOPPING_Newsletter->link('Newsletter&Preview&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . $Qnewsletters->value('title') .'</a>'; ?></th>
                <td class="text-md-center"><?php echo number_format($Qnewsletters->value('content_length')) . ' bytes'; ?></td>
                <td class="text-md-center"><?php echo $Qnewsletters->value('module'); ?></td>
                <td class="text-md-center"><?php echo $newsletters_language['name']; ?></td>
<?php
// Permettre l'affichage des groupes en mode B2B
        if (MODE_B2B_B2C == 'true') {
?>
                <td class="text-md-center"><?php echo $customers_group['customers_group_name']; ?></td>

<?PHP
        }
?>
                <td class="text-md-center"><?php if ($Qnewsletters->valueInt('status') == 1) { echo '<i class="fas fa-check fa-lg" aria-hidden="true"></i>'; } else { echo '<i class="fas fa-times fa-lg" aria-hidden="true"></i>'; } ?></td>
                <td class="text-md-center"><?php if ($Qnewsletters->valueInt('locked') > 0) { echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/locked.gif', $CLICSHOPPING_Newsletter->getDef('icon_locked')); } else { echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/unlocked.gif', $CLICSHOPPING_Newsletter->getDef('icon_unlocked')); } ?></td>
                <td class="text-md-right">
<?php
        if ($Qnewsletters->valueInt('locked') > 0) { echo '<a href="' .  $CLICSHOPPING_Newsletter->link('Update&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Newsletter->getDef('icon_edit')) . '</a>&nbsp;'; }
        echo '<a href="' . $CLICSHOPPING_Newsletter->link('Preview&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_Newsletter->getDef('icon_preview')) . '</a>' ;
        echo '&nbsp;';

        if ($Qnewsletters->valueInt('locked') > 0) { echo '<a href="' . $CLICSHOPPING_Newsletter->link('Newsletter&Unlock&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/unlock.gif', $CLICSHOPPING_Newsletter->getDef('icon_unlock')) . '</a>'; } else { echo '<a href="' . $CLICSHOPPING_Newsletter->link('Newsletter&Lock&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/lock.gif', $CLICSHOPPING_Newsletter->getDef('image_lock')) . '</a>'; }
        if ($Qnewsletters->valueInt('locked') > 0) { echo '&nbsp;<a href="' . $CLICSHOPPING_Newsletter->link('Send&page=' . $page . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&nlID=' . $Qnewsletters->valueInt('languages_id') .'&cgID=' . $Qnewsletters->valueInt('customers_group_id') . '&ac='. $Qnewsletters->valueInt('newsletters_accept_file') . '&at='. $Qnewsletters->valueInt('newsletters_twitter') . '&ana=' . $Qnewsletters->valueInt('newsletters_customer_no_account')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/send.gif', $CLICSHOPPING_Newsletter->getDef('image_send')) . '</a>'; }
        echo '&nbsp;';
?>
                </td>
              </tr>
<?php
        }
      } // end $listingTotalRow
?>
          </tbody>
        </table>
      </td>
    </table>
    </form><!-- end form delete all -->
<?php
    if ($listingTotalRow > 0) {
?>
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qnewsletters->getPageSetLabel($CLICSHOPPING_Newsletter->getDef('text_display_number_of_link')); ?></div>
          <div class="float-md-right text-md-right"><?php echo $Qnewsletters->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
<?php
    } // end $listingTotalRow
?>
</div>