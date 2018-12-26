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
  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class StoreOffline implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

      if (!defined(STORE_OFFLINE)) {
        static::install();
      }

      if(STORE_OFFLINE == 'true') {

        $allowed_ip = false;
        $ips = explode(',', STORE_OFFLINE_ALLOW);

        foreach($ips as $ip) {
          if(trim($ip) == $_SERVER['REMOTE_ADDR']) {
            $allowed_ip = true;
            break;
          }
        }

        if($allowed_ip === false) {
          CLICSHOPPING::redirect('offline.html');
        }
      }

        return true;
    }

    public static function stop() {
      return true;
    }

    private static function install() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($CLICSHOPPING_Language->getId() == 1) {
        $CLICSHOPPING_Db->save('configuration', [
            'configuration_title' => 'Souhaitez vous mettre la boutique hors ligne pour maintenance ?',
            'configuration_key' => 'STORE_OFFLINE',
            'configuration_value' => 'false',
            'configuration_description' => 'Si true, votre site sera mis hors ligne et les clients ne pourront plus commander<br><br><i>(Valeur True = Oui - Valeur False = Non)</i>',
            'configuration_group_id' => '1',
            'sort_order' => '23',
            'set_function' => 'clic_cfg_set_boolean_value(array(\'true\', \'false\'))',
            'date_added' => 'now()'
          ]
        );

        $CLICSHOPPING_Db->save('configuration', [
            'configuration_title' => 'Qui est autorisé à accéder au catalogue quand la boutique est hors ligne',
            'configuration_key' => 'STORE_OFFLINE_ALLOW',
            'configuration_value' => '',
            'configuration_description' => '<br>Veuillez spécifier votre adresse ou vos adresses IP. Si vous avez plusieurs adresses ip, veuillez suivre les instructions entre parenthèses (Chaque ip doivent être séparées par des virgules ex: 127.0.0.1,222.0.0.5',
            'configuration_group_id' => '1',
            'sort_order' => '24',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );

      } else {
        $CLICSHOPPING_Db->save('configuration', [
            'configuration_title' => 'Do want to put the shop in maintenance ?',
            'configuration_key' => 'STORE_OFFLINE',
            'configuration_value' => 'false',
            'configuration_description' => 'Si true, your site will be in off line and the customer could not take an</i>',
            'configuration_group_id' => '1',
            'sort_order' => '23',
            'set_function' => 'clic_cfg_set_boolean_value(array(\'true\', \'false\'))',
            'date_added' => 'now()'
          ]
        );


        $CLICSHOPPING_Db->save('configuration', [
            'configuration_title' => 'Who is authorized to connect to the site when the shop is under maintenance',
            'configuration_key' => 'STORE_OFFLINE_ALLOW',
            'configuration_value' => '',
            'configuration_description' => '<br>Please specify your IP address or yours IP addresses. If you have multiple IP addresses, please follow the instructions in parentheses (Each IP must be separated by commas <br />ex: 127.0.0.1,222.0.0.5',
            'configuration_group_id' => '1',
            'sort_order' => '24',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );
      }
    }
  }
