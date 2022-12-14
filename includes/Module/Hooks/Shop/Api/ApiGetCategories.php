<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\Shop\Api;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ApiGetCategories
  {
    /**
     * @param int|null $id
     * @param int|string $language_id
     * @return array
     */
    private static function categories(int|string $id, int|string $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id)) {
        $sql_request = ' and c.categories_id = :categories_id';
      } else {
        $sql_request = '';
      }

      if (is_numeric($language_id)) {
        $sql_language_request = ' and cd.language_id = :language_id';
      } else {
        $sql_language_request = '';
      }

      $Qapi = $CLICSHOPPING_Db->prepare('select c.*,
                                                cd.*
                                         from :table_categories c,
                                              :table_categories_description cd 
                                         where c.categories_id = cd.categories_id
                                         ' . $sql_request . '
                                         ' . $sql_language_request . '
                                      ');
      if (is_numeric($id)) {
        $Qapi->bindInt(':categories_id', $id);
      }

      if (is_numeric($language_id)) {
        $Qapi->bindInt(':language_id', $language_id);
      }

      $Qapi->execute();

      $categories_data = [];

      $result = $Qapi->fetchAll();

      foreach ($result as $value) {
        $categories_data[] = [
          'categories_id'           => $value['categories_id'],
          'parent_id'               => $value['parent_id'],
          'language_id'             => $value['language_id'],
          'categories_name'     	  => $value['categories_name'],
          'categories_description'  => $value['categories_description'],
        ];
      }

      return $categories_data;
    }

    public function execute()
    {
      $id = HTML::sanitize($_GET['cId']);

      if (isset($_GET['cId'], $_GET['token'])) {
        if (isset($_GET['lId'])) {
          $language_id = HTML::sanitize($_GET['lId']);
        } else {
          $language_id = '';
        }

        return static::categories($id, $language_id);
      } else {
        return false;
      }
    }
  }
