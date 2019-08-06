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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  class LoggerAdmin
  {
    public $timerStart;
    public $timerStop;
    public $timer_total;

// class constructor
    public function __construct()
    {
      $this->timerStart();
    }

    public function timerStart()
    {
      if (defined("PAGE_PARSE_START_TIME")) {
        $this->timerStart = PAGE_PARSE_START_TIME;
      } else {
        $this->timerStart = microtime();
      }
    }

    public function timerStop($display = 'false')
    {
      $this->timerStop = microtime();

      $time_start = explode(' ', $this->timerStart);
      $time_end = explode(' ', $this->timerStop);

      $this->timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      $this->write($_SERVER['REQUEST_URI'], $this->timer_total . 's');

      if ($display == 'true') {
        return $this->timerDisplay();
      }
    }

    public function timerDisplay()
    {
      return '<span>Parse Time: ' . $this->timer_total . 's</span>';
    }

    public static function write($message, $type)
    {
      if ( STORE_PAGE_PARSE_TIME == 'True') {
        if (is_file(STORE_PAGE_PARSE_TIME_LOG)) {
          error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' [' . $type . '] ' . $message . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
        } else {
          echo '<div class="alert alert-warning text-md-center">The time log directory or file is not found</div>';
        }
      }
    }
  }

