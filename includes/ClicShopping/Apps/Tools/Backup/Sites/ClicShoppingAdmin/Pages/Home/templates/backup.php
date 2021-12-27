<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Backup = Registry::get('Backup');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';
  // check if the backup directory exists
  $dir_ok = false;

  if (is_dir($backup_directory)) {
    if (FileSystem::isWritable($backup_directory)) {
      $dir_ok = true;
    } else {
      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Backup->getDef('error_backup_directory_not_writeable'), 'error');
    }
  } else {
    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Backup->getDef('error_backup_directory_does_not_exist'), 'error');
  }

?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/backup.gif', $CLICSHOPPING_Backup->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Backup->getDef('heading_title'); ?></span>
          <span class="col-md-6 text-end">
<?php
  echo HTML::button($CLICSHOPPING_Backup->getDef('button_backup'), null, $CLICSHOPPING_Backup->link('BackupDb'), 'info') . ' ';
  echo HTML::button($CLICSHOPPING_Backup->getDef('button_restore_file'), null, $CLICSHOPPING_Backup->link('RestoreLocal'), 'warning');
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm  table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th class="dataTableHeadingContent"><?php echo $CLICSHOPPING_Backup->getDef('table_heading_title'); ?></th>
          <th
            class="dataTableHeadingContent text-center"><?php echo $CLICSHOPPING_Backup->getDef('table_heading_file_date'); ?></th>
          <th
            class="dataTableHeadingContent text-end"><?php echo $CLICSHOPPING_Backup->getDef('table_heading_file_size'); ?></th>
          <th
            class="dataTableHeadingContent text-end"><?php echo $CLICSHOPPING_Backup->getDef('table_heading_info_compression'); ?></th>
          <th
            class="dataTableHeadingContent text-end"><?php echo $CLICSHOPPING_Backup->getDef('table_heading_action'); ?>
            &nbsp;
          </th>
        </tr>
        </thead>
        <tbody>
        <?php

          if ($dir_ok === true) {

          $dir = dir($backup_directory);
          $contents = [];

          while ($file = $dir->read()) {
            if (!is_dir($backup_directory . $file) && \in_array(substr($file, -3), array('zip', 'sql', '.gz'))) {
              $contents[] = $file;
            }
          }

          sort($contents);

          for ($i = 0, $n = \count($contents); $i < $n; $i++) {
            $entry = $contents[$i];

            if ((!isset($_GET['file']) || (isset($_GET['file']) && ($_GET['file'] == $entry)))) {
              if (is_file($backup_directory . $file)) {
                $info = [
                  'file' => $file,
                  'date' => date($CLICSHOPPING_Backup->getDef('php_date_time_format'), filemtime($backup_directory . $file)),
                  'size' => number_format(filesize($backup_directory . $entry)) . ' file',
                ];

                switch (substr(file, -3)) {
                  case 'zip':
                    $info['compression'] = 'ZIP';
                    break;
                  case '.gz':
                    $info['compression'] = 'GZIP';
                    break;
                  default:
                    $info['compression'] = $CLICSHOPPING_Backup->getDef('text_no_extension');
                    break;
                }

                $buInfo = new ObjectInfo($info);

                $compression = $buInfo->compression;
              } else {
                $compression = '';
              }
              ?>
              <th scope="row"><?php echo $entry; ?></th>
              <td class="text-center"><?php echo date("m/d/Y", filemtime($backup_directory . $entry)); ?></td>
              <td class="text-end"><?php echo number_format(filesize($backup_directory . $entry)); ?>
                bytes
              </td>
              <td class="text-center" onclick="document.location.href='<?php $compression; ?>'"></td>
              <td class="text-end">
                <?php
                  echo '<a href="' . $CLICSHOPPING_Backup->link('Backup&Download&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/file_download.gif', $CLICSHOPPING_Backup->getDef('icon_file_downlad')) . '</a>';
                  echo '&nbsp;';
                  echo '<a href="' . $CLICSHOPPING_Backup->link('Restore&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/restore.gif', $CLICSHOPPING_Backup->getDef('icon_restore')) . '</a>';
                  echo '&nbsp;';
                  echo '<a href="' . $CLICSHOPPING_Backup->link('Delete&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Backup->getDef('icon_delete')) . '</a>';
                  echo '&nbsp;';
                ?>
              </td>
              </tr>
              <?php
            }
          }

          $dir->close();
        ?>
        </tbody>
      </table>
    </td>
  </table>
  <?php
    if (\defined('DB_LAST_RESTORE')) {
      ?>
      <div><?php echo $CLICSHOPPING_Backup->getDef('text_last_restoration') . ' ' . DB_LAST_RESTORE . ' <a href="' . $CLICSHOPPING_Backup->link('Backup.php&Forget') . '">' . $CLICSHOPPING_Backup->getDef('text_forget') . '</a>'; ?></div>
      <?php
    }
  }
  ?>
</div>


