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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Db;

  use ClicShopping\Sites\ClicShoppingAdmin\IndexAdmin;

  class ActionInformation {

    public function __construct() {
      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }
    }

    public function execute() {
// Space_disk
      $space_disk = '';
// bandwidth
      $bandwidth = '';
// backup
      $backup = '';

      $memory = round(memory_get_usage() / 1048576,2);

      $memory_start = memory_get_usage(false);
      $memory_end = memory_get_peak_usage(false);
      $valuemax = (int)ini_get('memory_limit');
      $valuenow = round(($memory_end-$memory_start)/1024/1024/$valuemax, 3)*100;

      $output = '
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">' . CLICSHOPPING::getDef('space') . '</span>
                  <span class="col-md-6 main text-md-right">' . $space_disk . '</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">' . CLICSHOPPING::getDef('bandwith') . '</span>
                  <span class="col-md-6 main text-md-right">' . $bandwidth . '</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">' . CLICSHOPPING::getDef('title_web_site_size') . '</span>
                  <span class="col-md-6 main text-md-right">' . IndexAdmin::getSizeReadable(IndexAdmin::getDirSize('.')) . '</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">' . CLICSHOPPING::getDef('title_db_index') . '</span>
                  <span class="col-md-6 main text-md-right">' . DB::sizeDb() . ' MB' . '</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">' . CLICSHOPPING::getDef('backup_site') . '</span>
                  <span class="col-md-6 main text-md-right">' . $backup . '</span>
                </div>
              </div>

              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">System Memory (get usage)</span>
                  <span class="col-md-6 main text-md-right">"use : ' . $memory . ' Megabytes</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <span class="col-md-6 main">Bootstrap Core Memory Usage</span>
                  <span class="col-md-6 main text-md-right">' . $valuenow . ' %</span>
                </div>
              </div>
            ';
      return $output;
    }
  }