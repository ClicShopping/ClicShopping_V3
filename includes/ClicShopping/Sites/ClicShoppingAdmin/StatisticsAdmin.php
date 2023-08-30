<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class StatisticsAdmin
{

//
// Statistiques sur le CA
//

// CA total sur toutes les annees
// stat_total
  public static function statsTotalCaAllYear()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaTotal = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and  (YEAR(o.date_purchased))
                                              ');
    $QcaTotal->bindInt(':orders_status', 3);
    $QcaTotal->execute();
    $ca_total = $QcaTotal->fetch();

    $ca_total = $ca_total['psum'];

    if (($ca_total == '') || ($ca_total == 0)) $ca_total = 0;
    return $ca_total;
  }


// CA annee en cours
// stat0
  public static function statCurrentYear()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaYear = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                              from :table_orders o,
                                                   :table_orders_products op
                                              where o.orders_id = op.orders_id
                                              and o.orders_status = :orders_status
                                              and  ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                             ');
    $QcaYear->bindInt(':orders_status', 3);
    $QcaYear->execute();
    $ca_year = $QcaYear->fetch();

    $ca_year = $ca_year['psum'];
    if (($ca_year == '') || ($ca_year == 0)) $ca_year = 0;
    return $ca_year;
  }


// CA annee n-1
// stat1
  public static function statLastYear()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaYear1 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and ((YEAR(o.date_purchased)) >= (YEAR(CURRENT_DATE))-1)
                                                and ((YEAR(o.date_purchased)) < (YEAR(CURRENT_DATE)))
                                              ');
    $QcaYear1->bindInt(':orders_status', 3);
    $QcaYear1->execute();
    $ca_year1 = $QcaYear1->fetch();

    $ca_year1 = $ca_year1['psum'];
    if (($ca_year1 == '') || ($ca_year1 == 0)) $ca_year1 = 0;
    return $ca_year1;
  }

// Ca annee n-2
// stat2
  public static function statYearN2()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaYear2 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and ((YEAR(o.date_purchased)) >= (YEAR(CURRENT_DATE))-2)
                                                and ((YEAR(o.date_purchased)) < (YEAR(CURRENT_DATE))-1)
                                              ');
    $QcaYear2->bindInt(':orders_status', 3);
    $QcaYear2->execute();
    $ca_year2 = $QcaYear2->fetch();

    $ca_year2 = $ca_year2['psum'];
    if (($ca_year2 == '') || ($ca_year2 == 0)) $ca_year2 = 0;
    return $ca_year2;
  }

// CA annee n-3
// stat3
  public static function statYearN3()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaYear3 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                              from :table_orders o,
                                                   :table_orders_products op
                                              where o.orders_id = op.orders_id
                                              and o.orders_status = :orders_status
                                              and ((YEAR(o.date_purchased)) >= (YEAR(CURRENT_DATE))-3)
                                              and  ((YEAR(o.date_purchased)) < (YEAR(CURRENT_DATE))-2)
                                             ');
    $QcaYear3->bindInt(':orders_status', 3);
    $QcaYear3->execute();
    $ca_year3 = $QcaYear3->fetch();

    $ca_year3 = $ca_year3['psum'];
    if (($ca_year3 == '') || ($ca_year3 == 0)) $ca_year3 = 0;
    return $ca_year3;
  }

//
// Statististique sur nbr de client
//

//annee
//  stat_customers_annee
  public static function statCustomerCurrentYear()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QCustomersTotalYear = $CLICSHOPPING_Db->prepare('select count(customers_id) as customers
                                                        from :table_orders o,
                                                             :table_orders_products op
                                                        where o.orders_id = op.orders_id
                                                        and o.orders_status = :orders_status
                                                        and  ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                      ');
    $QCustomersTotalYear->bindInt(':orders_status', 3);
    $QCustomersTotalYear->execute();
    $customers_total_year = $QCustomersTotalYear->fetch();

    $customers_total_year = $customers_total_year['customers'];

    return $customers_total_year;
  }

//annee n-1
// stat_customers_annee1
  public static function statCustomerLastYear()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QCustomersTotalYear1 = $CLICSHOPPING_Db->prepare('select count(customers_id) as customers
                                                          from :table_orders o,
                                                               :table_orders_products op
                                                          where o.orders_id = op.orders_id
                                                          and o.orders_status = :orders_status
                                                          and  ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)-1))
                                                        ');
    $QCustomersTotalYear1->bindInt(':orders_status', 3);
    $QCustomersTotalYear1->execute();
    $customers_total_year1 = $QCustomersTotalYear1->fetch();

    $customers_total_year1 = $customers_total_year1['customers'];

    return $customers_total_year1;
  }

// anneee n-2
// stat_customers_annee2
  public static function statCustomerYearN2()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QCustomersTotalYear2 = $CLICSHOPPING_Db->prepare('select count(customers_id) as customers
                                                          from :table_orders o,
                                                               :table_orders_products op
                                                          where o.orders_id = op.orders_id
                                                          and o.orders_status = :orders_status
                                                          and  ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)-2))
                                                         ');
    $QCustomersTotalYear2->bindInt(':orders_status', 3);
    $QCustomersTotalYear2->execute();
    $customers_total_year2 = $QCustomersTotalYear2->fetch();

    $customers_total_year2 = $customers_total_year2['customers'];

    return $customers_total_year2;
  }

//annee n-3
// stat_customers_annee3
  public static function statCustomerYearN3()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QCustomersTotalYear3 = $CLICSHOPPING_Db->prepare('select count(customers_id) as customers
                                                          from :table_orders o,
                                                               :table_orders_products op
                                                          where o.orders_id = op.orders_id
                                                          and o.orders_status = :orders_status
                                                          and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)-3))
                                                        ');
    $QCustomersTotalYear3->bindInt(':orders_status', 3);
    $QCustomersTotalYear3->execute();
    $customers_total_year3 = $QCustomersTotalYear3->fetch();

    $customers_total_year3 = $customers_total_year3['customers'];

    return $customers_total_year3;
  }


//
// Statistiques par mois
//

// Ca mois janvier
// stat1_month
  public static function statMonthJanuary()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth1 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 1
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                ');
    $QcaMonth1->bindInt(':orders_status', 3);
    $QcaMonth1->execute();
    $month_janvier = $QcaMonth1->fetch();

    $month_janvier = $month_janvier['psum'];

    if (($month_janvier == '') || ($month_janvier == 0)) $month_janvier = 0;
    return $month_janvier;
  }

// Ca mois fevrier
// stat2_month
  public static function statMonthFebruary()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth2 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and  ((MONTH(o.date_purchased))) = 2
                                                and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                               ');
    $QcaMonth2->bindInt(':orders_status', 3);
    $QcaMonth2->execute();
    $month_fevrier = $QcaMonth2->fetch();

    $month_fevrier = $month_fevrier['psum'];
    if (($month_fevrier == '') || ($month_fevrier == 0)) $month_fevrier = 0;
    return $month_fevrier;
  }

// Ca mois mars
  public static function statMonthMarch()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth3 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and  ((MONTH(o.date_purchased))) = 3
                                                and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                              ');
    $QcaMonth3->bindInt(':orders_status', 3);
    $QcaMonth3->execute();
    $month_mars = $QcaMonth3->fetch();

    $month_mars = $month_mars['psum'];
    if (($month_mars == '') || ($month_mars == 0)) $month_mars = 0;
    return $month_mars;
  }

// Ca mois avril
  public static function statMonthApril()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth4 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                          from :table_orders o,
                                               :table_orders_products op
                                          where o.orders_id = op.orders_id
                                          and o.orders_status = :orders_status
                                          and  ((MONTH(o.date_purchased))) = 4
                                          and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                         ');
    $QcaMonth4->bindInt(':orders_status', 3);
    $QcaMonth4->execute();
    $month_avril = $QcaMonth4->fetch();

    $month_avril = $month_avril['psum'];
    if (($month_avril == '') || ($month_avril == 0)) $month_avril = 0;
    return $month_avril;
  }

// Ca mois mai
  public static function statMonthMay()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth5 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and  ((MONTH(o.date_purchased))) = 5
                                                and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                               ');
    $QcaMonth5->bindInt(':orders_status', 3);
    $QcaMonth5->execute();
    $month_mai = $QcaMonth5->fetch();

    $month_mai = $month_mai['psum'];
    if (($month_mai == '') || ($month_mai == 0)) $month_mai = 0;
    return $month_mai;
  }

// Ca mois juin
  public static function statMonthJune()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth6 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 6
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth6->bindInt(':orders_status', 3);
    $QcaMonth6->execute();
    $month_juin = $QcaMonth6->fetch();

    $month_juin = $month_juin['psum'];
    if (($month_juin == '') || ($month_juin == 0)) $month_juin = 0;
    return $month_juin;
  }

// Ca mois juillet
  public static function statMonthJuly()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth7 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 7
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth7->bindInt(':orders_status', 3);
    $QcaMonth7->execute();
    $month_juillet = $QcaMonth7->fetch();

    $month_juillet = $month_juillet['psum'];
    if (($month_juillet == '') || ($month_juillet == 0)) $month_juillet = 0;
    return $month_juillet;
  }

// Ca mois Aout
  public static function statMonthAugust()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth8 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                from :table_orders o,
                                                     :table_orders_products op
                                                where o.orders_id = op.orders_id
                                                and o.orders_status = :orders_status
                                                and  ((MONTH(o.date_purchased))) = 8
                                                and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                               ');
    $QcaMonth8->bindInt(':orders_status', 3);
    $QcaMonth8->execute();
    $month_aout = $QcaMonth8->fetch();

    $month_aout = $month_aout['psum'];
    if (($month_aout == '') || ($month_aout == 0)) $month_aout = 0;
    return $month_aout;
  }

// Ca mois Septembre
  public static function statMonthSeptember()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth9 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 9
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth9->bindInt(':orders_status', 3);
    $QcaMonth9->execute();
    $month_septembre = $QcaMonth9->fetch();

    $month_septembre = $month_septembre['psum'];
    if (($month_septembre == '') || ($month_septembre == 0)) $month_septembre = 0;
    return $month_septembre;
  }

// Ca mois Octobre
  public static function statMonthOctober()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth10 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 10
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth10->bindInt(':orders_status', 3);
    $QcaMonth10->execute();
    $month_octobre = $QcaMonth10->fetch();

    $month_octobre = $month_octobre['psum'];
    if (($month_octobre == '') || ($month_octobre == 0)) $month_octobre = 0;
    return $month_octobre;
  }

// Ca mois Novembre
  public static function statMonthNovember()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth11 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 11
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth11->bindInt(':orders_status', 3);
    $QcaMonth11->execute();
    $month_novembre = $QcaMonth11->fetch();

    $month_novembre = $month_novembre['psum'];
    if (($month_novembre == '') || ($month_novembre == 0)) $month_novembre = 0;
    return $month_novembre;
  }

// Ca mois Decembre
  public static function statMonthDecember()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcaMonth12 = $CLICSHOPPING_Db->prepare('select sum(op.final_price * op.products_quantity) as psum
                                                  from :table_orders o,
                                                       :table_orders_products op
                                                  where o.orders_id = op.orders_id
                                                  and o.orders_status = :orders_status
                                                  and  ((MONTH(o.date_purchased))) = 12
                                                  and ((YEAR(o.date_purchased)) = (YEAR(CURRENT_DATE)))
                                                 ');
    $QcaMonth12->bindInt(':orders_status', 3);
    $QcaMonth12->execute();
    $month_decembre = $QcaMonth12->fetch();

    $month_decembre = $month_decembre['psum'];
    if (($month_decembre == '') || ($month_decembre == 0)) $month_decembre = 0;
    return ($month_decembre);
  }
}

