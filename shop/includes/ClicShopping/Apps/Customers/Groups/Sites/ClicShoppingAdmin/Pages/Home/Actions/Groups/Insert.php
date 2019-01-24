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


  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Groups;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Groups = Registry::get('Groups');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $customers_groups_name = HTML::sanitize($_POST['customers_group_name']);
      $customers_groups_discount = HTML::sanitize($_POST['customers_group_discount']);
      $color_bar = HTML::sanitize($_POST['color_bar']); //ok
      $customers_group_quantity_default =  HTML::sanitize($_POST['customers_group_quantity_default']);
      $group_order_taxe = HTML::sanitize($_POST['group_order_taxe']);

// Supprimer (|| $customers_group_discount ==  0) dans la condition IF pour pouvoir cree un groupe a 0% par defaut
      if (strlen($customers_groups_name) == '') {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Groups->getDef('entry_groups_name_error'));
        $CLICSHOPPING_Groups->redirect('Groups&Insert&error=name');

      } else {

        if (empty($customers_groups_discount)) {
// valeur discount mis a zero si le champs est vide
          $customers_groups_discount = '0.00';
        }

// Affichage des prix produits en HT ou TTC (Mis automatiquement en false "HT" si l'on coche non-assujetti a la TVA)
        if ($group_order_taxe == 1) {
          $group_tax = 'false';
        } else {
          $group_tax = HTML::sanitize($_POST['group_tax']);
        }

// Module de paiement autorise
        if ($_POST['payment_unallowed']) {
          $group_payment_unallowed =  '';

          foreach ($_POST['payment_unallowed'] as $key => $val) {
            if (isset($val)) {
              $group_payment_unallowed .= $val . ',';
            }
          }

          $group_payment_unallowed = substr($group_payment_unallowed, 0,strlen($group_payment_unallowed)-1);
        }

// Module de livraison autorise
        if ($_POST['shipping_unallowed']) {
          $group_shipping_unallowed = '';

          foreach ($_POST['shipping_unallowed'] as $key => $val) {
            if (isset($val)) {
              $group_shipping_unallowed .= $val . ',';
            }
          }

          $group_shipping_unallowed = substr($group_shipping_unallowed, 0, strlen($group_shipping_unallowed)-1);
        }

        $CLICSHOPPING_Groups->db->save('customers_groups', ['customers_group_name' => $customers_groups_name,
                                                            'customers_group_discount' => (float)$customers_groups_discount,
                                                            'color_bar' => $color_bar,
                                                            'group_payment_unallowed' => $group_payment_unallowed,
                                                            'group_shipping_unallowed' => $group_shipping_unallowed ,
                                                            'group_tax' => $group_tax,
                                                            'group_order_taxe' => (int)$group_order_taxe,
                                                            'customers_group_quantity_default' => (int)$customers_group_quantity_default
                                                            ]
                                          );


        $CLICSHOPPING_Hooks->call('CustomersGroup','Insert');

        $CLICSHOPPING_Groups->redirect('Groups');
      } // end strlen($customers_groups_name)
    }
  }