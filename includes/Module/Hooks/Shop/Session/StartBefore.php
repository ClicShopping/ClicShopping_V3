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

  namespace ClicShopping\OM\Module\Hooks\Shop\Session;

  use ClicShopping\OM\CLICSHOPPING;

  class StartBefore
  {
    public function execute($parameters)
    {
      if (SESSION_BLOCK_SPIDERS == 'True') {
        $user_agent = '';

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
          $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        }

        if (!empty($user_agent)) {
          $file_array = file(CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Assets/spiders.txt');

          if (\is_array($file_array)) {
            foreach ($file_array as $spider) {
              if ((substr($spider, \strlen($spider) - 1, 1) == ' ') || (substr($spider, \strlen($spider) - 1, 1) == "\n")) {
                $spider = substr($spider, 0, \strlen($spider) - 1);
              }

              if (!empty($spider)) {
                if (strpos($user_agent, $spider) !== false) {
                  $parameters['can_start'] = false;
                  break;
                }
              }
            }
          }
        }
      }
    }
  }
