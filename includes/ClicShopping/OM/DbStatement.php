<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use PDO;
use function in_array;
use function is_array;
use function is_null;

/**
 * Class DbStatement
 * Extends the PDOStatement class to provide additional features for database operations,
 * including custom data binding, pagination, caching, and result handling.
 */
class DbStatement extends \PDOStatement
{
  protected $pdo;
  protected bool $is_error = false;
  protected string $page_set_keyword = 'page';
  protected mixed $page_set_results_per_page;
  protected $cache;
  protected int $cache_expire;
  protected $cache_data;
  protected bool $cache_read = false;
  protected bool $cache_empty_results = false;
  protected string $query_call;
  protected int|null $page_set_total_rows;
  protected $result;
  public ?string $page_set;

  /**
   *
   */
  public function bindValue(string|int $parameter, mixed $value, int $data_type = PDO::PARAM_STR): bool
  {
    return parent::bindValue($parameter, $value, $data_type);
  }

  /**
   * Binds an integer value to a parameter for use in a prepared statement.
   *
   * @param string|int $parameter The parameter identifier to bind the value to.
   * @param string|int
   */
// force type to int (see http://bugs.php.net/bug.php?id=44639)
  public function bindInt(string|int $parameter, string|int|null $value): bool
  {
    return $this->bindValue($parameter, (int)$value, PDO::PARAM_INT);
  }

  /**
   *
   */
// force type to bool (see http://bugs.php.net/bug.php?id=44639)
  public function bindBool(string|int $parameter, bool $value): bool
  {
    return $this->bindValue($parameter, (bool)$value, PDO::PARAM_BOOL);
  }

  /**
   * Binds a decimal value to
   */
  public function bindDecimal(string|int $parameter, float $value): bool
  {
    return $this->bindValue($parameter, (float)$value); // there is no \PDO::PARAM_FLOAT
  }

  /**
   *
   */
  public function bindNull(string|int $parameter): bool
  {
    return $this->bindValue($parameter, null, PDO::PARAM_NULL);
  }

  /**
   * Sets up pagination properties and binds relevant placeholders for offset and maximum results.
   *
   * @param int $max_results The maximum number of results per page.
   */
  public function setPageSet($max_results, $page_set_keyword = null, string $placeholder_offset = 'page_set_offset', string $placeholder_max_results = 'page_set_max_results')
  {
    if (!empty($page_set_keyword)) {
      $this->page_set_keyword = $page_set_keyword;
    }

    $this->page_set = (isset($_GET[$this->page_set_keyword]) && is_numeric($_GET[$this->page_set_keyword]) && ($_GET[$this->page_set_keyword] > 0)) ? $_GET[$this->page_set_keyword] : 1;
    $this->page_set_results_per_page = $max_results;

    $offset = max(($this->page_set * $max_results) - $max_results, 0);

    $this->bindInt(':' . $placeholder_offset, $offset);
    $this->bindInt(':' . $placeholder_max_results, $max_results);
  }

  /**
   * Executes the query with the provided input parameters, handles caching,
   * and processes pagination-related operations if applicable.
   *
   * @param array|null
   */
  public function execute(array|null $input_parameters = null): bool
  {
    if ($this->cache) {
      if (isset($this->page_set)) {
        $this->cache->setKey($this->cache->getKey() . '-pageset' . $this->page_set);
      }

      if ($this->cache->exists($this->cache_expire)) {
        $this->cache_data = $this->cache->get();

        if (isset($this->cache_data['data']) && isset($this->cache_data['total'])) {
          $this->page_set_total_rows = $this->cache_data['total'];
          $this->cache_data = $this->cache_data['data'];
        }

        $this->cache_read = true;
      } else {
        $this->cache_read = false;
      }
    }

    if ($this->cache_read === false) {
      if (empty($input_parameters)) {
        $input_parameters = null;
      }

      $this->is_error = !parent::execute($input_parameters);

      if ($this->is_error === true) {
        trigger_error($this->queryString);
      }

      if (str_contains($this->queryString, ' SQL_CALC_FOUND_ROWS ')) {
        $this->page_set_total_rows = $this->pdo->query('select found_rows()')->fetchColumn();
      } elseif (isset($this->page_set)) {
        trigger_error('ClicShopping\OM\DbStatement::execute(): Page Set query does not contain SQL_CALC_FOUND_ROWS. Please add it to the query: ' . $this->queryString);
      }
    }

    return false;
  }

  /**
   * Fetches the next row from the result set.
   *
   * @param int $fetch_style The fetch style for the resulting row. Defaults
   */
  public function fetch(
    int $fetch_style = PDO::FETCH_DEFAULT, //FETCH_ASSOC,
    int $cursor_orientation = PDO::FETCH_ORI_NEXT,
    int $cursor_offset = 0): bool|array
  {
    if ($this->cache_read === true) {
      $this->result = current($this->cache_data);
    } else {
      $this->result = parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);

      if (isset($this->cache) && ($this->result !== false)) {
        if (!isset($this->cache_data)) {
          $this->cache_data = [];
        }

        $this->cache_data[] = $this->result;
      }
    }

    return $this->result;
  }

  /**
   * Fetches all rows from the
   */
  public function fetchAll(int|null $fetch_style = PDO::FETCH_BOTH, mixed ...$args): array
  {
    if ($this->cache_read === true) {
      $this->result = $this->cache_data;
    } else {
// fetchAll() fails if second argument is passed in a fetch style that does not
// use the optional argument
      if (in_array($fetch_style, [PDO::FETCH_COLUMN, PDO::FETCH_CLASS, PDO::FETCH_FUNC])) {
        $this->result = parent::fetchAll($fetch_style, $fetch_argument, $ctor_args);
      } else {
        $this->result = parent::fetchAll($fetch_style);
      }

      if (isset($this->cache) && ($this->result !== false)) {
        $this->cache_data = $this->result;
      }
    }

    return $this->result;
  }

  /**
   *
   */
  public function check()
  {
    if (!isset($this->result)) {
      $this->fetch();
    }

    return $this->result !== false;
  }

  /**
   *
   */
  public function toArray()
  {
    if (!isset($this->result)) {
      $this->fetch();
    }

    return $this->result;
  }

  /**
   * Sets caching parameters for the current database query.
   *
   * @param string $key The unique key to reference the cache.
   * @param int
   */
  public function setCache(string $key, int|null $expire = null, bool $cache_empty_results = false)
  {
    if (!is_numeric($expire)) {
      $expire = 0;
    }

    if (!is_bool($cache_empty_results)) {
      $cache_empty_results = false;
    }

    $this->cache = new Cache($key);
    $this->cache_expire = $expire;
    $this->cache_empty_results = $cache_empty_results;

    if ($this->query_call != 'prepare') {
      trigger_error('ClicShopping\\OM\\DbStatement::setCache(): Cannot set cache (\'' . $key . '\') on a non-prepare query. Please change the query to a prepare() query.');
    }
  }

  /**
   *
   */
  protected function valueMixed(string $column, string $type = 'string')
  {
    if (!isset($this->result)) {
      $this->fetch();
    }

    switch ($type) {
      case 'protected':
        if (isset($this->result[$column])) {
          return HTML::outputProtected($this->result[$column]);
        }
        break;

      case 'int':
        if (isset($this->result[$column])) {
          return (int)$this->result[$column];
        }
        break;

      case 'decimal':
        if (isset($this->result[$column])) {
          return (float)$this->result[$column];
        }
        break;

      case 'string':
      default:
        if (isset($this->result[$column])) {
          return $this->result[$column];
        }
    }

    return false;
  }

  /**
   * Retrieves the value of a specified column as a string.
   *
   * @param string $column The name of the column to retrieve the value from.
   * @return
   */
  public function value(string $column): string
  {
    return $this->valueMixed($column, 'string');
  }

  /**
   * Retrieves a protected value for the given column.
   *
   * @param string $column The name of the column to retrieve the protected value for.
   * 
   */
  public function valueProtected(string $column): string
  {
    return $this->valueMixed($column, 'protected');
  }

  /**
   * Retrieves the value of the specified column cast as an integer.
   *
   * @param string $column The name of the column from which to retrieve the
   */
  public function valueInt(string $column): int
  {
    return $this->valueMixed($column, 'int');
  }

  /**
   *
   */
  public function valueDecimal(string $column): float
  {
    return $this->valueMixed($column, 'decimal');
  }

  /**
   * Checks if a specified column has a value in the result set.
   *
   * @param string $column The name of the column to check.
   **/
  public function hasValue(string $column): bool
  {
    if (!isset($this->result)) {
      $this->fetch();
    }

    return isset($this->result[$column]);
  }

  /**
   * Checks whether the current instance represents an error state.
   *
   * @return bool True if the instance is in
   */
  public function isError(): bool
  {
    return $this->is_error;
  }

  /**
   * Retrieves the query string.
   *
   * @return string The query string.
   */
  public function getQuery(): string
  {
    return $this->queryString;
  }

  /**
   *
   */
  public function setQueryCall(string $type)
  {
    $this->query_call = $type;
  }

  /**
   * Retrieves the stored query call string.
   *
   * @return string The query call string.
   */
  public function getQueryCall(): string
  {
    return $this->query_call;
  }

  /**
   *
   */
  public function getCurrentPageSet()
  {
    return $this->page_set;
  }

  /**
   * Retrieves the number of results displayed per page in the current page set.
   *
   * @return int The number of results per page.
   */
  public function getPageSetResultsPerPage()
  {
    return $this->page_set_results_per_page;
  }

  /**
   * Retrieves the total number of rows for the current page set.
   *
   * @return int The total number of rows if set, otherwise 0.
   */
  public function getPageSetTotalRows()
  {
    if (isset($this->page_set_total_rows)) {
      return $this->page_set_total_rows;
    } else {
      return 0;
    }
  }

  /**
   * Sets the PDO instance to be
   */
  public function setPDO(PDO $instance)
  {
    $this->pdo = $instance;
  }

  /**
   * Generates a label for the current page set
   */
  public function getPageSetLabel(string $text): string
  {
    if ($this->page_set_total_rows < 1) {
      $from = 0;
    } else {
      $from = max(($this->page_set * $this->page_set_results_per_page) - $this->page_set_results_per_page, 1);
    }

    $to = min($this->page_set * $this->page_set_results_per_page, $this->page_set_total_rows);

    if ($to > $this->page_set_results_per_page) {
      $from++;
    }

//return '<span class="pagination">
    return '<span class="pageSetTotalListing">' . Language::parseDefinition($text, [
          'listing_from' => $from,
          'listing_to' => $to,
          'listing_total' => $this->page_set_total_rows
        ]
      ) . '</span>';
  }

  /**
   *
   */
  public function getPageSetLinks($parameters = null, $site = null): string
  {
    $number_of_pages = ceil($this->page_set_total_rows / $this->page_set_results_per_page);

    if (empty($parameters)) {
      $parameters = '';
    }

    if (!empty($parameters)) {
      parse_str($parameters, $p);

      if (isset($p[$this->page_set_keyword])) {
        unset($p[$this->page_set_keyword]);
      }

      // Manually build the query string
      $queryString = '';
      foreach ($p as $key => $value) {
        if ($value !== '') {
          $queryString .= $key . '=' . urlencode($value) . '&';
        } else {
          $queryString .= $key . '&';
        }
      }

      // Append the page parameter if it exists
      $parameters = !empty($queryString) ? '' . $queryString : '';
    }

    $pages = [];

    for ($i = 1; $i <= $number_of_pages; $i++) {
      $pages[] = [
        'id' => $i,
        'text' => $i
      ];
    }

    $output = '<nav aria-label="pagination">';
    $output .= '<ul class="pagination pagination-sm">';

    if (is_null($site)) {
//admin
      if ($number_of_pages > 1) {
        $output .= '<li class="page-item">' . HTML::selectField('pageset' . $this->page_set_keyword, $pages, $this->page_set, 'style="vertical-align: top; display: inline-block; float-start;" data-pageseturl="' . HTML::output(CLICSHOPPING::link(null, 'A&' . $parameters . $this->page_set_keyword . '=PAGESETGOTO')) . '"') . '</li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-center page-link sr-only">1</a></li>';
      }

// previous button
      if ($this->page_set > 1) {
        $output .= '<li class="page-item active">' . HTML::link(CLICSHOPPING::link(null, $parameters . $this->page_set_keyword . '=' . ($this->page_set - 1)), null, 'title="' . CLICSHOPPING::getDef('prevnext_title_previous_page') . '" class="text-center page-link bi bi-chevron-left"') . '</li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-center page-link bi bi-chevron-left"></a></li>';
      }

	// next button
      if (($this->page_set < $number_of_pages) && ($number_of_pages != 1)) {
        $output .= '<li class="page-item active">' . HTML::link(CLICSHOPPING::link(null, $parameters . $this->page_set_keyword . '=' . ($this->page_set + 1)), null, 'title="' . CLICSHOPPING::getDef('prevnext_title_next_page') . '" class="text-center page-link bi bi-chevron-right"') . '</li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-m-center page-link bi bi-chevron-right"></a></li>';
      }
    } else {
      if ($number_of_pages > 1) {
        $output .= '<li class="page-item">' . HTML::selectField('pageset' . $this->page_set_keyword, $pages, $this->page_set, 'style="vertical-align: top; display: inline-block; float-start; height: 32px; width: 80px;" data-pageseturl="' . HTML::output(CLICSHOPPING::link(null, $parameters . $this->page_set_keyword . '=PAGESETGOTO')) . '"') . '</li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-center page-link sr-only">1</a></li>';
      }

// previous button
      if ($this->page_set > 1) {
        $output .= '<li class="page-item active"><a href="' . CLICSHOPPING::link(null, $parameters . $this->page_set_keyword . '=' . ($this->page_set - 1)) . '" title="' . CLICSHOPPING::getDef('prevnext_title_previous_page') . '" class="text-center page-link  bi bi-chevron-left"</a></li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-center page-link bi bi-chevron-left"></a></li>';
      }
      
// next button
      if (($this->page_set < $number_of_pages) && ($number_of_pages != 1)) {
        $output .= '<li class="page-item active"><a href="' . CLICSHOPPING::link(null, $parameters . $this->page_set_keyword . '=' . ($this->page_set + 1)) . '" title="' . CLICSHOPPING::getDef('prevnext_title_next_page') . '" class="text-center page-link bi bi-chevron-right"></a></li>';
      } else {
        $output .= '<li class="page-item disabled"><a class="text-center page-link bi bi-chevron-right"></a></li>';
      }
    }

    $output .= '</ul>';
    $output .= '</nav>';

    if ($number_of_pages > 1) {
      $output .= <<<EOD
<script>
$(function() {
  $('select[name="pageset{$this->page_set_keyword}"]').on('change', function() {
    window.location = $(this).data('pageseturl').replace('PAGESETGOTO', $(this).children(':selected').val());
  });
});
</script>
EOD;
    }

    return $output;
  }

  /**
   * Destructor method responsible for handling cache operations.
   *
   * @return void
   */
  public function __destruct()
  {
    if (($this->cache_read === false) && isset($this->cache) && is_array($this->cache_data)) {
      if ($this->cache_empty_results || (isset($this->cache_data[0]) && ($this->cache_data[0] !== false))) {
        $cache_data = $this->cache_data;

        if (isset($this->page_set_total_rows)) {
          $cache_data = [
            'data' => $cache_data,
            'total' => $this->page_set_total_rows
          ];
        }

        $this->cache->save($cache_data);
      }
    }
  }
}
