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

  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions\Langues;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Save extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Langues');
    }

    public function execute() {

      $lID = HTML::sanitize($_GET['lID']);
      $name = HTML::sanitize($_POST['name']);
      $code = HTML::sanitize(substr($_POST['code'], 0, 2));
      $image = HTML::sanitize($_POST['image']);
      $directory = HTML::sanitize($_POST['directory']);
      $sort_order = (int)HTML::sanitize($_POST['sort_order']);

      $this->app->db->save('languages', [
                                          'name' => $name,
                                          'code' => $code,
                                          'image' => $image,
                                          'directory' => $directory,
                                          'sort_order' => (int)$sort_order,
                                          'status' => 1
                                          ],
                                          [
                                            'languages_id' => (int)$lID
                                          ]
                            );


      if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
        $this->app->db->save('configuration', [
                                                'configuration_value' => $code
                                                ],
                                                [
                                                  'configuration_key' => 'DEFAULT_LANGUAGE'
                                                ]
                            );
      }

      Cache::clear('languages-system-shop');
      Cache::clear('languages-system-admin');

      $this->app->redirect('Langues&page=' . $_GET['page'] . '&lID=' . $_GET['lID']);
    }
  }