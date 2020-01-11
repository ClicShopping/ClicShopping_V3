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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  class NavigationHistory
  {

    public $path = [];
    public $snapshot = [];

    public function __construct(bool $add_current_page = false)
    {
      if (isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data']) && is_array($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data']) && !empty($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data'])) {
        $this->path =& $_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data'];
      }

      if (isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot']) && is_array($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot']) && !empty($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot'])) {
        $this->snapshot =& $_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot'];
      }

      if ($add_current_page === true) {
        $this->addCurrentPage();
      }
    }

    /**
     *
     */
    public function addCurrentPage()
    {
      $action_counter = 0;
      $application_key = null;
      $action = [];

      foreach ($_GET as $key => $value) {
        if (!isset($application_key) && ($key == CLICSHOPPING::getSiteApplication())) {
          $application_key = $action_counter;

          $action_counter++;

          continue;
        }

        $action[] = [$key => $value];

        if ($this->siteApplicationActionExists(implode('\\', array_keys($action))) === false) {
          array_pop($action);

          break;
        }

        $action_counter++;
      }

      $action_get = http_build_query($action);

      if (is_array($this->path)) {
        for ($i = 0, $n = count($this->path); $i < $n; $i++) {
          if (($this->path[$i]['application'] == CLICSHOPPING::getSiteApplication()) && ($this->path[$i]['action'] == $action_get)) {
            array_splice($this->path, $i);
            break;
          }
        }
      }

      $this->path[] = ['application' => CLICSHOPPING::getSiteApplication(),
        'action' => $action_get,
        'mode' => HTTP::getRequestType(),
        'get' => array_slice($_GET, $action_counter),
        'post' => $_POST
      ];

      if (!isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data'])) {
        $_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data'] = $this->path;
      }
    }

    /**
     *
     */
    public function removeCurrentPage()
    {
      array_pop($this->path);

      if (empty($this->path)) {
        $this->resetPath();
      }
    }

    /**
     * @param int $back
     * @return bool
     */
    public function hasPath($back = 1)
    {
      if ((is_numeric($back) === false) || (is_numeric($back) && ($back < 1))) {
        $back = 1;
      }

      return isset($this->path[count($this->path) - $back]);
    }

    /**
     * @param int $back
     * @param array $exclude
     * @return string
     */
    public function getPathURL($back = 1)
    {
      if ((is_numeric($back) === false) || (is_numeric($back) && ($back < 1))) {
        $back = 1;
      }

      $back = count($this->path) - $back;

      return CLICSHOPPING::link(null, $this->path[$back]['application'] . '&' . $this->path[$back]['action'] . '&' . $this->parseParameters($this->path[$back]['get']));
    }

    public function setSnapshot($page = null)
    {
      if (isset($page) && is_array($page)) {
        $this->snapshot = ['application' => $page['application'],
          'action' => $page['action'],
          'mode' => $page['mode'],
          'get' => $page['get'],
          'post' => $page['post']
        ];
      } else {
        $this->snapshot = $this->path[count($this->path) - 1];
      }

      if (!isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot'])) {
        $_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot'] = $this->snapshot;
      }
    }

    /**
     * @return bool
     */
    public function hasSnapshot()
    {
      return !empty($this->snapshot);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getSnapshot($key)
    {
      if (isset($this->snapshot[$key])) {
        return $this->snapshot[$key];
      }
    }

    /**
     * @return string
     */
    public function getSnapshotURL()
    {
      if ($this->hasSnapshot()) {
        $target = CLICSHOPPING::redirect(null, $this->snapshot['application'] . '&' . $this->snapshot['action'] . '&' . $this->parseParameters($this->snapshot['get']));
      } else {
        $target = CLICSHOPPING::redirect();
      }

      return $target;
    }

    /**
     * @return string
     */
    public function redirectToSnapshot()
    {
      $target = $this->getSnapshotURL();

      $this->resetSnapshot();

      return $target;
    }

    public function resetPath()
    {
      $this->path = [];

      if (isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data'])) {
        unset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['data']);
      }
    }

    public function resetSnapshot()
    {
      $this->snapshot = [];

      if (isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot'])) {
        unset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']['snapshot']);
      }
    }


    public function reset()
    {
      $this->resetPath();
      $this->resetSnapshot();

      if (isset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory'])) {
        unset($_SESSION[CLICSHOPPING::getSite()]['NavigationHistory']);
      }
    }

    /**
     * @param $array
     * @param array $additional_exclude
     * @return bool|string
     */
    protected function parseParameters($array, $additional_exclude = array())
    {
      $exclude = array('x', 'y', Registry::get('Session')->getName());

      if (is_array($additional_exclude) && !empty($additional_exclude)) {
        $exclude = array_merge($exclude, $additional_exclude);
      }

      $string = '';

      if (is_array($array) && !empty($array)) {
        foreach ($array as $key => $value) {
          if (!in_array($key, $exclude)) {
            $string .= $key . '=' . $value . '&';
          }
        }

        $string = substr($string, 0, -1);
      }

      return $string;
    }

    /**
     * @param string $action
     * @return string
     */
    protected function siteApplicationActionExists($action)
    {
      return class_exists('ClicShopping\\OM\\Site\\Shop\\Pages\\' . CLICSHOPPING::getSiteApplication() . '\\Actions\\' . $action);
    }
  }