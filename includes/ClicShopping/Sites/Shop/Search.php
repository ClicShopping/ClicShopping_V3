<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop;

use ClicShopping\OM\DateTime;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function count;
use function defined;
use function in_array;
use function is_array;

/**
 * Handles various operations related to product search, including filtering by year,
 * date, price, manufacturer, keywords, description, and categories. It also provides
 * methods to retrieve and validate search parameters.
 */
class Search
{
  protected $_period_min_year;
  protected $_period_max_year;
  protected string $_keywords;
  protected $_description;
  protected $_date_from;
  protected $_date_to;
  protected $_price_to;
  protected $_price_from;
  protected $_manufacturer;
  protected $_category;
  protected $_result;
  protected $column_list;
  private mixed $db;
  protected bool $checkManufacturer = false;
  protected bool $_recursive = false;
  protected $listing;

  /**
   * Initializes the object by setting up the database connection and querying the minimum and maximum years
   * from the products table. The resulting values are used to initialize the period range.
   *
   * @return void
   */
  public function __construct()
  {
    $this->db = Registry::get('Db');

    $Qproducts = $this->db->query('select min(year(products_date_added)) as min_year,
                                             max(year(products_date_added)) as max_year
                                      from :table_products
                                      limit 1
                                      ');
    $Qproducts->execute();

    $this->_period_min_year = $Qproducts->valueInt('min_year');
    $this->_period_max_year = $Qproducts->valueInt('max_year');
  }

  /**
   * Retrieves the minimum year of the period.
   *
   * @return string The minimum year as a string.
   */
  public function getMinYear(): string
  {
    return $this->_period_min_year;
  }

  /**
   * Retrieves the maximum year of the period.
   *
   * @return string The maximum year of the period.
   */
  public function getMaxYear(): string
  {
    return $this->_period_max_year;
  }

  /**
   * Retrieves the starting date ('dfrom') from either POST or GET request.
   * - If 'dfrom' is set and non-empty in the POST request, it sanitizes and sets it.
   * - If not found in POST, checks GET for 'dfrom', sanitizes, and sets it.
   * - If 'dfrom' is not set in both, defaults to an empty string.
   *
   * @return string The sanitized starting date or an empty string if not provided.
   */
  public function getDateFrom(): string
  {
    if (isset($_POST['dfrom']) && !empty($_POST['dfrom'])) {
      $this->_date_from = HTML::sanitize($_POST['dfrom']);
    } elseif (isset($_GET['dfrom']) && !empty($_GET['dfrom'])) {
      $this->_date_from = HTML::sanitize($_GET['dfrom']);
    } else {
      $this->_date_from = '';
    }

    return $this->_date_from;
  }

  /**
   * Determines if the dateFrom property is valid.
   *
   * @return bool True if the dateFrom property is valid, false otherwise.
   */
  public function hasDateFrom(): bool
  {
    $dfromDateTime = new DateTime($this->getDateFrom(), false);

    if ($dfromDateTime->isValid() === false) {
      $datefrom = false;
    } else {
      $datefrom = true;
    }

    return $datefrom;
  }

  /**
   * Retrieves the ending date value based on the 'dto' parameter from the POST or GET request.
   * Sanitizes the retrieved value to ensure safety.
   *
   * @return string The sanitized 'dto' value or an empty string if not provided.
   */
  public function getDateTo(): string
  {
    if (isset($_POST['dto']) && !empty($_POST['dto'])) {
      $this->_date_to = HTML::sanitize($_POST['dto']);
    } elseif (isset($_GET['dto']) && !empty($_GET['dto'])) {
      $this->_date_to = HTML::sanitize($_GET['dto']);
    } else {
      $this->_date_to = '';
    }

    return $this->_date_to;
  }

  /**
   * Checks if there is a valid "date to" value.
   *
   * @return bool Returns true if the "date to" value is valid, otherwise false.
   */
  public function hasDateTo(): bool
  {
    $dtoDateTime = new DateTime($this->getDateTo(), false);

    if ($dtoDateTime->isValid() === false) {
      $dateto = false;
    } else {
      $dateto = true;
    }

    return $dateto;
  }

  /**
   *
   * @param string $timestamp The timestamp to set as the starting date.
   * @return string The set starting date.
   */
  public function setDateFrom(string $timestamp): string
  {
    $this->_date_from = $timestamp;
  }

  /**
   * Sets the date to the specified timestamp.
   *
   * @param int|string $timestamp The timestamp to set as the date.
   * @return void
   */
  public function setDateTo($timestamp)
  {
    $this->_date_to = $timestamp;
  }

  /**
   * Retrieves the "price from" value from either the POST or GET request.
   * Sanitizes the input if it is set, not empty, and numeric. If not available, returns an empty string.
   *
   * @return string The sanitized "price from" value or an empty string if not set or invalid.
   */
  public function getPriceFrom(): string
  {
    if (isset($_POST['pfrom']) && !empty($_POST['pfrom']) && is_numeric($_POST['pfrom'])) {
      $this->_price_from = HTML::sanitize((float)$_POST['pfrom']);
    } elseif (isset($_GET['pfrom']) && !empty($_GET['pfrom']) && is_numeric($_GET['pfrom'])) {
      $this->_price_from = HTML::sanitize((float)$_GET['pfrom']);
    } else {
      $this->_price_from = '';
    }

    return $this->_price_from;
  }

  /**
   * Determines whether a price is set for the entity.
   *
   * @return bool Returns true if a price is set, otherwise false.
   */
  public function hasPriceFrom(): bool
  {
    if (empty($this->getPriceFrom())) {
      $pricefrom = false;
    } else {
      $pricefrom = true;
    }

    return $pricefrom;
  }

  /**
   * Retrieves the maximum price value from request parameters.
   *
   * @return string The sanitized maximum price value or an empty string if no valid value is found.
   */
  public function getPriceTo(): string
  {
    if (isset($_POST['pto']) && !empty($_POST['pto']) && is_numeric($_POST['pto'])) {
      $this->_price_to = HTML::sanitize((float)$_POST['pto']);
    } elseif (isset($_GET['pto']) && !empty($_POST['pto']) && is_numeric($_GET['pto'])) {
      $this->_price_to = HTML::sanitize((float)$_GET['pto']);
    } else {
      $this->_price_to = '';
    }

    return $this->_price_to;
  }

  /**
   * Determines if there is a price for the "to" field.
   *
   * @return bool True if the "to" price is set, otherwise false.
   */
  public function hasPriceTo(): bool
  {
    if (empty($this->getPriceTo())) {
      $priceto = false;
    } else {
      $priceto = true;
    }

    return $priceto;
  }

  /**
   * Retrieves the total number of results.
   *
   * @return int The total number of results.
   */
  public function getNumberOfResults(): int
  {
    return $this->_result['total'];
  }

  /**
   * Retrieves and sanitizes the 'keywords' parameter from either POST or GET request.
   *
   * @return string The sanitized keywords or an empty string if not present in the request.
   */
  public function getKeywords(): string
  {
    if (isset($_POST['keywords'])) {
      $this->_keywords = HTML::sanitize($_POST['keywords']);
    } elseif (isset($_GET['keywords'])) {
      $this->_keywords = HTML::sanitize($_GET['keywords']);
    } else {
      $this->_keywords = '';
    }

    return $this->_keywords;
  }


  /**
   * Determines if keywords are present.
   *
   * @return bool Returns true if there are keywords, false otherwise.
   */
  public function hasKeywords(): bool
  {
    if (!empty($this->getKeywords())) {
      return true;
    } else {
      return false;
    }
  }

  /*
   * explode keywords
   * @param $keywords, keywords
   * @return keywords under an array
   *
  */
  /**
   * Sets and sanitizes the input keywords, limiting the processed terms to a maximum of five unique words.
   *
   * @param string $keywords The input keywords string to be sanitized and processed.
   * @return void
   */
  public function setKeywords(string $keywords)
  {
    if (isset($keywords)) {
      $this->_keywords = HTML::sanitize($keywords);
    }

    $terms = explode(' ', trim($keywords));
    $terms_array = [];

    $counter = 0;

    foreach ($terms as $word) {
      $counter++;

      if ($counter > 5) {
        break;
      } elseif (!empty($word)) {
        if (!in_array($word, $terms_array, true)) {
          $terms_array[] = $word;
        }
      }
    }

    $this->_keywords = implode(' ', $terms_array);
  }

  /*
   * Search in description
   * @param
   * @return $this->_description, the keywords
   *
  */
  /**
   * Determines whether to search within descriptions based on POST or GET values.
   *
   * @return bool Returns true if searching in descriptions is enabled; otherwise, false.
   */
  private function getDescription(): bool
  {
    if (isset($_POST['search_in_description']) == 1) {
      $this->_description = true;
    } elseif (isset($_GET['search_in_description']) == 1) {
      $this->_description = true;
    } else {
      $this->_description = false;
    }

    return $this->_description;
  }


  /**
   * Checks if a description is present.
   *
   * @return bool True if a description exists, false otherwise.
   */
  private function hasDescription(): bool
  {
    return $this->getDescription();
  }

  /*
   * Search in category
   * @param
   * @return $this->_category, the categorie
   *
  */
  /**
   * Determines and returns the category status based on input parameters.
   *
   * @return bool True if a valid categories_id is found in POST or GET data, otherwise false.
   */
  private function getCategory(): bool
  {
    if (isset($_POST['categories_id']) && !empty($_POST['categories_id']) && is_numeric($_POST['categories_id'])) {
      $this->_category = true;
    } elseif (isset($_GET['categories_id']) && !empty($_GET['categories_id']) && is_numeric($_POST['categories_id'])) {
      $this->_category = true;
    } else {
      $this->_category = false;
    }

    return $this->_category;
  }

  /**
   * Checks if a category exists.
   *
   * @return bool Returns true if a category exists, otherwise false.
   */
  private function hasCategory(): bool
  {
    return $this->getCategory();
  }

  /*
   * Category recusive
   * array id of recursive category
   * @return $this->_recursive, id fo categories
   *
  */
  /**
   * Determines if the operation should be recursive based on a specific POST parameter.
   *
   * @return bool Whether the operation is recursive.
   */
  private function isRecursive(): bool
  {
    if (isset($_POST['inc_subcat']) && ($_POST['inc_subcat'] == '1')) {
      $this->_recursive = true;
    } elseif (isset($_POST['inc_subcat']) && ($_POST['inc_subcat'] == '1')) {
      $this->_recursive = true;
    } else {
      $this->_recursive = false;
    }
    return $this->_recursive;
  }

  /**
   * Retrieves the category ID from the request parameters if available.
   *
   * @return int|null The sanitized category ID if present, null otherwise.
   */
  private function getCategoryID():  int|null
  {
    if (isset($_POST['categories_id']) && !empty($_POST['categories_id'])) {
      $category_id = HTML::sanitize($_POST['categories_id']);
    } elseif (isset($_GET['categories_id']) && !empty($_GET['categories_id'])) {
      $category_id = HTML::sanitize($_GET['categories_id']);
    }
    return $category_id;
  }

  /*
  * Search in manufacturer
  * @param
  * @return $this->_manufacturer, the manufacturer
  *
  */
  /**
   * Determines the manufacturer based on provided POST or GET data.
   *
   * @return bool Returns true if a valid manufacturer ID is found and set, otherwise false.
   */
  private function getManufacturer(): bool
  {
    $this->checkManufacturer = false;

    if (isset($_POST['manufacturersId']) && !empty($_POST['manufacturersId']) && is_numeric($_POST['manufacturersId'])) {
      $this->_manufacturer = HTML::sanitize($_POST['manufacturersId']);
      $this->checkManufacturer = true;
    } elseif (isset($_GET['manufacturersId']) && !empty($_GET['manufacturersId']) && is_numeric($_GET['manufacturersId'])) {
      $this->_manufacturer = HTML::sanitize($_POST['manufacturersId']);
      $this->checkManufacturer = true;
    }

    return $this->_manufacturer;
  }

  /*
   * manufacturer
   * Boolean true False
   * @return true or False
   * @access private
  */
  /**
   *
   * @return bool|null Indicates whether a manufacturer is present or null if not determined.
   */
  private function hasManufacturer(): ?bool
  {
    return $this->checkManufacturer;
  }

  /*
  * Sort order list
  * String
  * @return array $define_list, sort order type
  *
  */

  /**
   * Sorts and filters the search list configuration settings based on their defined values.
   *
   * @return array Sorted and filtered array of column identifiers for the search list.
   */
  public function sortListSearch(): array
  {
    if (defined('MODULE_PRODUCTS_SEARCH_LIST_NAME')) {
      $define_list = [
        'MODULE_PRODUCTS_SEARCH_LIST_NAME' => MODULE_PRODUCTS_SEARCH_LIST_NAME,
        'MODULE_PRODUCTS_SEARCH_LIST_MODEL' => MODULE_PRODUCTS_SEARCH_LIST_MODEL,
        'MODULE_PRODUCTS_SEARCH_LIST_MANUFACTURER' => MODULE_PRODUCTS_SEARCH_LIST_MANUFACTURER,
        'MODULE_PRODUCTS_SEARCH_LIST_PRICE' => MODULE_PRODUCTS_SEARCH_LIST_PRICE,
        'MODULE_PRODUCTS_SEARCH_LIST_QUANTITY' => MODULE_PRODUCTS_SEARCH_LIST_QUANTITY,
        'MODULE_PRODUCTS_SEARCH_LIST_WEIGHT' => MODULE_PRODUCTS_SEARCH_LIST_WEIGHT,
        'MODULE_PRODUCTS_SEARCH_LIST_DATE_ADDED' => MODULE_PRODUCTS_SEARCH_LIST_DATE_ADDED
      ];

      asort($define_list);

      $column_list = [];

      foreach ($define_list as $key => $value) {
        if ($value > 0) $column_list[] = $key;
      }

      return $column_list;
    }
  }

  /*
   * Execute
   *
   * @return $result : sql sesult
   *
  */
  /**
   * Executes a comprehensive product search based on various criteria such as price, category, manufacturer, and keywords.
   * The search conditions are dynamically constructed to filter results from the product database.
   *
   * @return array An array of search results based on the specified filters and sorting criteria.
   */
  public function execute()
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_CategoryTree = Registry::get('CategoryTree');
    $CLICSHOPPING_Currencies = Registry::get('Currencies');
    $CLICSHOPPING_Language = Registry::get('Language');

    $dtoDateTime = new DateTime($this->getDateTo(), false);
    $dfromDateTime = new DateTime($this->getDateFrom(), false);

    $dtoDateTime1 = $this->getDateTo();
    $dfromDateTime1 = $this->getDateFrom();

    if (defined('MODULE_PRODUCTS_SEARCH_MAX_DISPLAY')) {
      $max_display = MODULE_PRODUCTS_SEARCH_MAX_DISPLAY;
    } else {
      $max_display = 1;
    }

    $result = [];

    if ($this->hasPriceFrom()) {
      if ($CLICSHOPPING_Currencies->getValue($_SESSION['currency'])) {
        $this->_price_from /= $CLICSHOPPING_Currencies->getValue($_SESSION['currency']);
      }
    }

    if ($this->hasPriceTo()) {
      if ($CLICSHOPPING_Currencies->getValue($_SESSION['currency'])) {
        $this->_price_to /= $CLICSHOPPING_Currencies->getValue($_SESSION['currency']);
      }
    }

    $listing_sql = 'select SQL_CALC_FOUND_ROWS ';

    $listing_sql .= ' p.*,
                        pd.*,
                        m.*
                       ';
    /*
                            if(s.status, s.specials_new_products_price, null) as specials_new_products_price,
                            if(s.status, s.specials_new_products_price, p.products_price) as final_price
    ';
    */
    if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
      $listing_sql .= ', g.*';
      $listing_sql .= ' from :table_products p';
      $listing_sql .= ' left join :table_products_groups g on p.products_id = g.products_id';
      $listing_sql .= ' left join :table_specials s on p.products_id = s.products_id ';
    } else {
      $listing_sql .= ' from :table_products p';
      $listing_sql .= ' left join :table_specials s on p.products_id = s.products_id ';
    }

    $listing_sql .= ' left join :table_manufacturers m using(manufacturers_id) ';


    if (($this->hasPriceFrom() || $this->hasPriceTo()) && (DISPLAY_PRICE_WITH_TAX == 'true')) {
      $listing_sql .= ' left join :table_tax_rates tr on p.products_tax_class_id = tr.tax_class_id';
      $listing_sql .= ' left join :table_zones_to_geo_zones gz on tr.tax_zone_id = gz.geo_zone_id
                           and (gz.zone_country_id is null
                               or gz.zone_country_id = 0
                               or gz.zone_country_id = :zone_country_id
                               )
                           and (gz.zone_id is null
                                or gz.zone_id = 0
                                or gz.zone_id = :zone_id
                                )
                           ';
    }

    $listing_sql .= ', :table_products_description pd,
                         :table_categories c,
                         :table_products_to_categories p2c
                       ';

    if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
      $listing_sql .= ' where g.products_group_view = 1 ';
      $listing_sql .= ' and g.customers_group_id = :customers_group_id ';
    } else {
      $listing_sql .= ' where p.products_view = 1 ';
    }

    $listing_sql .= ' and p.products_status = 1
                        and p.products_archive = 0
                        and c.virtual_categories = 0
                        and c.status = 1
                        and p.products_id = pd.products_id
                        and p.products_id = p2c.products_id
                        and p2c.categories_id = c.categories_id
                        and pd.language_id = :language_id
                      ';

    if ($this->hasCategory()) {
      if ($this->isRecursive()) {
        $subcategories_array = [$this->_category];

        $listing_sql .= ' and p2c.products_id = p.products_id
                             and p2c.products_id = pd.products_id
                             and p2c.categories_id in (' . implode(',', $CLICSHOPPING_CategoryTree->getChildren($this->_category, $subcategories_array)) . ')
                             and c.status = 1
                           ';
      } else {
        $listing_sql .= ' and p2c.products_id = p.products_id
                             and p2c.products_id = pd.products_id
                             and pd.language_id = :language_id_c
                             and p2c.categories_id = :categories_id
                             and c.status = 1
                          ';
      }
    }

    if ($this->hasManufacturer()) {
      $listing_sql .= ' and m.manufacturers_id = :manufacturers_id ';
    }

    if ($this->hasKeywords() === true) {
      $array = explode(' ', $this->_keywords);

      foreach ($array as $this->_keywords) {
        $listing_sql .= ' and (';
        $listing_sql .= ' pd.products_name like :products_name_keywords or
                            p.products_model like :products_model_keywords or
                            p.products_ean like :products_ean_keywords or
                            p.products_sku like :products_sku_keywords or
                            m.manufacturers_name like :manufacturers_name_keywords
                          ';

        if ($this->hasDescription() === true) {
          $listing_sql .= ' or pd.products_description like :products_description_keywords';
        }

        $listing_sql .= ') ';
      }
    }

    if (($this->hasDateFrom() === true) && isset($dfromDateTime) && $dfromDateTime->isValid()) {
      $listing_sql .= ' and p.products_date_added >= :products_date_added_from';
    }

    if (($this->hasDateTo() === true) && isset($dtoDateTime) && $dtoDateTime->isValid()) {
      $listing_sql .= ' and p.products_date_added <= :products_date_added_to';
    }

    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      if ($this->_price_from > 0) {
        $listing_sql .= ' and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= :price_from)';
      }

      if ($this->_price_to > 0) {
        $listing_sql .= ' and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= :price_to)';
      }
    } else {
      if ($this->_price_from > 0) {
        $listing_sql .= ' and (if(s.status, s.specials_new_products_price, p.products_price) >= :price_from)';
      }

      if ($this->_price_to > 0) {
        $listing_sql .= ' and (if(s.status, s.specials_new_products_price, p.products_price) <= :price_to)';
      }
    }

    $listing_sql .= ' group by p.products_id';

    $column_list = $this->sortListSearch();

    if ((!isset($_GET['sort'])) || (!preg_match('/^[1-8][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > count($column_list))) {
      if (is_array($column_list)) {
        for ($i = 0, $n = count($column_list); $i < $n; $i++) {
          if ($column_list[$i] == 'MODULE_PRODUCTS_SEARCH_LIST_DATE_ADDED') {
            $_GET['sort'] = $i + 1 . 'a';
            $listing_sql .= ' order by p.products_sort_order DESC,
                                         pd.products_name
                             ';
            break;
          }
        }
      }
    } else {

      $sort_col = substr($_GET['sort'], 0, 1);
      $sort_order = substr($_GET['sort'], 1);

      switch ($column_list[$sort_col - 1]) {
        case 'MODULE_PRODUCTS_SEARCH_LIST_DATE_ADDED':
          $listing_sql .= ' order by p.products_date_added ' . ($sort_order == 'd' ? 'desc' : ' ');
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_PRICE':
          $listing_sql .= ' order by p.products_price ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_MODEL':
          $listing_sql .= ' order by p.products_model ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_QUANTITY':
          $listing_sql .= ' order by p.products_quantity ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_WEIGHT':
          $listing_sql .= ' order by p.products_weight ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_NAME':
          $listing_sql .= ' order by pd.products_name ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_LIST_MANUFACTURER':
          $listing_sql .= ' order by m.manufacturers_name ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
          break;
        case 'MODULE_PRODUCTS_SEARCH_DATE_ADDED':
          $listing_sql .= ' order by p.products_date_added ' . ($sort_order == 'd' ? 'desc' : '') . ', pd.products_name DESC ';
          break;
      }
    }

    $listing_sql .= ' limit :page_set_offset,
                             :page_set_max_results
                      ';

    $Qlisting = $this->db->prepare($listing_sql);

    if (($this->hasPriceFrom() || $this->hasPriceTo()) && (DISPLAY_PRICE_WITH_TAX == 'true')) {
      if ($CLICSHOPPING_Customer->isLoggedOn()) {
        $customer_country_id = $CLICSHOPPING_Customer->getCountryID();
        $customer_zone_id = $CLICSHOPPING_Customer->getZoneID();
      } else {
        $customer_country_id = (int)STORE_COUNTRY;
        $customer_zone_id = (int)STORE_ZONE;
      }

      $Qlisting->bindInt(':zone_country_id', $customer_country_id);
      $Qlisting->bindInt(':zone_id', $customer_zone_id);
    }

    if ($this->hasCategory()) {
      if (!$this->isRecursive()) {
        $Qlisting->bindInt(':categories_id', $this->getCategoryID());
        $Qlisting->bindInt(':language_id_c', $CLICSHOPPING_Language->getId());
      }
    }

    if ($this->hasManufacturer()) {
      $Qlisting->bindInt(':manufacturers_id', $this->getManufacturer());
    }

    if ($this->hasKeywords()) {
      $array = explode(' ', $this->_keywords);

      foreach ($array as $keyword) {
        $Qlisting->bindValue(':products_name_keywords', '%' . $keyword . '%');
        $Qlisting->bindValue(':products_model_keywords', '%' . $keyword . '%');
        $Qlisting->bindValue(':products_sku_keywords', '%' . $keyword . '%');
        $Qlisting->bindValue(':products_ean_keywords', '%' . $keyword . '%');
        $Qlisting->bindValue(':manufacturers_name_keywords', '%' . $keyword . '%');

        if ($this->hasDescription() === true) {
          $Qlisting->bindValue(':products_description_keywords', '%' . $keyword . '%');
        }
      }
    }

    if ($this->hasDateFrom()) {
      if (isset($dfromDateTime) && $dfromDateTime->isValid()) {
        $Qlisting->bindValue(':products_date_added_from', $dfromDateTime1);
      }
    }

    if ($this->hasDateTo()) {
      if (isset($dtoDateTime) && $dtoDateTime->isValid()) {
        $Qlisting->bindValue(':products_date_added_to', $dtoDateTime1);
      }
    }

    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      if ($this->_price_from > 0) {
        $Qlisting->bindDecimal(':price_from', $this->_price_from);
      }

      if ($this->_price_to > 0) {
        $Qlisting->bindDecimal(':price_to', $this->_price_to);
      }
    } else {
      if ($this->_price_from > 0) {
        $Qlisting->bindDecimal(':price_from', $this->_price_from);
      }

      if ($this->_price_to > 0) {
        $Qlisting->bindDecimal(':price_to', $this->_price_to);
      }
    }

    $Qlisting->bindInt(':language_id', $CLICSHOPPING_Language->getId());

    if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
      $Qlisting->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
    }

    $Qlisting->setPageSet($max_display);

    $Qlisting->execute();

    $result['entries'] = $Qlisting->fetchAll();

    $result['total'] = $Qlisting->getPageSetTotalRows();

    $this->listing = $Qlisting;

    $this->_result = $result;
  }


  /**
   * Retrieves the listing.
   *
   * @return mixed The listing associated with the instance.
   */
  public function getListing()
  {
    return $this->listing;
  }

  /**
   * Retrieves the result of an operation.
   * If the result is not already set, it will execute the operation to generate it.
   *
   * @return mixed The result of the operation.
   */
  public function getResult()
  {
    if (!isset($this->_result)) {
      $this->execute();
    }

    return $this->_result;
  }
}