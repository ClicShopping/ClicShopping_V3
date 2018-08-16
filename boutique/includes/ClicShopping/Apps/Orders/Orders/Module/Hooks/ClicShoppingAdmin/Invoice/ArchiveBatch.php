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

  namespace ClicShopping\Apps\Orders\Orders\Module\Hooks\ClicShoppingAdmin\Invoice;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Orders\Orders\Orders as OrdersApp;

  class ArchiveBatch implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $Id;

    public function __construct() {
      global $archive_id;

      if (!Registry::exists('Orders')) {
        Registry::set('Orders', new OrdersApp());
      }

      $this->app = Registry::get('Orders');

      $this->Id = HTML::sanitize($archive_id);
    }

    public function execute() {

      if (!defined('CLICSHOPPING_APP_ORDERS_OD_STATUS')) {
        return false;
      }

      if ($this->Id != 1) {
        $output = '&nbsp;';

        $output .= '
              <a data-toggle="modal" data-target="#myModalBatchArchive">' . HTML::button($this->app->getDef('button_archive'), null, null, 'primary') . '</a>
              <div class="modal fade" id="myModalBatchArchive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title text-md-left" id="myModalLabel">' . $this->app->getDef('text_archive_batch_heading') . '</h4>
                    </div>
                    <div class="modal-body text-md-center">
                      ' . HTML::form('archive', $this->app->link('Orders')) .'
                        ' . HTML::hiddenField('aID', 1) . '
                       </form>
                      ' . HTML::form('archive',  $this->app->link('Orders&ArchiveBatch')) . '
                        <div class="separator"></div>
                        <div class="col-md-12">
                          <div class="col-md-12 text-md-left">' . $this->app->getDef('label_text_order_number') . '</div>
                          <span class="col-md-6">' . $this->app->getDef('label_text_start') . HTML::inputField('orders_id_start', '', 'aria-required="true" placeholder="10"') . '</span>
                          <span class="col-md-6">' . $this->app->getDef('label_text_end') . HTML::inputField('orders_id_end', '', 'aria-required="true" placeholder="50"') . '</span><br />
                        </div>
                        <div class="separator"></div>
                        <div>
                        ' . HTML::button($this->app->getDef('button_archive'), null, null, 'primary') . ' ' .  HTML::button($this->app->getDef('button_archive_consult'), null, $this->app->link('Orders&aID=1'), 'secondary') . '
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
        ';
      }

      return $output;
    }
  };