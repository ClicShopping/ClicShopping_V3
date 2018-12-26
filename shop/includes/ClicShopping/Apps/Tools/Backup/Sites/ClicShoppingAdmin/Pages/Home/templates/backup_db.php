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
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Backup = Registry::get('Backup');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';

  $dir_ok = false;
  if (is_dir($backup_directory)) {
    if (FileSystem::isWritable($backup_directory)) {
      $dir_ok = true;
    } else {
      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_backup_directory_not_writeable'), 'error');
    }
  } else {
    $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_backup_directory_does_not_exist'), 'error');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/backup.gif', $CLICSHOPPING_Backup->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Backup->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>


    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Backup->getDef('text_info_heading_new_backup'); ?></strong></div>
    <?php  echo HTML::form('backup', $CLICSHOPPING_Backup->link('Backup&BackupNow')); ?>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $CLICSHOPPING_Backup->getDef('text_info_new_backup'); ?></div>
        <div class="separator"></div>
        <div>
<?php
  if (is_file(LOCAL_EXE_GZIP)) $contents[] = array('text' => HTML::radioField('compress', 'gzip') . ' ' . $CLICSHOPPING_Backup->getDef('text_info_use_gzip'));
?>
        </div>
        <div class="separator"></div>
        <div class="col-md-12"><?php echo HTML::radioField('compress', 'no', true) . ' ' . $CLICSHOPPING_Backup->getDef('text_info_use_no_compression'); ?><br/></div>
        <div class="separator"></div>
        <div class="col-md-12"><?php echo HTML::radioField('compress', 'zip') . ' ' . $CLICSHOPPING_Backup->getDef('text_info_use_zip'); ?><br/></div>
<?php
  if ($dir_ok === true) {
?>
            <div class="separator"></div>
            <div class="col-md-12"><?php echo HTML::checkboxField('download', 'yes') . ' ' . $CLICSHOPPING_Backup->getDef('text_info_download_only')  . '*<br /><br />*' .  $CLICSHOPPING_Backup->getDef('text_info_best_through_https'); ?><br/></div>
<?php
  } else {
?>
            <div class="separator"></div>
            <div class="col-md-12"><?php echo HTML::radioField('download', 'yes', true) . ' ' . $CLICSHOPPING_Backup->getDef('text_info_download_only') . '*<br /><br />*' . $CLICSHOPPING_Backup->getDef('text_info_best_through_https'); ?><br/></div>
<?php
  }
?>
        <div class="col-md-12 text-md-center">
          <span><br /><?php echo HTML::button($CLICSHOPPING_Backup->getDef('button_backup'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Backup->getDef('button_cancel'), null, $CLICSHOPPING_Backup->link('Backup'), 'warning', null, 'sm'); ?></span>
        </div>
      </div>
    </div>


    </form>
  </div>


