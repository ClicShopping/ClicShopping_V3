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

  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions\Langues;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Save extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Langues');
    }

    public function execute()
    {

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
      $lID = HTML::sanitize($_GET['lID']);
      $name = HTML::sanitize($_POST['name']);
      $code = HTML::sanitize(substr($_POST['code'], 0, 2));
      $image = HTML::sanitize($_POST['image']);
      $directory = HTML::sanitize($_POST['directory']);
      $sort_order = (int)HTML::sanitize($_POST['sort_order']);
      $locale = HTML::sanitize($_POST['locale']);

      $this->app->db->save('languages', [
        'name' => $name,
        'code' => $code,
        'image' => $image,
        'directory' => $directory,
        'sort_order' => (int)$sort_order,
        'status' => 1,
        'locale' => $locale
      ],
        ['languages_id' => (int)$lID]
      );

      if (isset($_POST['default'])) {
        $this->app->db->save('configuration', ['configuration_value' => $code],
          ['configuration_key' => 'DEFAULT_LANGUAGE']
        );
      }

      Cache::clear('languages-system-shop');
      Cache::clear('languages-system-admin');

      $this->app->redirect('Langues&page=' . $page . '&lID=' . $_GET['lID']);
    }
  }