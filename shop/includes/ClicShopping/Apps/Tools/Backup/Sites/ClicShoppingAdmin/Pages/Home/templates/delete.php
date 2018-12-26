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

  $CLICSHOPPING_Backup = Registry::get('Backup');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $info = [
          'file' => $file,
          'date' => date($CLICSHOPPING_Backup->getDef('php_date_time_format'), filemtime($backup_directory . $file)),
          'size' => number_format(filesize($backup_directory . $entry)) . ' file',
          ];

  switch (substr(file, -3)) {
    case 'zip': $info['compression'] = 'ZIP'; break;
    case '.gz': $info['compression'] = 'GZIP'; break;
    default: $info['compression'] = $CLICSHOPPING_Backup->getDef('text_no_extension'); break;
  }

  $buInfo = new ObjectInfo($info);
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

    <div class="col-md-12 mainTitle"><strong><?php echo $buInfo->date ; ?></strong></div>
    <?php echo HTML::form('delete', $CLICSHOPPING_Backup->link('Backup&DeleteConfirm&file=' . $_GET['file'])); ?>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $CLICSHOPPING_Backup->getDef('text_delete_intro'); ?><br/></div>
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $buInfo->file; ?><br/><br/></div>
        <div class="col-md-12 text-md-center">
          <span><br /><?php echo HTML::button($CLICSHOPPING_Backup->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Backup->getDef('button_cancel'), null, $CLICSHOPPING_Backup->link('Backup'), 'warning', null, 'sm'); ?></span>
        </div>
      </div>
    </div>
    </form>
  </div>


