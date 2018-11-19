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

  namespace ClicShopping\Apps\Catalog\Categories\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\RewriteUrl;

  class CategoryTree {

/**
 * Flag to control if the total number of products in a category should be calculated
 *
 * @var boolean
 * @access protected
 */

    protected $_show_total_products = false;

/**
 * Array containing the category structure relationship data
 *
 * @var array
 * @access protected
 */

    protected $_data = [];

    protected $root_category_id = 0;
    protected $max_level = 0;
    protected $root_start_string = '';
    protected $root_end_string = '';
    protected $parent_start_string = '';
    protected $parent_end_string = '';
    protected $parent_group_start_string = '<ul>';
    protected $parent_group_end_string = '</ul>';
    protected $parent_group_apply_to_root = false;
    protected $child_start_string = '<li>';
    protected $child_end_string = '</li>';
    protected $breadcrumb_separator = '_';
    protected $breadcrumb_usage = true;
    protected $spacer_string = '';
    protected $spacer_multiplier = 1;
    protected $follow_cpath = false;
    protected $cpath_array = [];
    protected $cpath_start_string = '';
    protected $cpath_end_string = '';
    protected $category_product_count_start_string = '&nbsp;(';
    protected $category_product_count_end_string = ')';
    protected $rewriteUrl;
    protected $db;

/**
 * Constructor; load the category structure relationship data from the database
 *
 * @access public
 */

    public function __construct() {
      static $_category_tree_data;

      $this->Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ( isset($_category_tree_data) ) {
        $this->_data = $_category_tree_data;
      } else {

        $Qcategories = $this->db->prepare('select c.categories_id,
                                           c.parent_id,
                                           c.categories_image,
                                           cd.categories_name,
                                           cd.categories_description
                                    from :table_categories c,
                                         :table_categories_description cd
                                    where c.categories_id = cd.categories_id
                                    and cd.language_id = :language_id
                                    order by c.parent_id,
                                             c.sort_order,
                                             cd.categories_name
                                    ');

        $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $Qcategories->setCache('categories-lang' . $CLICSHOPPING_Language->getId());

        $Qcategories->execute();

        while ( $Qcategories->fetch() ) {
          $this->_data[$Qcategories->valueInt('parent_id')][$Qcategories->valueInt('categories_id')] = ['name' => $Qcategories->value('categories_name'),
                                                                                                       'description' => $Qcategories->value('categories_description'),
                                                                                                       'image' => $Qcategories->value('categories_image'),
                                                                                                       'count' => 0
                                                                                                       ];
        }

        $_category_tree_data = $this->_data;
      }

      if (!Registry::exists('RewriteUrl')) {
        Registry::set('RewriteUrl', new RewriteUrl());
      }

      $this->rewriteUrl = Registry::get('RewriteUrl');
    }

/**
 * Count the categories
 * @return int
 */
    public function getCountCategories() {
      $Qcategories = $this->db->prepare('select count(categories_id) as count
                                         from :table_categories
                                        ');

      $Qcategories->execute();

      return($Qcategories->valueInt('count'));
    }

    public function reset() {
      $this->root_category_id = 0;
      $this->max_level = 0;
      $this->root_start_string = '';
      $this->root_end_string = '';
      $this->parent_start_string = '';
      $this->parent_end_string = '';
      $this->parent_group_start_string = '<ul>';
      $this->parent_group_end_string = '</ul>';
      $this->child_start_string = '<li>';
      $this->child_end_string = '</li>';
      $this->breadcrumb_separator = '_';
      $this->breadcrumb_usage = true;
      $this->spacer_string = '';
      $this->spacer_multiplier = 1;
      $this->follow_cpath = false;
      $this->cpath_array = [];
      $this->cpath_start_string = '';
      $this->cpath_end_string = '';
//      $this->_show_total_products = (SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT == '1') ? true : false;
      $this->category_product_count_start_string = '&nbsp;(';
      $this->category_product_count_end_string = ')';
    }

/**
 * Return a formated string representation of a category and its subcategories
 *
 * @param int $parent_id The parent ID of the category to build from
 * @param int $level Internal flag to note the depth of the category structure
 * @access protected
 * @return string
 */
    protected function _buildBranch($parent_id, $level = 0) {

      $result = ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0)) ? $this->parent_group_start_string : null;

      if ( isset($this->_data[$parent_id]) ) {
        foreach ( $this->_data[$parent_id] as $category_id => $category ) {
          if ( $this->breadcrumb_usage === true ) {
            $category_link = $this->buildBreadcrumb($category_id);
          } else {
            $category_link = $category_id;
          }

          $result .= $this->child_start_string;

          if ( isset($this->_data[$category_id]) ) {
            $result .= $this->parent_start_string;
          }

          if ( $level === 0 ) {
            $result .= $this->root_start_string;
          }

          $category_name = $this->getCategoryTreeTitle($category['name']);
          $categories_url = $this->getCategoryTreeUrl($category_link);

          if ( ($this->follow_cpath === true) && in_array($category_id, $this->cpath_array) ) {
            $link_title = $this->cpath_start_string . $category_name . $this->cpath_end_string;
          } else {
            $link_title = $category_name;
          }

          $result .= str_repeat($this->spacer_string, $this->spacer_multiplier * $level);

          $result .= HTML::link($categories_url, $link_title);
          if ( $this->_show_total_products === true ) {
            $result .= $this->category_product_count_start_string . $category['count'] . $this->category_product_count_end_string;
          }

          if ( $level === 0 ) {
            $result .= $this->root_end_string;
          }

          if ( isset($this->_data[$category_id]) ) {
            $result .= $this->parent_end_string;
          }

          if ( isset($this->_data[$category_id]) && (($this->max_level == '0') || ($this->max_level > $level+1)) ) {
            if ( $this->follow_cpath === true ) {
              if ( in_array($category_id, $this->cpath_array) ) {
                $result .= $this->_buildBranch($category_id, $level+1);
              }
            } else {
              $result .= $this->_buildBranch($category_id, $level+1);
            }
          }

          $result .= $this->child_end_string;
        }
      }

      $result .= ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0)) ? $this->parent_group_end_string : null;

      return $result;
    }

    public function buildBranchArray($parent_id, $level = 0, $result = '') {
      if (empty($result)) {
        $result = [];
      }

      if (isset($this->_data[$parent_id])) {
        foreach ($this->_data[$parent_id] as $category_id => $category) {
          if ($this->breadcrumb_usage === true) {
            $category_link = $this->buildBreadcrumb($category_id);
          } else {
            $category_link = $category_id;
          }

          $result = ['id' => $category_link,
                   'title' => str_repeat($this->spacer_string, $this->spacer_multiplier * $level) . $category['name']
                  ];

          if (isset($this->_data[$category_id]) && (($this->max_level == '0') || ($this->max_level > $level+1))) {
            if ($this->follow_cpath === true) {
              if (in_array($category_id, $this->cpath_array)) {
                $result = $this->buildBranchArray($category_id, $level+1, $result);
              }
            } else {
              $result = $this->buildBranchArray($category_id, $level+1, $result);
            }
          }
        }
      }

      return $result;
    }

    public function buildBreadcrumb($category_id, $level = 0) {
      $breadcrumb = '';

      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $id => $info) {
          if ($id == $category_id) {
            if ($level < 1) {
              $breadcrumb = $id;
            } else {
              $breadcrumb = $id . $this->breadcrumb_separator . $breadcrumb;
            }

            if ($parent != $this->root_category_id) {
              $breadcrumb = $this->buildBreadcrumb($parent, $level+1) . $breadcrumb;
            }
          }
        }
      }

      return $breadcrumb;
    }

/**
 * Return a formated string representation of the category structure relationship data
 *
 * @access public
 * @return string
 */

    public function getTree() {
      return $this->_buildBranch($this->root_category_id);
    }

/**
 * @access public
 * Magic function; return a formated string representation of the category structure relationship data
 * This is used when echoing the class object, eg:
 *
 * @return string
 */

    public function __toString() {
      return $this->getTree();
    }

    public  function getArray($parent_id = '') {
      return $this->buildBranchArray((empty($parent_id) ? $this->root_category_id : $parent_id));
    }

    public function exists($id) {
      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $category_id => $info) {
          if ($id == $category_id) {
            return true;
          }
        }
      }

      return false;
    }

    public function getChildren($category_id, &$array = []) {
      foreach ($this->_data as $parent => $categories) {
        if ($parent == $category_id) {
          foreach ($categories as $id => $info) {
            $array[] = $id;
            $this->getChildren($id, $array);
          }
        }
      }

      return $array;
    }

/**
 * Return category information
 *
 * @param int $id The category ID to return information of
 * @param string $key The key information to return (since v3.0.2)
 * @return mixed
 * @since v3.0.0
 */

    public function getData($id, $key = null) {
      foreach ( $this->_data as $parent => $categories ) {
        foreach ( $categories as $category_id => $info ) {
          if ( $id == $category_id ) {
            $data = ['id' => $id,
                    'name' => $info['name'],
                    'description' => $info['description'],
                    'parent_id' => $parent,
                    'image' => $info['image'],
                    'count' => $info['count']
                    ];

            return ( isset($key) ? $data[$key] : $data );
          }
        }
      }

      return false;
    }

/**
 * Return the parent ID of a category
 *
 * @param int $id The category ID to return the parent ID of
 * @return int
 * @since v3.0.2
 */

    public function getParentID($id) {
      return $this->getData($id, 'parent_id');
    }

/**
 * Calculate the number of products in each category
 *
 * @access protected
 */

    protected function _calculateProductTotals($filter_active = true) {
      $totals = [];

      $sql_query = 'select p2c.categories_id, count(*) as total
                    from :table_products p,
                        :table_products_to_categories p2c
                    where p2c.products_id = p.products_id';

      if ( $filter_active === true ) {
        $sql_query .= ' and p.products_status = :products_status';
      }

      $sql_query .= ' group by p2c.categories_id';

      if ( $filter_active === true ) {
        $Qtotals = $this->db->prepare($sql_query);
        $Qtotals->bindInt(':products_status', 1);
      } else {
        $Qtotals = $this->db->query($sql_query);
      }

      $Qtotals->execute();

      while ( $Qtotals->fetch() ) {
        $totals[$Qtotals->valueInt('categories_id')] = $Qtotals->valueInt('total');
      }

      foreach ( $this->_data as $parent => $categories ) {
        foreach ( $categories as $id => $info ) {
          if ( isset($totals[$id]) && ($totals[$id] > 0) ) {
            $this->_data[$parent][$id]['count'] = $totals[$id];

            $parent_category = $parent;

            while ( $parent_category != $this->root_category_id ) {
              foreach ( $this->_data as $parent_parent => $parent_categories ) {
                foreach ( $parent_categories as $parent_category_id => $parent_category_info ) {
                  if ( $parent_category_id == $parent_category ) {
                    $this->_data[$parent_parent][$parent_category_id]['count'] += $this->_data[$parent][$id]['count'];

                    $parent_category = $parent_parent;

                    break 2;
                  }
                }
              }
            }
          }
        }
      }
    }

    public function getNumberOfProducts($id) {
      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $category_id => $info) {
          if ($id == $category_id) {
            return $info['count'];
          }
        }
      }

      return false;
    }

    public function setRootCategoryID($root_category_id) {
      $this->root_category_id = $root_category_id;
    }

    public function setMaximumLevel($max_level) {
      $this->max_level = $max_level;
    }

    public function setRootString($root_start_string, $root_end_string) {
      $this->root_start_string = $root_start_string;
      $this->root_end_string = $root_end_string;
    }

    public function setParentString($parent_start_string, $parent_end_string) {
      $this->parent_start_string = $parent_start_string;
      $this->parent_end_string = $parent_end_string;
    }

    public function setParentGroupString($parent_group_start_string, $parent_group_end_string, $apply_to_root = false) {
      $this->parent_group_start_string = $parent_group_start_string;
      $this->parent_group_end_string = $parent_group_end_string;
      $this->parent_group_apply_to_root = $apply_to_root;
    }

    public function setChildString($child_start_string, $child_end_string) {
      $this->child_start_string = $child_start_string;
      $this->child_end_string = $child_end_string;
    }

    public function setBreadcrumbSeparator($breadcrumb_separator) {
      $this->breadcrumb_separator = $breadcrumb_separator;
    }

    public function setBreadcrumbUsage($breadcrumb_usage) {
      if ($breadcrumb_usage === true) {
        $this->breadcrumb_usage = true;
      } else {
        $this->breadcrumb_usage = false;
      }
    }

    public function setSpacerString($spacer_string, $spacer_multiplier = 2) {
      $this->spacer_string = $spacer_string;
      $this->spacer_multiplier = $spacer_multiplier;
    }

    public function setCategoryPath($cpath, $cpath_start_string = '', $cpath_end_string = '') {
      $this->follow_cpath = true;
      $this->cpath_array = explode($this->breadcrumb_separator, $cpath);
      $this->cpath_start_string = $cpath_start_string;
      $this->cpath_end_string = $cpath_end_string;
    }

    public function setFollowCategoryPath($follow_cpath) {
      if ($follow_cpath === true) {
        $this->follow_cpath = true;
      } else {
        $this->follow_cpath = false;
      }
    }

    public function setCategoryPathString($cpath_start_string, $cpath_end_string) {
      $this->cpath_start_string = $cpath_start_string;
      $this->cpath_end_string = $cpath_end_string;
    }

    public function setShowCategoryProductCount($show_category_product_count) {
      if ($show_category_product_count === true) {
        $this->_show_total_products = true;
      } else {
        $this->_show_total_products = false;
      }
    }

    public function setCategoryProductCountString($category_product_count_start_string, $category_product_count_end_string) {
      $this->category_product_count_start_string = $category_product_count_start_string;
      $this->category_product_count_end_string = $category_product_count_end_string;
    }

/**
 * Rewrite categories Name
 * @param $categories_name
 * @return string
 */
    public function getCategoryTreeTitle($categories_name) {
      $category_name = $this->rewriteUrl->getCategoryTreeTitle($categories_name);

      return $category_name;
    }

/**
 * Rewrite link of category
 * @param $categories_link
 * @return mixed
 */
    public function getCategoryTreeUrl($categories_id) {
      $categories_url = $this->rewriteUrl->getCategoryTreeUrl($categories_id);

      return $categories_url;
    }

/**
 * Rewrite link of Image
 * @param $categories_link
 * @return mixed
 */
    public function getCategoryTreeImageUrl($categories_id) {
      $categories_url = $this->rewriteUrl->getCategoryImageUrl($categories_id);

      return $categories_url;
    }

/**
 * Rewrite link of Image
 * @param $categories_link
 * @return mixed
 */
    public function getCategoryImageUrl($categories_id) {
      $category = $this->getPathCategories($categories_id);

      $categories_url = $this->rewriteUrl->getCategoryImageUrl($category);

      return $categories_url;
    }
  }
