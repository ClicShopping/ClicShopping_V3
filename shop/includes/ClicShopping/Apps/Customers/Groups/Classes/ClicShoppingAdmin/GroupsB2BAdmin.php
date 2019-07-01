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


  namespace ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class GroupsB2BAdmin
  {

    protected $default;
    protected $iso;
    protected $piva;


    public static function getCustomersGroupName(int $id): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QcustomersGroup = $CLICSHOPPING_Db->prepare('select customers_group_id,
                                                           customers_group_name
                                                   from :table_customers_groups
                                                   where customers_group_id = :customers_group_id
                                                   order by customers_group_name
                                                  ');
      $QcustomersGroup->bindInt('customers_group_id', $id);
      $QcustomersGroup->execute();

      return $QcustomersGroup->value('customers_group_name');
    }


    /**
     * Returns an array with customers_groups
     * @param string $default
     * @return array
     */
    public static function getCustomersGroup(string $default = ''): array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $customers_group_array = [];

      if ($default) {
        $customers_group_array[] = ['id' => '',
          'text' => $default
        ];
      }

      $QcustomersGroup = $CLICSHOPPING_Db->prepare('select customers_group_id,
                                                          customers_group_name
                                                   from :table_customers_groups
                                                   order by customers_group_name
                                                  ');

      $QcustomersGroup->execute();

      while ($customers_group = $QcustomersGroup->fetch()) {
        $customers_group_array[] = ['id' => $customers_group['customers_group_id'],
          'text' => $customers_group['customers_group_name']
        ];
      }

      return $customers_group_array;
    }

// Returns an array with all customers_groups
    public static function getAllGroups(): array
    {

      $customers_group = static::getCustomersGroup();

      $customers_group[] = ['id' => '99',
        'text' => CLICSHOPPING::getDef('text_all_groups')
      ];

      $values_customers_group_id[0] = ['id' => 0,
        'text' => CLICSHOPPING::getDef('visitor_name')
      ];

      for ($i = 0, $n = count($customers_group); $i < $n; $i++) {
        $values_customers_group_id[$i + 1] = ['id' => $customers_group[$i]['id'],
          'text' => $customers_group[$i]['text']
        ];
      }

      return $values_customers_group_id;
    }

    /**
     * Check the vat european
     *
     * @param string $iso , $piva, number of the company vat
     * @access public
     */
    public function isoCheck(string $iso, string $piva): string
    {

      $fp1 = fsockopen("europa.eu.int", 80, $errno1, $errstr1, 30);
      if (!$fp1) {
        //echo "$errstr1 ($errno1)<br />\n";
        $iso = '2';
      } else {
        $lang = "EN";
        $find = "No, invalid VAT number";
        fputs($fp1, "GET /comm/taxation_customs/vies/cgi-bin/viesquer?Lang=" . $lang . "&MS=" . $iso . "&ISO=" . $iso . "&VAT=" . $piva . " HTTP/1.1\r\nHost: europa.eu.int\r\n\r\n");
        $iso = '0';
        while (!feof($fp1)) {
          if (substr_count(fgets($fp1, 128), $find) == 1) {
            $iso = "1";
          }
        }
        fclose($fp1);
      }

      return $iso;
    }

  }

