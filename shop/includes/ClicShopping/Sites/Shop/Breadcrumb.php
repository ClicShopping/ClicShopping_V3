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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

/**
* The Breadcrumb class handles the breadcrumb navigation path
*/

  class Breadcrumb implements \Iterator {

/**
 * An array containing the breadcrumb navigation path
 *
 * @var array
 * @access private
 */

    private $_path = [];

/**
 * The string to separate the breadcrumb entries with
 *
 * @var string
 * @access private
 */

    private $_separator = ' &raquo; ';

    protected $rewriteUrl;

/**
 * Resets the breadcrumb navigation path
 *
 * @access public
 */

    public function reset() {
      $this->_path = [];
    }

/**
 * Adds an entry to the breadcrumb navigation path
 *
 * @param string $title The title of the breadcrumb navigation entry
 * @param string $link The link of the breadcrumb navigation entry
 * @access public
 */

    public function add($title, $link = null) {
      if ( !empty($link) ) {
        $title = '<span class="BreadcrumbCustomize" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . HTML::outputProtected($link) .'" itemprop="item"><span itemprop="name">' . $title .'</span></a></span>';
      }

      $this->_path[] = $title;
    }

/**
 * Returns the breadcrumb navigation path with the entries separated by $separator
 *
 * @param string $separator The string value to separate the breadcrumb navigation path entries with
 * @access public
 * @return string
 */

    public function get($separator = null) {
      if ( is_null($separator) ) {
        $separator = $this->_separator;
      }

      return implode($separator, $this->_path);
    }

/**
 * Returns the breadcrumb navigation path array
 *
 * @access public
 * @return array
 */

    public function getArray() {
      return $this->_path;
    }

/**
 * Returns the breadcrumb separator
 *
 * @access public
 * @return string
 */

    public function getSeparator() {
      return $this->_separator;
    }

/**
* Sets the breadcrumb string separator
*
* @param string $separator The string to separator breadcrumb entries with
* @access public
* @return string
*/

    public function setSeparator($separator) {
      $this->_separator = $separator;
    }

/**
 * Overloaded rewind iterator function
 *
 * @access public
 */

    public function rewind() {
      return reset($this->_path);
    }

/**
 * Overloaded current iterator function
 *
 * @access public
 */

    public function current() {
      return current($this->_path);
    }

/**
 * Overloaded key iterator function
 *
 * @access public
 */

    public function key() {
      return key($this->_path);
    }

/**
 * Overloaded next iterator function
 *
 * @access public
 */

    public function next() {
      return next($this->_path);
    }

/**
 * Overloaded valid iterator function
 *
 * @access public
 */

    public function valid() {
      return ( current($this->_path) !== false );
    }

/**
 * get manufacturer (brand) inside the catgories
 * @return mixed
 */
    public function getCategoriesManufacturer() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_CategoryCommon = Registry::get('CategoryCommon');
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Manufacturer = Registry::get('Manufacturers');
      $CLICSHOPPING_Prod = Registry::get('Prod');

      $this->rewriteUrl = Registry::get('RewriteUrl');

      // calculate category path
      if ($CLICSHOPPING_Category->getPath()) {
        $cPath = $CLICSHOPPING_Category->getPath();
      } elseif ($CLICSHOPPING_Prod->getID() && !$CLICSHOPPING_Manufacturer->getID()) {
        $cPath = $CLICSHOPPING_Category->getProductPath($CLICSHOPPING_Prod->getID());
      } else {
        $cPath = '';
      }

      if ( !empty($cPath) ) {
        $cPath_array = $CLICSHOPPING_CategoryCommon->getParseCategoryPath($cPath);
      }

// add category names or the manufacturer name to the breadcrumb trail
      if (isset($cPath_array)) {
        for ($i=0, $n=count($cPath_array); $i<$n; $i++) {

          $Qcategories = $CLICSHOPPING_Db->get('categories_description', 'categories_name', ['categories_id' => (int)$cPath_array[$i],
                                                                                             'language_id' => $CLICSHOPPING_Language->getId()
                                                                                            ]
                                              );

          if ($Qcategories->fetch() !== false) {
             $categories_url = $this->rewriteUrl->getCategoryTreeUrl(implode('_', array_slice($cPath_array, 0, ($i+1))));

            $result =  $CLICSHOPPING_Breadcrumb->add($Qcategories->value('categories_name'), $categories_url);
          } else {
            break;
          }
        }
      } elseif ($CLICSHOPPING_Prod->getID()) {
        $Qmanufacturer = $CLICSHOPPING_Db->get('manufacturers', 'manufacturers_name', ['manufacturers_id' => (int)$CLICSHOPPING_Manufacturer->getID()]);

        if ( $Qmanufacturer->fetch() !== false ) {
          $manufacturer_url = $this->rewriteUrl->getManufacturerUrl($CLICSHOPPING_Manufacturer->getID());

          $result = $CLICSHOPPING_Breadcrumb->add($Qmanufacturer->value('manufacturers_name'), $manufacturer_url);
        }
      }

      return $result;
    }
  }
