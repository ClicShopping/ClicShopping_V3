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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Login;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Is;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  class ErrorProcess
  {
    public function execute()
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');
// send an email if someone try to connect on admin panel without authorization
// get ip and infos
      if (SEND_EMAILS == 'true' && CONFIGURATION_EMAIL_SECURITY == 'true') {
        if (isset($_SESSION['redirect_origin']) && isset($_SESSION['redirect_origin']['auth_user']) && !isset($_POST['username'])) {
          $username = HTML::sanitize($_SESSION['redirect_origin']['auth_user']);
        } else {
          $username = HTML::sanitize($_POST['username']);
        }

        $ip = HTTP::getIpAddress();

        if (Is::IpAddress($ip)) {
          $host = @gethostbyaddr($ip);
          $referer = $_SERVER['HTTP_REFERER'];
          $details = file_get_contents("https://ipinfo.io/{$ip}/geo");

          if ($details !== false) {
            $details = json_decode($details);

            $country = $details->country;
            $city = $details->city;
            $region =$details->region;
            $localisation = $details->loc;
            $google_map = CLICSHOPPING::getDef('report_sender_ip_address', ['IP' => $ip]) . ' : https://www.google.com/maps/place/' . $localisation;
            $new_info_ip = CLICSHOPPING::getDef('report_sender_ip_address', ['IP' => $ip]) . ' <a href="https://whatismyipaddress.com/ip/' . $ip . '">https://whatismyipaddress.com/ip/' . $ip . '</a>';

// build report
            $report = date("D M j G:i:s Y") . "\n\n";
            $report .= CLICSHOPPING::getDef('report_access_login', ['IP' => $ip]);
            $report .= "\n" . CLICSHOPPING::getDef('report_sender_host_name', ['HOST' => $host]);
            $report .= "\n" . CLICSHOPPING::getDef('report_sender_username', ['USERNAME' => $username]);
            $report .= "\n" .'City : '. $city;
            $report .= "\n" .'Country : '. $country;
            $report .= "\n" .'Region : '. $region;
            $report .= "\n" .'Referer : '. $referer;
            $report .= "\n\n" . $google_map;
            $report .= "\n" . $new_info_ip;
            $report .= "\n" . CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin');
            $report .= "\n\n" . TemplateEmailAdmin::getTemplateEmailTextFooter();

            $CLICSHOPPING_Mail->clicMail(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER_EMAIL_ADDRESS, CLICSHOPPING::getDef('report_email_subject'), $report, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
          }
        }
      }
    }
  }