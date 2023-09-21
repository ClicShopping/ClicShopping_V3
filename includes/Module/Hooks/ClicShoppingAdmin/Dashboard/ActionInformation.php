<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Db;

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
                      <label for="' . CLICSHOPPING::getDef('title_db_index') . '" class="col-9 col-form-label">' . CLICSHOPPING::getDef('title_db_index') . '</label>
                      <div class="col-md-3">
                        ' . DB::sizeDb() . ' MB
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-11 mainTable">
                    <div class="form-group row">
                      <label for="System Memory" class="col-9 col-form-label">' . CLICSHOPPING::getDef('title_system_memory') . '</label>
                      <div class="col-md-3">
                        ' . $memory . ' MB
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-11 mainTable">
                    <div class="form-group row">
                      <label for="Bootstrap includes Memory Usage" class="col-9 col-form-label">' . CLICSHOPPING::getDef('title_core_memory') . '</label>
                      <div class="col-md-3">
                        <div class="separator"></div>
                        <div class="progress">
                          <div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' . $valuenow . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $valuenow . '%;">
                             <strong>' . $valuenow . '%</strong>
                          </div>
                        </div> 
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