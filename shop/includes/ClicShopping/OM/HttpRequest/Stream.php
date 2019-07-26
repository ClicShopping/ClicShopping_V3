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

  namespace ClicShopping\OM\HttpRequest;

  class Stream {
    public static function execute($parameters) {
      $options = ['http' => ['method' => ($parameters['method'] == 'get' ? 'GET' : 'POST'),
                             'follow_location' => true,
                             'max_redirects' => 5,
                             'content' => $parameters['parameters']
                            ]
                 ];

      if ( (strlen($options['http']['content']) < 1) && ($options['http']['method'] == 'POST') ) {
        $options['http']['method'] = 'GET';
      }

      if ( !isset($parameters['header']) ) {
        $parameters['header'] = array();
      }

      if ( $options['http']['method'] == 'POST' ) {
        $parameters['header'][] = 'Content-Type: application/x-www-form-urlencoded';
        $parameters['header'][] = 'Content-Length: ' . strlen($options['http']['content']);
      }

      if ( !empty($parameters['header']) ) {
        $options['http']['header'] = implode("\r\n", $parameters['header']);
      }

      if ( $parameters['server']['scheme'] === 'https' ) {
        $options['ssl'] = ['verify_peer' => true];

        if ( isset($parameters['cafile']) && file_exists($parameters['cafile']) ) {
          $options['ssl']['cafile'] = $parameters['cafile'];
        }

        if ( isset($parameters['certificate']) ) {
          $options['ssl']['local_cert'] = $parameters['certificate'];
        }
      }

      $result = '';

      try {
        $context = stream_context_create($options);

        $result = file_get_contents($parameters['url'], false, $context);
      } catch ( \Exception $e ) {
        trigger_error($e->getMessage());
      }

      return $result;
    }

    public static function canUse() {
      return extension_loaded('openssl');
    }
  }