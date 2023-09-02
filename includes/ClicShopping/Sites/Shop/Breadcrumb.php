<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function array_slice;
use function count;
use function is_null;

/**
 * The Breadcrumb class handles the breadcrumb navigation path
 */
class Breadcrumb implements \Iterator
{
  private array $_path = [];
  private string $_separator = ' &raquo; ';
  private $rewriteUrl;
  private array $_pathArray;

  /**
   * Resets the breadcrumb navigation path
   * @return void
   */
  public function reset(): void
  {
    $this->_path = [];
  }

  /**
   * Adds an entry to the breadcrumb navigation path
   *
   * @param string $title The title of the breadcrumb navigation entry
   * @param string $link The link of the breadcrumb navigation entry
   *
   */

  public function add(string $title, string $link = ''): void
  {
    if (!empty($link)) {
      $title = '<span class="breadcrumb-item breadcrumbCustomize">' . HTML::link(HTML::output($link), $title) . '</span>';
    }

    $this->_path[] = $title;
    $this->_pathArray[] = [
      'link' => HTML::outputProtected($link),
      'title' => $title
    ];
  }

  /**
   * return navigation path
   * @return string
   */
  public function getJsonBreadcrumb(): string
  {
    $itemlistelement = [];

    $array = $this->_pathArray;

    foreach ($array as $k => $v) {
      $itemlistelement[] = [
        '@type' => 'ListItem',
        'position' => $k,
        'item' => array('@id' => $v['link'],
          'name' => strip_tags($v['title']))
      ];
    }

    $schema_breadcrumb = ['@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => $itemlistelement
    ];

    $data = json_encode($schema_breadcrumb);

    $data = '<script type="application/ld+json">' . $data . '</script>';

    return $data;
  }

  /**
   * Returns the breadcrumb navigation path with the entries separated by $separator
   * @param string|null $separator
   * @return string
   */
  public function get(string $separator = null): string
  {
    if (is_null($separator)) {
      $separator = $this->_separator;
    }

    return implode($separator, $this->_path);
  }

  /**
   * Returns the breadcrumb navigation path array
   * @return array
   */

  public function getArray(): array
  {
    return $this->_path;
  }

  /**
   * Returns the breadcrumb separator
   *
   * @return string
   */

  public function getSeparator(): string
  {
    return $this->_separator;
  }

  /**
   * Sets the breadcrumb string separator
   *
   * @param string $separator The string to separator breadcrumb entries with
   * @return string
   */
  public function setSeparator(string $separator): ?string
  {
    $this->_separator = $separator;
  }

  /**
   * Overloaded rewind iterator function
   * @return void
   */
  public function Rewind(): void
  {
    //  return reset($this->_path); // php8.1 deprecated  to do
  }

  /**
   *  Overloaded current iterator function
   * @return string
   */
  public function current(): mixed
  {
    return current($this->_path);
  }

  /**
   * Overloaded key iterator function
   * @return string
   *
   */
  public function key(): string
  {
    return key($this->_path);
  }

  /**
   * Overloaded next iterator function
   *
   */
  public function Next(): void
  {
    // return next($this->_path);  // php8.1 deprecated  to do
  }

  /**
   * Overloaded next iterator function
   * @return bool
   */
  public function Valid(): bool
  {
    return (current($this->_path) !== false);
  }

  /**
   *  get manufacturer (brand) inside the categories
   * @return string
   */
  public function getCategoriesManufacturer(): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_CategoryCommon = Registry::get('CategoryCommon');
    $CLICSHOPPING_Category = Registry::get('Category');
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_Manufacturer = Registry::get('Manufacturers');
    $CLICSHOPPING_Prod = Registry::get('Prod');

    $this->rewriteUrl = Registry::get('RewriteUrl');

    $result = null;

    // calculate category path
    if ($CLICSHOPPING_Category->getPath()) {
      $cPath = $CLICSHOPPING_Category->getPath();
    } elseif ($CLICSHOPPING_Prod->getID() && !$CLICSHOPPING_Manufacturer->getID()) {
      $cPath = $CLICSHOPPING_Category->getProductPath($CLICSHOPPING_Prod->getID());
    } else {
      $cPath = '';
    }

    if (!empty($cPath)) {
      $cPath_array = $CLICSHOPPING_CategoryCommon->getParseCategoryPath($cPath);
    }

// add category names or the manufacturer name to the breadcrumb trail
    if (isset($cPath_array)) {
      for ($i = 0, $n = count($cPath_array); $i < $n; $i++) {
        $Qcategories = $CLICSHOPPING_Db->get('categories_description', 'categories_name', [
            'categories_id' => (int)$cPath_array[$i],
            'language_id' => $CLICSHOPPING_Language->getId()
          ]
        );

        if ($Qcategories->fetch() !== false) {
          $categories_url = $this->rewriteUrl->getCategoryTreeUrl(implode('_', array_slice($cPath_array, 0, ($i + 1))));

          $result = $CLICSHOPPING_Breadcrumb->add($Qcategories->value('categories_name'), $categories_url);
        } else {
          break;
        }
      }
    } elseif ($CLICSHOPPING_Prod->getID()) {
      $Qmanufacturer = $CLICSHOPPING_Db->get('manufacturers', 'manufacturers_name', ['manufacturers_id' => (int)$CLICSHOPPING_Manufacturer->getID()]);

      if ($Qmanufacturer->fetch() !== false) {
        $manufacturer_url = $this->rewriteUrl->getManufacturerUrl($CLICSHOPPING_Manufacturer->getID());

        $result = $CLICSHOPPING_Breadcrumb->add($Qmanufacturer->value('manufacturers_name'), $manufacturer_url);
      }
    }

    if (is_null($result)) {
      return false;
    } else {
      return $result;
    }
  }
}
