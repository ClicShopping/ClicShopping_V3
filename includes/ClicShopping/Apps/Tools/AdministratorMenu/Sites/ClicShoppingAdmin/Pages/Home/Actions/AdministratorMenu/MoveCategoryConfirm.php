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


  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\AdministratorMenu;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  class MoveCategoryConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $Id;
    protected $moveToCategoryID;
    protected $cPath;

    public function __construct()
    {
      $this->app = Registry::get('AdministratorMenu');

      $this->Id = HTML::sanitize($_GET['id']);
      $this->moveToCategoryID = HTML::sanitize($_POST['move_to_category_id']);
      $this->cPath = HTML::sanitize($_GET['cPath']);
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($this->Id) && ($this->Id != $this->moveToCategoryID)) {
        $new_parent_id = $this->moveToCategoryID;

        $path = explode('_', AdministratorMenu::getGeneratedAdministratorMenuPathIds($new_parent_id));

        if (in_array($this->Id, $path)) {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_move_directory_to_parent'), 'error');

          $this->app->redirect('AdministratorMenu&cPath=' . (int)$this->cPath . '&cID=' . (int)$this->Id);
        } else {

          $this->app->db->save('administrator_menu', [
            'parent_id' => (int)$new_parent_id
          ], [
              'id' => (int)$this->Id
            ]
          );

          Cache::clear('menu-administrator');

          $this->app->redirect('AdministratorMenu&cPath=' . (int)$new_parent_id . '&cID=' . (int)$this->Id);
        }
      }
    }
  }