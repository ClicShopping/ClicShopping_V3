<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO\Params;

  class logo extends \ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'paiement_livraison.jpg';
    public ?int $sort_order = 30;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_cod_no_logo_title');
      $this->description = $this->app->getDef('cfg_cod_logo_desc');
    }
  }
