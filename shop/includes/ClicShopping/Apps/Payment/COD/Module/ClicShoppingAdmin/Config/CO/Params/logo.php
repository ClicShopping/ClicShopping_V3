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

  namespace ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO\Params;

  class logo extends \ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {
    public $default = 'paiement_livraison.jpg';
    public $sort_order = 30;

    protected function init() {
      $this->title = $this->app->getDef('cfg_cod_no_logo_title');
      $this->description = $this->app->getDef('cfg_cod_logo_desc');
    }
  }
