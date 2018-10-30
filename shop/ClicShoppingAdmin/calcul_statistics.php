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

  use ClicShopping\Sites\ClicShoppingAdmin\StatisticsAdmin;

// Calcul concernant les statistiques generales

  $total ='';
  $annuel3 = '';
  $annuel2 = '';
  $annuel1 = '';
  $annuel = '';

// Calcul CA annuel
    $ca_total =  StatisticsAdmin::statsTotalCaAllYear($total); // sur toutes les annees
    $annuel3 = StatisticsAdmin::statYearN3($annuel3); // annee en cours n-3
    $annuel2 =  StatisticsAdmin::statYearN2($annuel2); // annee en cours n-2
    $annuel1 =  StatisticsAdmin::statLastYear($annuel1); // annee en cours n-1
    $annuel =   StatisticsAdmin::statCurrentYear($annuel); // annee en cours

// Calcul Taux de croissance sur l'annee precedente
    if (($annuel == 0) || ($annuel1 == 0)) {
        $taux_croissance_annee1 = 0;
      } else {
        $taux_croissance_annee1 = (round(($annuel/$annuel1)-1,2)*100);
      }
      if (($annuel1 == 0) || ($annuel2 == 0)) {
        $taux_croissance_annee2 = 0 ;
      } else {
        $taux_croissance_annee2 =  (round(($annuel1/$annuel2)-1,2)*100);
      }
      if (($annuel2 == 0) || ($annuel3 == 0)) {
        $taux_croissance_annee3 = 0 ;
      } else {
        $taux_croissance_annee3 =  (round(($annuel2/$annuel3)-1,2)*100);
      }

// Calcul CA Mensuel
  $month_janvier1 = round(StatisticsAdmin::statMonthJanuary(),0);
  $month_fevrier1 = round(StatisticsAdmin::statMonthFebruary(),0);
  $month_mars1 = round(StatisticsAdmin::statMonthMarch(),0);
  $month_avril1 = round(StatisticsAdmin::statMonthApril(),0);
  $month_mai1 = round(StatisticsAdmin::statMonthMay(),0);
  $month_juin1 =  round(StatisticsAdmin::statMonthJune(),0);
  $month_juillet1 = round(StatisticsAdmin::statMonthJuly(),0);
  $month_aout1 = round(StatisticsAdmin::statMonthAugust(),0);
  $month_septembre1 = round(StatisticsAdmin::statMonthSeptember(),0);
  $month_octobre1 = round(StatisticsAdmin::statMonthOctober(),0);
  $month_novembre1 = round(StatisticsAdmin::statMonthNovember(),0);
  $month_decembre1 = round(StatisticsAdmin::statMonthDecember(),0);


// Calcul CA Mensuel cumule
  $month_janvier = round(StatisticsAdmin::statMonthJanuary(),0);
  $month_fevrier = round(StatisticsAdmin::statMonthFebruary() +$month_janvier,0);
  $month_mars = round(StatisticsAdmin::statMonthMarch() + $month_fevrier,0);
  $month_avril = round(StatisticsAdmin::statMonthApril() + $month_mars,0);
  $month_mai = round(StatisticsAdmin::statMonthMay() +  $month_avril,0);
  $month_juin =  round(StatisticsAdmin::statMonthJune() +  $month_mai,0);
  $month_juillet = round(StatisticsAdmin::statMonthJuly() +  $month_juin,0);
  $month_aout = round(StatisticsAdmin::statMonthAugust() +  $month_juillet,0);
  $month_septembre = round(StatisticsAdmin::statMonthSeptember() +  $month_aout,0);
  $month_octobre = round(StatisticsAdmin::statMonthOctober() +  $month_septembre,0);
  $month_novembre = round(StatisticsAdmin::statMonthNovember() +  $month_octobre,0);
  $month_decembre = round(StatisticsAdmin::statMonthDecember() +  $month_novembre,0);

// Calcul du nbr de client annuel
  $customers_total_annee = StatisticsAdmin::statCustomerCurrentYear();
  $customers_total_annee1 = StatisticsAdmin::statCustomerLastYear();
  $customers_total_annee2 = StatisticsAdmin::statCustomerYearN2();
  $customers_total_annee3 = StatisticsAdmin::statCustomerYearN3();


// Calcul du panier Moyen par annee
// Sur toutes les annees
// All the years
    if (($annuel == 0 || $customers_total_annee == 0)) {
      $panier_client_total = 0;
    } else {
      $panier_client_total = (round(($ca_total/($customers_total_annee + $customers_total_annee1 + $customers_total_annee2 + $customers_total_annee3)),2));
    }

    if (($annuel == 0 || $customers_total_annee == 0)) {
      $panier_client_annee = 0;
    } else {
      $panier_client_annee = (round(($annuel/$customers_total_annee),2));
    }

    if (($annuel1 == 0 || $customers_total_annee1 == 0)) {
      $panier_client_annee1 = 0 ;
    } else {
      $panier_client_annee1 =  (round(($annuel1/$customers_total_annee1),2));
    }

    if (($annuel2 == 0 || $customers_total_annee2 == 0)) {
      $panier_client_annee2 = 0 ;
    } else {
      $panier_client_annee2 =  (round(($annuel2/$customers_total_annee2),2));
    }

    if (($annuel3 == 0 || $customers_total_annee3 == 0)) {
      $panier_client_annee3 = 0 ;
    } else {
      $panier_client_annee3 =  (round(($annuel3/$customers_total_annee3),2));
    }



// Calcul du panier Moyen par annee
    if (($panier_client_annee == 0) || ($panier_client_annee1 == 0)) {
      $croissance_panier_client_annee = 0;
    } else {
      $croissance_panier_client_annee = (round(($panier_client_annee / $panier_client_annee1)-1,2)*100);
    }

    if (($panier_client_annee1 == 0) || ($panier_client_annee2 == 0)) {
      $croissance_panier_client_annee1 = 0;
    } else {
      $croissance_panier_client_annee1 = (round(($panier_client_annee1 / $panier_client_annee2)-1,2)*100);
    }

    if (($panier_client_annee2 == 0) || ($panier_client_annee3 == 0)) {
      $croissance_panier_client_annee2 = 0;
    } else {
      $croissance_panier_client_annee2 = (round(($panier_client_annee2 / $panier_client_annee3)-1,2)*100);
    }

    if (($panier_client_annee3 == 0) || ($panier_client_annee4 == 0)) {
      $croissance_panier_client_annee3 = 0;
    } else {
      $croissance_panier_client_annee3 = (round(($panier_client_annee3 / $panier_client_annee4)-1,2)*100);
    }

// calcul average feedback
