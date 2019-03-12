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

  namespace ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class CategoriesAdmin {

    public $categorie;
    protected $lang;
    protected $template;
    protected $db;

    public function __construct() {

      $this->db =  Registry::get('Db');
      $this->lang = Registry::get('Language');
      $this->template = Registry::get('TemplateAdmin');
    }

/**
 * @param null $keywords
 * @return mixed
 */
    public function getSearch($keywords = null) {
      $current_category_id = HTML::sanitize($_POST['cPath']);

      if (is_null($current_category_id)) $current_category_id = 0;
      if (isset($_POST['cPath'])) $current_category_id = $_POST['cPath'];
      if (isset($_GET['cPath'])) $current_category_id = $_GET['cPath'];

      if (isset($keywords) && !empty($keywords)) {
        $search = HTML::sanitize($keywords);

        $Qcategories= $this->db->prepare('select SQL_CALC_FOUND_ROWS c.categories_id,
                                                                     cd.categories_name,
                                                                     c.categories_image,
                                                                     c.parent_id,
                                                                     c.sort_order,
                                                                     c.date_added,
                                                                     c.last_modified ,
                                                                     c.status,
                                                                     c.virtual_categories
                                          from :table_categories c,
                                               :table_categories_description cd
                                          where c.categories_id = cd.categories_id
                                          and cd.language_id = :language_id
                                          and cd.categories_name like :search
                                          order by c.sort_order,
                                                   cd.categories_name
                                          limit :page_set_offset, :page_set_max_results
                                          ');
        $Qcategories->bindInt(':language_id', (int)$this->lang->getId() );
        $Qcategories->bindValue(':search', '%' . $search . '%');
        $Qcategories->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
        $Qcategories->execute();
      } else {
        $Qcategories= $this->db->prepare('select SQL_CALC_FOUND_ROWS c.categories_id,
                                                                     cd.categories_name,
                                                                     c.categories_image,
                                                                     c.parent_id,
                                                                     c.sort_order,
                                                                     c.date_added,
                                                                     c.last_modified,
                                                                     c.status,
                                                                     c.virtual_categories
                                        from :table_categories c,
                                             :table_categories_description cd
                                        where c.parent_id = :parent_id
                                        and c.categories_id = cd.categories_id
                                        and cd.language_id = :language_id
                                        order by c.sort_order,
                                                  cd.categories_name
                                        limit :page_set_offset, :page_set_max_results
                                        ');

        $Qcategories->bindInt(':parent_id', $current_category_id );
        $Qcategories->bindInt(':language_id', $this->lang->getId() );
        $Qcategories->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
        $Qcategories->execute();
      }

      return $Qcategories;
    }

/**
 * Return the breadcrumb path of the assigned category
 *
 * @access public
 * @return string
 */
    public function getPathArray($id = null) {
      $CLICSHOPPING_CategoryCommon = Registry::get('CategoryCommon');

// calculate category path
      if ((isset($_POST['cPath']) && !empty($_POST['cPath'])) || (isset($_GET['cPath']) && !empty($_GET['cPath']))) {
        if (isset($_POST['cPath'])) {
          $cPath = HTML::sanitize($_POST['cPath']);
        } else {
          $cPath = HTML::sanitize($_GET['cPath']);
        }

        $cPath_array = $CLICSHOPPING_CategoryCommon->getParseCategoryPath($cPath);
      } else {
        $cPath_array = [];
      }

      if (isset($id)) {
        return $cPath_array[$id];
      }

      return $cPath_array;
    }


/**
 * the category name
 *
 * @param string  $category_id, $language_id
 * @return string $category['categories_name'],  name of the categorie
 * @access public
 */
    public function getCategoryName($category_id, $language_id) {

      if (!$language_id) $language_id = $this->lang->getId();
      $Qcategory = Registry::get('Db')->get('categories_description', 'categories_name', ['categories_id' => (int)$category_id, 'language_id' => $language_id]);

      return $Qcategory->value('categories_name');
    }


/**
 * the category description
 *
 * @param string  $category_id, $language_id
 * @return string $category['blog_categories_name'],  description of the blog categorie
 * @access public
 */
    public function getCategoryDescription($category_id, $language_id) {

      if (!$language_id) $language_id = $this->lang->getId();

      $Qcategory = $this->db->prepare('select categories_description
                                        from :table_categories_description
                                        where categories_id = :categories_id
                                        and language_id = :language_id
                                      ');
      $Qcategory->bindInt(':categories_id', (int)$category_id);
      $Qcategory->bindInt(':language_id', (int)$language_id);

      $Qcategory->execute();

      return $Qcategory->value('categories_description');
    }

/**
 * Count how many subcategories exist in a category
 * @param $categories_id
 * @return int|mixed
 */
// pb avec static function

    public function getChildsInCategoryCount($categories_id) {
      $categories_count = 0;

      $Qcategories = Registry::get('Db')->get('categories', 'categories_id', ['parent_id' => (int)$categories_id]);

      while ($Qcategories->fetch() !== false) {
        $categories_count++;

        $categories_count += call_user_func(__METHOD__, $Qcategories->valueInt('categories_id'));
      }

      return $categories_count;
    }


/**
 * Count how many products exist in a category
 * @param $categories_id
 * @param bool $include_deactivated
 * @return mixed
 */
    public function getCatalogInCategoryCount($categories_id, $include_deactivated = false) {
      if ($include_deactivated) {
        $Qproducts = $this->db->get([
                                      'products p',
                                      'products_to_categories p2c'
                                      ], [
                                      'count(*) as total'
                                      ], [
                                        'p.products_id' => [
                                          'rel' => 'p2c.products_id'
                                        ],
                                        'p2c.categories_id' => (int)$categories_id
                                      ]
                                    );
      } else {
        $Qproducts = $this->db->get([
                                      'products p',
                                      'products_to_categories p2c'
                                      ], [
                                      'count(*) as total'
                                      ], [
                                        'p.products_id' => [
                                          'rel' => 'p2c.products_id'
                                        ],
                                        'p.products_status' => '1',
                                        'p2c.categories_id' => (int)$categories_id
                                      ]
                                    );
      }

      $products_count = $Qproducts->valueInt('total');

      $Qchildren = $this->db->get('categories', 'categories_id', ['parent_id' => (int)$categories_id]);


      while ($Qchildren->fetch() !== false) {
        $products_count += call_user_func(__METHOD__, $Qchildren->valueInt('categories_id'), $include_deactivated);
      }

      return $products_count;
    }

/**
 * @param $id
 * @param string $from
 * @return bool|string
 */
    public function getGeneratedCategoryPathIds($id, $from = 'category') {
      $calculated_category_path_string = '';
      $calculated_category_path = $this->getGenerateCategoryPath($id, $from);
      for ($i=0, $n=count($calculated_category_path); $i<$n; $i++) {
        for ($j=0, $k=count($calculated_category_path[$i]); $j<$k; $j++) {
          $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
        }
        $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br />';
      }

      $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

      if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = $this->db->getDef('text_top');

      return $calculated_category_path_string;
    }

/**
 *  remove category
 *
 * @param string $category_id
 * @return string
 * @access public
 */
    public function removeCategory($category_id) {
      $QcategoriesImage = $this->db->prepare('select categories_image
                                               from :table_categories
                                               where categories_id = :categories_id
                                             ');
      $QcategoriesImage->bindInt(':categories_id',  (int)$category_id);

      $QcategoriesImage->execute();


// Controle si l'image est utilise sur une autre categorie
      $QduplicateImage = $this->db->prepare('select count(*) as total
                                             from :table_categories
                                             where categories_image = :categories_image
                                             ');
      $QduplicateImage->bindValue(':categories_image', $QcategoriesImage->value('categories_image'));

      $QduplicateImage->execute();

// Controle si l'image est utilise sur une autre categorie du blog
      $QduplicateBlogImage = $this->db->prepare('select count(*) as total
                                                  from :table_blog_categories
                                                  where blog_categories_image = :blog_categories_image
                                                ');
      $QduplicateBlogImage->bindValue(':blog_categories_image', $QcategoriesImage->value('categories_image'));

      $QduplicateBlogImage->execute();

// Controle si l'image est utilise sur les descriptions d'un blog
      $QduplicateImageBlogCategoriesDescription = $this->db->prepare('select count(*) as total
                                                                     from :table_blog_categories_description
                                                                     where blog_categories_description like :blog_categories_description
                                                                    ');
      $QduplicateImageBlogCategoriesDescription->bindValue(':blog_categories_description', '%' . $QcategoriesImage->value('categories_image') . '%');

      $QduplicateImageBlogCategoriesDescription->execute();

// Controle si l'image est utilise le visuel d'un produit
      $QduplicateImageCatalog = $this->db->prepare('select count(*) as total
                                                    from :table_products
                                                    where products_image = :products_image
                                                    or products_image_zoom = :products_image_zoom
                                                   ');
      $QduplicateImageCatalog->bindValue(':products_image',  $QcategoriesImage->value('categories_image') );
      $QduplicateImageCatalog->bindValue(':products_image_zoom', $QcategoriesImage->value('categories_image') );

      $QduplicateImageCatalog->execute();

// Controle si l'image est utilise sur les descriptions d'un produit
      $QduplicateImageProductDescription = $this->db->prepare('select count(*) as total
                                                               from :table_products_description
                                                               where products_description like :blog_categories_description
                                                              ');
      $QduplicateImageProductDescription->bindValue(':blog_categories_description', '%' . $QcategoriesImage->value('categories_image') . '%');

      $QduplicateImageProductDescription->execute();

// Controle si l'image est utilisee sur une banniere
      $QduplicateImageBanners = $this->db->prepare('select count(*) as total
                                                    from :table_banners
                                                    where banners_image = :banners_image
                                                   ');
      $QduplicateImageBanners->bindValue(':banners_image', $QcategoriesImage->value('categories_image') );

      $QduplicateImageBanners->execute();

// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageManufacturers = $this->db->prepare('select count(*) as total
                                                          from :table_manufacturers
                                                          where manufacturers_image = :manufacturers_image
                                                         ');
      $QduplicateImageManufacturers->bindValue(':manufacturers_image', $QcategoriesImage->value('categories_image') );

      $QduplicateImageManufacturers->execute();

// Controle si l'image est utilisee sur les fournisseurs
      $QduplicateImageSuppliers = $this->db->prepare('select count(*) as total
                                                                  from :table_suppliers
                                                                  where suppliers_image = :suppliers_image
                                                                 ');
      $QduplicateImageSuppliers->bindValue(':suppliers_image', $QcategoriesImage->value('categories_image') );

      $QduplicateImageSuppliers->execute();

      if (($QduplicateImage->valueInt('total') < 2) &&
        ($QduplicateBlogImage->valueInt('total') == 0) &&
        ($QduplicateImageBlogCategoriesDescription->valueInt('total') == 0) &&
        ($QduplicateImageCatalog->valueInt('total') == 0) &&
        ($QduplicateImageProductDescription->valueInt('total') == 0) &&
        ($QduplicateImageBanners->valueInt('total') == 0) &&
        ($QduplicateImageManufacturers->valueInt('total') == 0) &&
        ($QduplicateImageSuppliers->valueInt('total') == 0)) {

// delete categorie image
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $QcategoriesImage->value('categories_image'))) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $QcategoriesImage->value('categories_image'));
        }
      }

      $this->db->delete('categories', ['categories_id' => (int)$category_id]);
      $this->db->delete('categories_description', ['categories_id' => (int)$category_id]);
      $this->db->delete('products_to_categories', ['categories_id' => (int)$category_id]);

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');
    }

/**
 *  Return catagories path
 *
 * @param string $current_category_id
 * @return string $cPath_new,
 * @access public
 *
 */
    public function getCategoriesPath($current_category_id = '') {
      $cPath_array = $this->getPathArray();

      if ($current_category_id == '') {
        $cPath_new = implode('_', $cPath_array);
      } else {
        if (count($cPath_array) == 0) {
          $cPath_new = $current_category_id;
        } else {
          $cPath_new = '';

          $Qlast = $this->db->get('categories', 'parent_id', ['categories_id' => (int)$cPath_array[(count($cPath_array)-1)]]);

          $Qcurrent = $this->db->get('categories', 'parent_id', ['categories_id' => (int)$current_category_id]);

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
 * category tree
 *
 * @param string $parent_id, $spacing, $exclude, $category_tree_array , $include_itself
 * @return string $category_tree_array, the tree of category
 * @access public
 */
    public function getCategoryTree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {

      if (!is_array($category_tree_array)) $category_tree_array = [];
       if ( (count($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = ['id' => '0', 'text' => CLICSHOPPING::getDef('text_top')];

      if ($include_itself) {
        $Qcategory = $this->db->get('categories_description', 'categories_name', ['language_id' => $this->lang->getId(),
                                                                                  'categories_id' => (int)$parent_id
                                                                                  ]
                                    );

        $category_tree_array[] = ['id' => $parent_id,
                                  'text' => $Qcategory->value('categories_name')
                                 ];
      }

      $Qcategories = $this->db->get(['categories c',
                                     'categories_description cd'
                                    ], [
                                      'c.categories_id',
                                      'cd.categories_name',
                                      'c.parent_id'
                                    ], [
                                      'c.categories_id' => [
                                        'rel' => 'cd.categories_id'
                                      ],
                                      'cd.language_id' => $this->lang->getId(),
                                      'c.parent_id' => (int)$parent_id
                                    ], [
                                        'c.sort_order',
                                        'cd.categories_name'
                                      ]
                                    );


      while ($Qcategories->fetch()) {
        if ($exclude != $Qcategories->valueInt('categories_id')) $category_tree_array[] = array('id' => $Qcategories->valueInt('categories_id'), 'text' => $spacing . $Qcategories->value('categories_name'));
        $category_tree_array = $this->getCategoryTree($Qcategories->valueInt('categories_id'), $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      }

      return $category_tree_array;
    }

/*
 * getPath category path
 * @int : id of category
 * @return $cPath_new, the new path
*/
    public function getPath($current_category_id = '') {
      $cPath_array = $this->getPathArray();

      if ($current_category_id == '') {
        $cPath_new = implode('_', $cPath_array);
      } else {
        if (count($cPath_array) == 0) {
          $cPath_new = $current_category_id;
        } else {
          $cPath_new = '';

          $Qlast = $this->db->get('categories', 'parent_id', ['categories_id' => (int)$cPath_array[(count($cPath_array)-1)]]);

          $Qcurrent = $this->db->get('categories', 'parent_id', ['categories_id' => (int)$current_category_id]);

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
 * Move the categories and products another category
 * @param $id
 * @param string $from
 * @param string $categories_array
 * @param int $index
 * @return array|mixed|string
 */
    public function getGenerateCategoryPath($id, $from = 'category', $categories_array = '', $index = 0) {
      if (!is_array($categories_array)) {
        $categories_array = [];
      }

      if ($from == 'product') {
        $Qcategories = $this->db->get('products_to_categories', 'categories_id', ['products_id' => (int)$id]);

        while ($Qcategories->fetch()) {
          if ($Qcategories->valueInt('categories_id') === 0) {
            $categories_array[$index][] = [
                                          'id' => '0',
                                          'text' => CLICSHOPPING::getDef('text_top')
                                          ];
          } else {
            $Qcategory = $this->db->get([
                                        'categories c',
                                        'categories_description cd'
                                        ], [
                                        'cd.categories_name',
                                        'c.parent_id'
                                        ], [
                                          'c.categories_id' => [
                                            'val' => $Qcategories->valueInt('categories_id'),
                                            'rel' => 'cd.categories_id'
                                          ],
                                          'cd.language_id' => (int)$this->lang->getId()
                                        ]
                                      );

            $categories_array[$index][] = ['id' => $Qcategories->valueInt('categories_id'),
                                          'text' => $Qcategory->value('categories_name')
                                          ];

/*
            if ($Qcategory->valueInt('parent_id') > 0) {
              $categories_array = call_user_func(__FUNCTION__, $Qcategory->valueInt('parent_id'), 'category', $categories_array, $index);
            }
*/
            $categories_array[$index] = array_reverse($categories_array[$index]);
          }

          $index++;
        }
      } elseif ($from == 'category') {
        $Qcategory = $this->db->get(['categories c',
                                     'categories_description cd'
                                    ], [
                                    'cd.categories_name',
                                    'c.parent_id'
                                    ], [
                                      'c.categories_id' => [
                                        'val' => (int)$id,
                                        'rel' => 'cd.categories_id'
                                      ],
                                      'cd.language_id' => (int)$this->lang->getId()
                                    ]
                                  );

        $categories_array[$index][] = ['id' => (int)$id,
                                       'text' => $Qcategory->value('categories_name')
                                      ];

        if ($Qcategory->valueInt('parent_id') > 0) {
          $categories_array = call_user_func(__FUNCTION__, $Qcategory->valueInt('parent_id'), 'category', $categories_array, $index);
        }
      }

      return $categories_array;
    }

/**
 * @access public
 * @param $id
 * @param string $from
 * @return bool|string
 */
    public function getOutputGeneratedCategoryPath($id, $from = 'category') {
      $calculated_category_path_string = '';

      $calculated_category_path = $this->getGenerateCategoryPath($id, $from);

      for ($i=0, $n=count($calculated_category_path); $i<$n; $i++) {
        for ($j=0, $k=count($calculated_category_path[$i]); $j<$k; $j++) {
          $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
        }

        $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br />';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

      if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = CLICSHOPPING::getDef('text_top');

      return $calculated_category_path_string;
    }
  }