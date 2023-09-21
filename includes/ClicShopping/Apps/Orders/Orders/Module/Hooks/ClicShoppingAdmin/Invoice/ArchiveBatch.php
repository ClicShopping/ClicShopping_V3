<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\Orders\Module\Hooks\ClicShoppingAdmin\Invoice;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Orders\Orders\Orders as OrdersApp;

class ArchiveBatch implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected $Id;

  public function __construct()
  {
    if (isset($_GET['aID'])) {
      $archive_id = HTML::sanitize($_GET['aID']);
    } else {
      $archive_id = 0;
    }

    if (!Registry::exists('Orders')) {
      Registry::set('Orders', new OrdersApp());
    }

    $this->app = Registry::get('Orders');

    $this->Id = HTML::sanitize($archive_id);
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_ORDERS_OD_STATUS')) {
      return false;
    }

    if ($this->Id != 1) {
      $output = '&nbsp;';

      $output .= '
              <a data-bs-toggle="modal" data-bs-target="#myModalBatchArchive">' . HTML::button($this->app->getDef('button_archive'), null, null, 'primary') . '</a>
              <div class="modal fade" id="myModalBatchArchive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title text-start" id="myModalLabel">' . $this->app->getDef('text_archive_batch_heading') . '</h4>
                    </div>
                    <div class="modal-body text-center">
                      ' . HTML::form('archive', $this->app->link('Orders')) . '
                        ' . HTML::hiddenField('aID', 1) . '
                       </form>
                      ' . HTML::form('archive', $this->app->link('Orders&ArchiveBatch')) . '
                        <div class="separator"></div>
                        <div class="col-md-12">
                          <div class="col-md-12 text-start">' . $this->app->getDef('label_text_order_number') . '</div>
                          <span class="col-md-6">' . $this->app->getDef('label_text_start') . HTML::inputField('orders_id_start', '', 'aria-required="true" placeholder="10"') . '</span>
                          <span class="col-md-6">' . $this->app->getDef('label_text_end') . HTML::inputField('orders_id_end', '', 'aria-required="true" placeholder="50"') . '</span><br />
                        </div>
                        <div class="separator"></div>
                        <div>
                        ' . HTML::button($this->app->getDef('button_archive_batch'), null, null, 'primary') . ' ' . HTML::button($this->app->getDef('button_archive_consult'), null, $this->app->link('Orders&aID=1'), 'secondary') . '
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
        ';

      return $output;
    }
  }
}