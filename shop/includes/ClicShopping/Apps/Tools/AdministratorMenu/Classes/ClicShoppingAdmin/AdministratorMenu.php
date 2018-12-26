<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class AdministratorMenu {

    protected $category_id;
    protected $language_id;

/**
 *  Return catagories path
 *
 * @param string $current_category_id
 * @return string $cPath_new,
 * @access public
 */
    public static function getPath($current_category_id = '')   {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      $cPath_array = $CLICSHOPPING_CategoriesAdmin->getPathArray();

      if ($current_category_id == '') {
        $cPath_new = implode('_', $cPath_array);
      } else {
        if (count($cPath_array) == 0) {
          $cPath_new = $current_category_id;
        } else {
          $cPath_new = '';

          $Qlast = $CLICSHOPPING_Db->get('administrator_menu', 'parent_id', ['id' => (int)$cPath_array[(count($cPath_array) - 1)]]);

          $Qcurrent = $CLICSHOPPING_Db->get('administrator_menu', 'parent_id', ['id' => (int)$current_category_id]);

          if ($Qlast->valueInt('parent_id') === $Qcurrent->valueInt('parent_id')) {
            for ($i = 0, $n = count($cPath_array) - 1; $i < $n; $i++) {
              $cPath_new .= '_' . $cPath_array[$i];
            }
          } else {
            for ($i = 0, $n = count($cPath_array); $i < $n; $i++) {
              $cPath_new .= '_' . $cPath_array[$i];
            }
          }

          $cPath_new .= '_' . $current_category_id;

          if (substr($cPath_new, 0, 1) == '_') {
            $cPath_new = substr($cPath_new, 1);
          }
        }
      }

      return 'cPath=' . $cPath_new;
    }

/**
 * the category name
 *
 * @param string $category_id , $language_id
 * @return string $category['categories_name'],  name of the categorie
 * @access public
 */
    public static function getAdministratorMenuLabel($id, $language_id)  {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();
      $Qcategory = Registry::get('Db')->get('administrator_menu_description', 'label', ['id' => (int)$id, 'language_id' => (int)$language_id]);

      return $Qcategory->value('label');
    }

/**
*  remove category
*
* @param string $category_id
* @return string
* @access public
*/
    public static function removeCategory($id) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->delete('administrator_menu', ['id' => (int)$id]);
      $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$id]);

      Cache::clear('menu-administrator');
    }


/**
 * category tree
 *
 * @param string $parent_id , $spacing, $exclude, $category_tree_array , $include_itself
 * @return string $category_tree_array, the tree of category
 * @access public
 */
    public static function getLabelTree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false)  {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

      if (!is_array($category_tree_array)) $category_tree_array = [];
      if ((count($category_tree_array) < 1) && ($exclude != '0')) $category_tree_array[] = ['id' => '0', 'text' => $CLICSHOPPING_AdministratorMenu->getDef('text_top')];

      if ($include_itself) {
        $Qcategory = $CLICSHOPPING_Db->get('administrator_menu_description', 'label', ['language_id' => (int)$CLICSHOPPING_Language->getId(),
                                                                                       'id' => (int)$parent_id
                                                                                      ]
                                          );

        $category_tree_array[] = ['id' => $parent_id,
                                  'text' => $Qcategory->value('label')
                                 ];
      }


      $Qcategories = $CLICSHOPPING_Db->get(['administrator_menu c',
                                            'administrator_menu_description cd'
                                          ], [
                                            'c.id',
                                            'cd.label',
                                            'c.parent_id'
                                          ], [
                                            'c.id' => [
                                              'rel' => 'cd.id'
                                            ],
                                            'cd.language_id' => (int)$CLICSHOPPING_Language->getId(),
                                            'c.parent_id' => (int)$parent_id
                                          ], [
                                              'c.sort_order',
                                              'cd.label'
                                            ]
                                          );


      while ($Qcategories->fetch()) {
        if ($exclude != $Qcategories->valueInt('id')) $category_tree_array[] = ['id' => $Qcategories->valueInt('id'), 'text' => $spacing . $Qcategories->value('label')];
        $category_tree_array = static::getLabelTree($Qcategories->valueInt('id'), $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      }

      return $category_tree_array;
    }

/**
 * getGeneratedAdministratorMenuPathIds
 *
 * @param string $id , $from,
 * @return string $calculated_category_path_string
 * @access public
 */

    public static function getGeneratedAdministratorMenuPathIds($id)  {
      $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

      $calculated_category_path_string = '';
      $calculated_category_path = static::getGenerateCategoryPath($id);

      for ($i = 0, $n = count($calculated_category_path); $i < $n; $i++) {
        for ($j = 0, $k = count($calculated_category_path[$i]); $j < $k; $j++) {
          $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
        }
        $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br />';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

      if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = $CLICSHOPPING_AdministratorMenu->getDef('text_top');

      return $calculated_category_path_string;
    }


    public static function getGenerateCategoryPath($id, $categories_array = '', $index = 0) {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!is_array($categories_array)) $categories_array = [];

      $Qcategory = $CLICSHOPPING_Db->get([
                                    'administrator_menu c',
                                    'administrator_menu_description cd'
                                  ], [
                                    'cd.label',
                                    'c.parent_id'
                                  ], [
                                    'c.id' => [
                                    'val' => (int)$id,
                                    'rel' => 'cd.id'
                                  ],
                                    'cd.language_id' => (int)$CLICSHOPPING_Language->getId()
                                  ]
                                );

      $categories_array[$index][] = [
                                      'id' => (int)$id,
                                      'text' => $Qcategory->value('label')
                                    ];

      if ( (!is_null($Qcategory->valueInt['parent_id'])) && ($Qcategory->valueInt('parent_id') != '0') ) $categories_array = static::getGenerateBlogCategoryPath($Qcategory->valueInt('parent_id'), 'category', $categories_array, $index);

      return $categories_array;
    }

/**
 * remove Administatrator Menu Category
 *
 * @param string $id
 * @return string
 * @access public
 */
    public static  function getRemoveAdministratorMenuCategory($id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $QImage = $CLICSHOPPING_Db->prepare('select image
                                    from :table_administrator_menu
                                    where id = :id
                                   ');
      $QImage->bindInt(':id', (int)$id);
      $QImage->execute();

// Controle si l'image est utilise sur une autre categorie
      $QduplicateImage = $CLICSHOPPING_Db->prepare('select count(*) as total
                                              from :table_administrator_menu
                                              where image = :image
                                             ');
      $QduplicateImage->bindValue(':image', $QImage->value('image'));
      $QduplicateImage->execute();

// Controle si l'image est utilise sur une autre categorie
      $QduplicateImageCategories = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                        from :table_administrator_menu
                                                        where image = :image
                                                       ');
      $QduplicateImageCategories->bindValue(':image',  $QImage->value('image') );
      $QduplicateImageCategories->execute();

      if (($QduplicateImage->valueInt('total') < 2) &&  ($QduplicateImageCategories->valueInt('total') == 0)) {
// delete categorie image
        if (is_file($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $QImage->value('image'))) {
          @unlink($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $QImage->value('image'));
        }
      }

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                      from :table_administrator_menu
                                      where id = :id
                                    ');
      $Qdelete->bindInt(':id',  (int)$id);
      $Qdelete->execute();


      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                      from :table_administrator_menu_description
                                      where id = :id
                                    ');
      $Qdelete->bindInt(':id',  (int)$id);
      $Qdelete->execute();

    }



/**
 * category tree
 *
 * @param string $parent_id, $spacing, $exclude, $category_tree_array , $include_itself
 * @return string $category_tree_array, the tree of category
 * @access public
 */
    public static function getAdministratorMenuCategoryTree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

      if (!is_array($category_tree_array)) $category_tree_array = [];
      if ( (count($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = ['id' => '0', 'text' => $CLICSHOPPING_AdministratorMenu->getDef('text_top')];

      if ($include_itself) {

        $Qcategory = $CLICSHOPPING_Db->prepare('select label
                                          from :table_administrator_menu_description
                                          where language_id = :language_id
                                          and id = :parent_id
                                         ');

        $Qcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qcategory->bindInt(':parent_id', (int)$parent_id);
        $Qcategory->execute();

        $category_tree_array[] = ['id' => $parent_id, 'text' => $Qcategory->value('label')];
      }

      $Qcategory = $CLICSHOPPING_Db->prepare('select c.id,
                                               cd.label,
                                               c.parent_id
                                        from :table_administrator_menu c,
                                             :table_administrator_menu_description cd
                                        where c.id = cd.id
                                        and cd.language_id = :language_id
                                        and c.parent_id = :parent_id
                                        order by c.sort_order,
                                                 cd.label
                                      ');

      $Qcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
      $Qcategory->bindInt(':parent_id', (int)$parent_id);
      $Qcategory->execute();

      while ($Qcategory->fetch()) {

        if ($exclude != $Qcategory->valueInt('id')) $category_tree_array[] = ['id' => $Qcategory->valueInt('id'), 'text' => $spacing . $Qcategory->value('label')];
        $category_tree_array = static::getAdministratorMenuCategoryTree($Qcategory->valueInt('id'), $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      }

      return $category_tree_array;
    }

// Count how many subcategories exist in a category
    public static function getChildsInMenuCount($id) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $categories_count = 0;

      $Qcategories = $CLICSHOPPING_Db->prepare('select id
                                        from :table_administrator_menu
                                        where parent_id = :parent_id
                                        ');

      $Qcategories->bindInt(':parent_id', $id );
      $Qcategories->execute();

      while ($Qcategories->fetch() !== false) {
        $categories_count++;

        $categories_count += call_user_func(__METHOD__, $Qcategories->valueInt('id'));
      }

      return $categories_count;
    }
  }
