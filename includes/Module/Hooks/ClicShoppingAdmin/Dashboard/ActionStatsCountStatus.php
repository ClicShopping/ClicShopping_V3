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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class ActionStatsCountStatus
  {

    public function __construct()
    {

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }
    }

    public function execute()
    {

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QordersStatus = $CLICSHOPPING_Db->prepare('select orders_status_name,
                                                         orders_status_id
                                                  from :table_orders_status
                                                  where language_id = :language_id
                                                  order by orders_status_id
                                                ');
      $QordersStatus->bindint(':language_id', $CLICSHOPPING_Language->getId());
      $QordersStatus->execute();

      $result = null;

      while ($QordersStatus->fetch()) {
        $QordersPending = $CLICSHOPPING_Db->prepare('select count(orders_id) as count
                                                     from :table_orders
                                                     where orders_status = :orders_status
                                                   ');
        $QordersPending->bindInt(':orders_status', $QordersStatus->valueInt('orders_status_id'));
        $QordersPending->execute();

        if ($QordersPending->valueInt('count') > 0) {
          $result[] = '
             <div class="row">
                <div class="col-md-11 mainTable">
                  <div class="form-group row">
                    <label for="' . CLICSHOPPING::getDef($QordersStatus->value('orders_status_name')) . '" class="col-9 col-form-label"><a href="' . CLICSHOPPING::link(null, 'A&Orders\Orders&Orders', $QordersStatus->valueInt('orders_status_id')) . '">' . CLICSHOPPING::getDef($QordersStatus->value('orders_status_name')) . '</a></label>
                    <div class="col-md-3">
                      ' . $QordersPending->valueInt('count') . '
                    </div>
                  </div>
                </div>
              </div>
            ';
        }
      }

      if (!is_null($result) && is_array($result)) {
        foreach ($result as $value) {
          echo $value;
        }
      }
    }
  }