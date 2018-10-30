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


  namespace ClicShopping\OM\Module\Hooks\Shop\Session;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Hash;

  class StartAfter {
    public function execute() {

      $CLICSHOPPING_Session = Registry::get('Session');

// initialize a session token
      if (!isset($_SESSION['sessiontoken'])) {
          $_SESSION['sessiontoken'] = md5(Hash::getRandomInt() . Hash::getRandomInt() . Hash::getRandomInt() .Hash::getRandomInt());
      }

// verify the ssl_session_id if the feature is enabled
      if ((HTTP::getRequestType() === 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && $CLICSHOPPING_Session->hasStarted()) {
      if (!isset($_SESSION['SSL_SESSION_ID'])) {
          $_SESSION['SESSION_SSL_ID'] = $_SERVER['SSL_SESSION_ID'];
      }

      if ($_SESSION['SESSION_SSL_ID'] != $_SERVER['SSL_SESSION_ID']) {
        $CLICSHOPPING_Session->kill();

        CLICSHOPPING::redirect('index.php', 'Info&SSLcheck');
      }
    }

// verify the browser user agent if the feature is enabled
      if (SESSION_CHECK_USER_AGENT == 'True') {
        if (!isset($_SESSION['SESSION_USER_AGENT'])) {
          $_SESSION['SESSION_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        }

        if ($_SESSION['SESSION_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
          $CLICSHOPPING_Session->kill();

          CLICSHOPPING::redirect('index.php', 'Account&Login');
        }
    }

// verify the IP address if the feature is enabled
      if (SESSION_CHECK_IP_ADDRESS == 'True') {
        if (!isset($_SESSION['SESSION_IP_ADDRESS'])) {
          $_SESSION['SESSION_IP_ADDRESS'] =  HTTP::GetIpAddress();
        }

        if ($_SESSION['SESSION_IP_ADDRESS'] != HTTP::GetIpAddress()) {
          $CLICSHOPPING_Session->kill();

          CLICSHOPPING::redirect('index.php', 'Account&Login');
        }
      }
    }
  }
