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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Db;

  use ClicShopping\Sites\ClicShoppingAdmin\IndexAdmin;

  class ActionInformation
  {

    public function __construct()
    {
      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }
    }

    public function execute()
    {
// Space_disk
      $space_disk = '';
// bandwidth
      $bandwidth = '';
// backup
      $backup = '';

      $memory = round(memory_get_usage() / 1048576, 2);

      $memory_start = memory_get_usage(false);
      $memory_end = memory_get_peak_usage(false);
      $valuemax = (int)ini_get('memory_limit');
      $valuenow = round(($memory_end - $memory_start) / 1024 / 1024 / $valuemax, 3) * 100;

      $output = '
              <div class="separator"></div>
              <div class="col-md-12">
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef('title_web_site_size') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('title_web_site_size') . '</label>
                    <div class="col-md-3">
                      ' . IndexAdmin::getSizeReadable(IndexAdmin::getDirSize('.')) . '
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef('title_db_index') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('title_db_index') . '</label>
                    <div class="col-md-3">
                      ' . DB::sizeDb() . ' MB' . '
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="System Memory" class="col-9 col-form-label">System Memory (get usage)</label>
                    <div class="col-md-3">
                      use : ' . $memory . ' Megabytes
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="Bootstrap Core Memory Usage" class="col-9 col-form-label">Bootstrap Core Memory Usage</label>
                    <div class="col-md-3">
                      ' . $valuenow . ' %
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef('space') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('space') . '</label>
                    <div class="col-md-3">
                      ' . $space_disk . '
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef('bandwith') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('bandwith') . '</label>
                    <div class="col-md-3">
                      ' . $bandwidth . '
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef('backup_site') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('backup_site') . '</label>
                    <div class="col-md-3">
                      ' . $backup . '
                    </div>
                  </div>
                </div>
              </div>
            </div>
            ';
      return $output;
    }
  }