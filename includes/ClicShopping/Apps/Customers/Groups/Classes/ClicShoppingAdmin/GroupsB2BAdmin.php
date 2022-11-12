<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class GroupsB2BAdmin
  {
    protected string $iso;

    /**
     * @param int $id
     * @return string
     */
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

      if (!empty($default)) {
        $customers_group_array[] = [
          'id' => '0',
          'text' => $default
        ];
      }

      $QcustomersGroup = $CLICSHOPPING_Db->prepare('select customers_group_id,
                                                           customers_group_name
                                                   from :table_customers_groups
                                                   order by customers_group_name
                                                  ');

      $QcustomersGroup->execute();

      while ($QcustomersGroup->fetch()) {
        $customers_group_array[] = [
          'id' => $QcustomersGroup->valueInt('customers_group_id'),
          'text' => $QcustomersGroup->value('customers_group_name')
        ];
      }

      return $customers_group_array;
    }

    /**
     * Returns an array with all customers_groups
     * @return array
     */
    public static function getAllGroups(): array
    {
      $customers_group = static::getCustomersGroup();
      $values_customers_group_id = [];

      $customers_group[] = [
        'id' => '99',
        'text' => CLICSHOPPING::getDef('text_all_groups')
      ];

      $values_customers_group_id[0] = [
        'id' => 0,
        'text' => CLICSHOPPING::getDef('visitor_name')
      ];

      for ($i = 0, $n = \count($customers_group); $i < $n; $i++) {
        $values_customers_group_id[$i + 1] = [
          'id' => $customers_group[$i]['id'],
          'text' => $customers_group[$i]['text']
        ];
      }

      return $values_customers_group_id;
    }
  }

