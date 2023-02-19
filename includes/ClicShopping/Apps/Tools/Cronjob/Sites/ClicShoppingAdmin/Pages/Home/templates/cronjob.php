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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ClicShopping;
  use ClicShopping\OM\HTTP;

  $CLICSHOPPING_Cronjob = Registry::get('Cronjob');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/cron.jpeg', $CLICSHOPPING_Cronjob->getDef($CLICSHOPPING_Cronjob->getDef('heading_title')), '40', '40'); ?></span>
          <span
            class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Cronjob->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-end"></span>
          <span class="col-md-1 text-end">
            <?php echo HTML::button($CLICSHOPPING_Cronjob->getDef('button_insert'), null, $CLICSHOPPING_Cronjob->link('Edit'), 'success'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="card">
    <div class="card-header">
      <?php echo $CLICSHOPPING_Cronjob->getDef('text_cronjob_instruction'); ?>
    </div>
    <div class="card-body">
      <div class="card-text"><?php echo $CLICSHOPPING_Cronjob->getDef('text_info_cronjob'); ?></div>
      <div class="separator"></div>
       <div class="row">
         <div class="input-group">
           <div class="input-group-text">Cron URL</div>
           <input id="html-code" class="form-control" value="wget <?php echo HTTP::getShopUrlDomain() . 'index.php?cronjob&runall'; ?> --read-timeout=5400">
           <button class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="click" title="Copied!" onclick="copyHtmlCode()">
             <i class="bi bi-clipboard" title="Copy HTML Code"></i>
           </button>
         </div>
       </div>
    </div>
  </div>
  <div class="separator"></div>
  <table
    id="table"
    data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
    data-sort-name="date_added"
    data-sort-order="asc"
    data-toolbar="#toolbar"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true">

    <thead class="dataTableHeadingRow">
      <tr>
        <th data-field="code" data-sortable="true"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_code'); ?></th>
        <th data-field="cycle" data-sortable="true"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_cycle'); ?></th>
        <th data-field="identifier" data-sortable="true"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_action'); ?></th>
        <th data-field="status" data-sortable="true"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_status'); ?></th>
        <th data-field="date_added" data-sortable="true" class="text-center"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_date_added'); ?></th>
        <th data-field="date_modified" data-sortable="true" class="text-center"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_cron_date_modified'); ?></th>
        <th data-field="action" data-sortable="true" class="text-center"><?php echo $CLICSHOPPING_Cronjob->getDef('table_heading_action'); ?></th>
      </tr>
    </thead>
    <tbody>
    <?php
      $Qcron = $CLICSHOPPING_Cronjob->db->prepare('select SQL_CALC_FOUND_ROWS cron_id,
                                                                              code,
                                                                              cycle,
                                                                              action,
                                                                              status,
                                                                              date_added,
                                                                              date_modified
                                                   from :table_cron
                                                   order by date_modified desc
                                                   limit :page_set_offset, :page_set_max_results
                                                  ');

      $Qcron->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qcron->execute();

      $listingTotalRow = $Qcron->getPageSetTotalRows();

      while ($Qcron->fetch()) {
    ?>
        <tr>
          <td><?php echo $Qcron->value('code'); ?></td>
          <td><?php echo $Qcron->value('cycle'); ?></td>
          <td><?php echo $Qcron->value('action'); ?></td>
          <td class="text-center">
            <?php
              if ($Qcron->valueInt('status') == 1) {
                echo '<a href="' . $CLICSHOPPING_Cronjob->link('Cronjob&SetFlag&flag=1&id=' . $Qcron->valueInt('cron_id')) . '"><i class="bi-check text-success"></i></a>';
              } else {
                echo '<a href="' . $CLICSHOPPING_Cronjob->link('Cronjob&SetFlag&flag=0&id=' . $Qcron->valueInt('cron_id')) . '"><i class="bi bi-x text-danger"></i></a>';
              }
            ?>
          </td>
          <td><?php echo $Qcron->value('date_added'); ?></td>
          <td><?php echo $Qcron->value('date_modified'); ?></td>
          <td class="text-end">
            <div class="btn-group" role="group" aria-label="buttonGroup">
              <?php
                if( $Qcron->valueInt('cron_id') > 4) {
                  echo '<a href="' . $CLICSHOPPING_Cronjob->link('Edit&Update&cronId=' . $Qcron->valueInt('cron_id')) . '"><h4><i class="bi bi-pencil" title="' . $CLICSHOPPING_Cronjob->getDef('icon_edit') . '"></i></h4></a>';
                  echo '&nbsp;';
                } else {
                  echo '&nbsp;';
                }
                echo '<a href="' . $CLICSHOPPING_Cronjob->link('Cronjob&Run&cronId=' . $Qcron->valueInt('cron_id')) . '"><h4><i class="bi bi-gear" title="' . $CLICSHOPPING_Cronjob->getDef('icon_run') . '"></i></h4></a>';
                echo '&nbsp;';

                if( $Qcron->valueInt('cron_id') > 4) {
                  echo '<a href="' . $CLICSHOPPING_Cronjob->link('Cronjob&Delete&cronId=' . $Qcron->value('cron_id')) . '"><h4><i class="bi bi-trash2" title="' . $CLICSHOPPING_Cronjob->getDef('icon_delete') . '"></i></h4></a>';
                  echo '&nbsp;';
                } else {
                  echo '&nbsp;';
                }
              ?>
            </div>
          </td>
        </tr>
     <?php
      }
    ?>
    </tbody>
  </table>
</div>
