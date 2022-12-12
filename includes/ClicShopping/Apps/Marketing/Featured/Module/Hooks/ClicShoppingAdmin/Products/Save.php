<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

  class Save implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Featured')) {
        Registry::set('Featured', new FeaturedApp());
      }

      $this->app = Registry::get('Featured');
    }

    private function saveProductsFeatured(int $id) :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!empty($_POST['products_featured'])) {
        $CLICSHOPPING_Db->save('products_featured', ['products_id' => (int)$id,
            'products_featured_date_added' => 'now()',
            'status' => 1,
            'customers_group_id' => 0
          ]
        );
      }
    }

    private function save(int $id) :void
    {
      $this->saveProductsFeatured($id);
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_FEATURED_FE_STATUS') || CLICSHOPPING_APP_FEATURED_FE_STATUS == 'False') {
        return false;
      }
    
      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);
        $this->save($id);
      }
    }
  }