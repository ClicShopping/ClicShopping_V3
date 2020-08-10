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

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $cPath;

    public function __construct()
    {
      $this->app = Registry::get('Products');

      $this->currentCategoryId = HTML::sanitize($_POST['cPath']);
    }

    public function execute()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      $id = null;
      $_POST['cPath'] = $this->currentCategoryId;

      $CLICSHOPPING_ProductsAdmin->save($id, 'Insert');

      $CLICSHOPPING_Hooks->call('Products', 'Insert');

      $this->app->redirect('Products&cPath=' . $this->currentCategoryId);
    }
  }