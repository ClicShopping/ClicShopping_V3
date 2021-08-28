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

  namespace ClicShopping\Apps\Catalog\Categories\Classes\Shop;

  use ClicShopping\OM\Registry;

  /**
   * The Category class manages category information
   */
  class Category
  {
    protected $_id;
    protected string $_title;
    protected string $_image;
    protected $_parent_id;
    protected string $_description;
    protected string $_category_depth;
    protected array $_data = [];
    protected mixed $db;
    protected mixed $lang;
    protected $categoryTree;
    protected $rewriteUrl;

    /**
     * Constructor
     *
     * @param int $id The ID of the category to retrieve information from
     *
     */

    public function __construct($id = null)
    {
      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');
      $this->categoryTree = Registry::get('CategoryTree');

      if (!isset($id) && isset($_GET['cPath'])) {
        $cPath_array = array_unique(array_filter(explode('_', $_GET['cPath']), 'is_numeric'));

        if (!empty($cPath_array)) {
          $id = end($cPath_array);
        }
      }

      if (isset($id) && $this->categoryTree->exists($id)) {
        $this->_data = $this->categoryTree->getData($id);

        $this->_id = $this->_data['id'];
        $this->_title = $this->_data['name'];
        $this->_description = $this->_data['description'];
        $this->_image = $this->_data['image'];
        $this->_parent_id = $this->_data['parent_id'];

        if (isset($this->_data['category_depth'])) {
          $this->_category_depth = $this->_data['category_depth'];
        } else {
          $this->_category_depth = 0;
        }
      }

      if (!Registry::exists('RewriteUrl')) {
        Registry::set('RewriteUrl', new RewriteUrl());
      }

      $this->rewriteUrl = Registry::get('RewriteUrl');
    }

    /**
     * Return the ID of the assigned category
     * @return integer
     */

    public function getID()
    {
      return $this->_id;
    }

    /**
     * Return the description of the assigned category
     * @return string
     */

    public function getDescription()
    {
      return $this->_description;
    }


    /**
     * Return the title of the assigned category
     * @return string
     */

    public function getTitle()
    {
      return $this->_title;
    }

    /**
     * Check if the category has an image
     * @return string
     */

    public function hasImage()
    {
      return (!empty($this->_image));
    }

    /**
     * Return the image of the assigned category
     * @return string
     */

    public function getImage()
    {
      return $this->_image;
    }

    /**
     * Check if the assigned category has a parent category
     * @return boolean
     */

    public function hasParent()
    {
      return ($this->_parent_id > 0);
    }

    /**
     * Return the parent ID of the assigned category
     * @return integer
     */

    public function getParent()
    {
      return $this->_parent_id;
    }

    /**
     * Return the breadcrumb path of the assigned category
     * @return string
     */

    public function getPath()
    {
      return $this->categoryTree->buildBreadcrumb($this->_id);
    }

    /**
     * Return the the path about the subcategory
     * string current_category_id =  the current categry id
     * @return string the new path
     */
    public function getPathCategories($current_category_id = '')
    {
      $cPath_array = $this->getPathArray();

      if (empty($current_category_id)) {
        $cPath_new = $this->getPathArray($cPath_array);
      } else {
        if (\count($cPath_array) == 0) {
          $cPath_new = $current_category_id;
        } else {
          $cPath_new = '';

          $insert_sql = [
            'categories_id' => (int)$cPath_array[(\count($cPath_array) - 1)],
            'status' => 1
          ];

          $Qlast = $this->db->get('categories', 'parent_id', $insert_sql);

            $insert_sql = [
              'categories_id' => (int)$current_category_id,
              'status' => 1
            ];

            $Qcurrent = $this->db->get('categories', 'parent_id', $insert_sql);

          if ($Qlast->valueInt('parent_id') === $Qcurrent->valueInt('parent_id')) {
            for ($i = 0, $n = \count($cPath_array) - 1; $i < $n; $i++) {
              $cPath_new .= '_' . $cPath_array[$i];
            }
          } else {
            for ($i = 0, $n = \count($cPath_array); $i < $n; $i++) {
              $cPath_new .= $cPath_array[$i];
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
     * Return the breadcrumb path of the assigned category
     * @return string
     */
    public function getPathArray($id = null)
    {
      $cPath_array = explode('_', $this->getPath());

      if (isset($id)) {
        return $cPath_array[$id];
      }

      return $cPath_array;
    }

    /**
     * Return specific information from the assigned category
     * @return mixed
     */

    public function getData($keyword)
    {
      return $this->_data[$keyword];
    }


    /**
     * Return deph the assigned category
     * @return mixed
     */

    public function getDepth()
    {
      $this->_category_depth = 'top';

      if (isset($_GET['cPath']) && !\is_null($_GET['cPath'])) {
        $Qcheck = $this->db->prepare('select products_id
                                      from :table_products_to_categories
                                      where categories_id = :categories_id
                                      limit 1
                                     ');
        $Qcheck->bindInt(':categories_id', $this->_id);
        $Qcheck->execute();

        if ($Qcheck->fetch() === false) {
          $this->_category_depth = 'products'; // display products
        } else {
          $Qcheck = $this->db->prepare('select categories_id
                                         from :table_categories
                                         where parent_id = :parent_id
                                         and status = 1
                                        ');
          $Qcheck->bindInt(':parent_id', $this->_id);
          $Qcheck->execute();

          if ($Qcheck->fetch() !== false) {
            $this->_category_depth = 'nested'; // navigate through the categories
          } else {
            $this->_category_depth = 'products'; // category has no products, but display the 'no products' message
          }
        }
      }

      return $this->_category_depth;
    }

    /**
     * Return a numlber about listing related themain category
     * @return number of the product in the main category
     */

    public function getCountCategoriesNested()
    {
      $Qcategories = $this->db->prepare('select count(*) as total
                                         from :table_categories c,
                                              :table_products_to_categories cd
                                         where c.parent_id = 0
                                         and c.categories_id = cd.categories_id
                                         and cd.categories_id = :categories_id
                                         and status = 1
                                       ');
      $Qcategories->bindInt(':categories_id', $this->_id);
      $Qcategories->execute();

      $total = $Qcategories->valueInt('total');

      return $total;
    }

    /**
     * Return True False in function the category is sub or not
     * string , $category_id, id of category
     *
     * @return number of the product in the main category
     */

    public function getHasSubCategories($category_id)
    {
      $Qcheck = $this->db->prepare('select categories_id
                                    from :table_categories
                                    where parent_id = :parent_id
                                    and status = 1
                                    limit 1
                                  ');
      $Qcheck->bindInt(':parent_id', $category_id);
      $Qcheck->execute();

      return ($Qcheck->fetch() !== false);
    }


    /**
     * Return all sub categories
     * string , $subcategories_array, id of category
     * string  $parent_id, id of the parent category
     *
     */
    public function getSubcategories(&$subcategories_array, $parent_id = 0)
    {
      $Qsub = $this->db->prepare('select categories_id
                                  from :table_categories
                                  where parent_id = :parent_id
                                  and status = 1
                                  ');
      $Qsub->bindInt(':parent_id', $parent_id);
      $Qsub->execute();

      while ($Qsub->fetch()) {
        $subcategories_array[count($subcategories_array)] = $Qsub->valueInt('categories_id');

        if ($Qsub->valueInt('categories_id') != $parent_id) {
          $this->getSubcategories($subcategories_array, $Qsub->valueInt('categories_id'));
        }
      }
    }

      /**
       * Return all  categories
       * @param null $categories_array
       * @param int|null $parent_id
       * @param string|null $indent
       * @return array
       */

    public function getCategories(?array $categories_array = null, ?int $parent_id = 0, string $indent = '') :?array
    {
      $Qcategories = $this->db->prepare('select c.categories_id,
                                                cd.categories_name
                                        from :table_categories c,
                                             :table_categories_description cd
                                        where parent_id = :parent_id
                                        and c.categories_id = cd.categories_id
                                        and cd.language_id = :language_id
                                        and c.virtual_categories = 0
                                        and c.status = 1
                                        order by sort_order,
                                                 cd.categories_name
                                       ');
      $Qcategories->bindInt(':parent_id', $parent_id);
      $Qcategories->bindInt(':language_id', $this->lang->getId());
      $Qcategories->execute();

      while ($Qcategories->fetch()) {
        $array = [
          'id' => $Qcategories->valueInt('categories_id'),
          'text' => $indent . $Qcategories->value('categories_name')
        ];

        if ($Qcategories->valueInt('categories_id') !== $parent_id) {
          $categories_array[] = $this->getCategories($array, $Qcategories->valueInt('categories_id'), $indent . '&nbsp;&nbsp;');
        }
      }

      return $categories_array;
    }


    /**
     * Recursively go through the categories and retreive all parent categories IDs
     * @param $categories
     * @param $categories_id
     * @return bool
     */
    public function getParentCategories(&$categories, $categories_id)
    {

      $Qparent = $this->db->prepare('select parent_id
                                    from :table_categories
                                    where categories_id = :categories_id
                                    and status = 1
                                    ');

      $Qparent->bindInt(':categories_id', $categories_id);
      $Qparent->execute();

      while ($Qparent->fetch()) {
        if ($Qparent->valueInt('parent_id') == 0) {
          return true;
        }

        $categories[count($categories)] = $Qparent->valueInt('parent_id');

        if ($Qparent->valueInt('parent_id') != $categories_id) {
          $this->getParentCategories($categories, $Qparent->valueInt('parent_id'));
        }
      }
    }

    /**
     * Construct a category path to the product
     * @param $products_id
     * @return string
     */
    public function getProductPath($products_id)
    {
      $cPath = '';

      $Qcategory = $this->db->prepare('select p2c.categories_id
                                      from :table_products p,
                                           :table_products_to_categories p2c
                                      where p.products_id = :products_id
                                      and p.products_status = 1
                                      and p.products_id = p2c.products_id
                                      and p.products_archive = 0
                                      limit 1
                                      ');
      $Qcategory->bindInt(':products_id', $products_id);

      $Qcategory->execute();

      if ($Qcategory->fetch() !== false) {
        $categories = [];
        $this->getParentCategories($categories, $Qcategory->valueInt('categories_id'));

        $categories = array_reverse($categories);

        $cPath = implode('_', $categories);

        if (!\is_null($cPath)) {
          $cPath .= '_';
        }

        $cPath .= $Qcategory->valueInt('categories_id');
      }

      return $cPath;
    }

    /**
     * Rewrite link of Image
     * @param $categories_link
     * @return mixed
     */
    public function getCategoryImageUrl($categories_id)
    {
      $category = $this->getPathCategories($categories_id);

      $categories_url = $this->rewriteUrl->getCategoryImageUrl($category);

      return $categories_url;
    }


    public function getCategoryTitle($categories_name)
    {
      $category_name = $this->rewriteUrl->getCategoryTreeTitle($categories_name);

      return $category_name;
    }
  }
