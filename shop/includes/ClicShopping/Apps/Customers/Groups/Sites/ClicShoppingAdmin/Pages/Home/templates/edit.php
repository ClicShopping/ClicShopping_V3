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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Apps;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Groups = Registry::get('Groups');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $QcustomersGroup = $CLICSHOPPING_Groups->db->prepare('select *
                                                 from :table_customers_groups
                                                 where customers_group_id = :customers_group_id
                                                 order by customers_group_id
                                                ');
  $QcustomersGroup->bindInt(':customers_group_id', (int)$_GET['cID'] );
  $QcustomersGroup->execute();

  $cInfo = new ObjectInfo($QcustomersGroup->toArray());

  $customers_groups_id = $QcustomersGroup->valueInt('customers_group_id');
  $customers_groups_name = $QcustomersGroup->value('customers_group_name');
  $customers_groups_discount = $QcustomersGroup->valueDecimal('customers_group_discount');
  $color_bar = $QcustomersGroup->value('color_bar');
  $customers_group_quantity_default = $QcustomersGroup->valueInt('customers_group_quantity_default');

// Affichage des prix de la boutique pour le groupe avec une taxe inclus ou exclus
  if (!isset($cInfo->group_tax)) $cInfo->group_tax = 'true';

  switch ($cInfo->group_tax) {
    case 'false':
      $group_tax_inc = false;
      $group_tax_ex = true;
      break;
    case 'true':
    default:
      $group_tax_inc = true;
      $group_tax_ex = false;
  } // end !isset

  // Affiche la case coche par defaut pour le mode de facturation utilisee avec taxe ou non
  if (!isset($cInfo->group_order_taxe)) $cInfo->group_order_taxe = '0';
  switch ($cInfo->group_order_taxe) {
    case '0':
      $status_order_taxe = true;
      $status_order_no_taxe = false;
      break;
    case '1':
      $status_order_taxe = false;
      $status_order_no_taxe = true;
      break;
    default:
      $status_order_taxe = true;
      $status_order_no_taxe = false;
  } // end !isset

  if ($CLICSHOPPING_MessageStack->exists('update')) {
    echo $CLICSHOPPING_MessageStack->get('update');
  }
?>

  <script src="<?php echo CLICSHOPPING::link('Shop/ext/javascript/colorpicker/jscolor.js'); ?>"></script>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/define_language.gif', $CLICSHOPPING_Groups->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Groups->getDef('heading_title_edit'); ?></span>
          <span class="col-md-6 text-md-right">
<?php
  echo HTML::form('customers_group', $CLICSHOPPING_Groups->link('Groups&Update'), 'post', 'onSubmit="return check_form();"');
  echo HTML::hiddenField('customer_group_id', $customers_groups_id);
  echo HTML::button($CLICSHOPPING_Groups->getDef('button_cancel'), null, $CLICSHOPPING_Groups->link('Groups'), 'warning') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Groups->getDef('button_update'), null, null, 'success');
?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="separator"></div>
<?php
    if ($error === true) {
?>
      <div class="alert alert-warning">
<?php
  if ($error == 'categorie') {
    echo '&nbsp;' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/warning.gif', $CLICSHOPPING_Groups->getDef('icon_warning')) . '&nbsp;' . $CLICSHOPPING_Groups->getDef('entry_groups_categorie_error');
  } else if ($error == 'name') {
    echo '&nbsp;' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/warning.gif', $CLICSHOPPING_Groups->getDef('icon_warning')) . '&nbsp;' . $CLICSHOPPING_Groups->getDef('entry_groups_name_error');
  }
?>
      </div>
      <div class="separator"></div>
<?php
    } // end $error
  ?>
  <!-- // FORM main screen -->

  <div id="CustomersGroupTab" class="CustomersGroupTab">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist"  id="myTab">
      <li class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Groups->getDef('tab_general') . '</a>'; ?></li>
      <li class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Groups->getDef('tab_orders'); ?></a></li>
      <li class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Groups->getDef('tab_shipping'); ?></a></li>
      <li class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Groups->getDef('tab_categorie'); ?></a></li>
    </ul>

    <div class="tabsClicShopping">
      <div class="tab-content">
<!-- //################################################################################################################ -->
<!--          ONGLET General informations groupe          //-->
<!-- //################################################################################################################ -->
        <div class="tab-pane active" id="tab1">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_group_name'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_groups_name'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_groups_name'); ?></label>
                  <div class="col-md-5">
<?php
  if ($error == "name") {
    echo HTML::inputField('customers_group_name', '', 'required aria-required="true" id="customers_group_name" placeholder="' . $CLICSHOPPING_Groups->getDef('entry_groups_name') . '" maxlength="32" style="border: 2px solid #FF0000"', true);
  } else {
    echo HTML::inputField('customers_group_name', $cInfo->customers_group_name, 'required aria-required="true" id="customers_group_name" maxlength="32"', true);
  }
?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_color_bar'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_color_bar'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('color_bar', $cInfo->color_bar, 'size=12 class="color {pickerPosition:\'right\'}"', false); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_default_discount'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_group_discount', $cInfo->customers_group_discount, 'required aria-required="true" id="customers_group_discount" maxlength="5" size="5" placeholder="%" ' , false); ?>
                    <?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount_note'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_default_quantity'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_text_quantity_default'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_text_quantity_default'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_group_quantity_default', $cInfo->customers_group_quantity_default, 'maxlength="5" size=5', false); ?>
                    <?php echo $CLICSHOPPING_Groups->getDef('entry_text_quantity_note'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_group_tax'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_group_tax'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_group_tax'); ?></label>
                  <div class="col-md-12">
                    <?php echo HTML::radioField('group_tax', 'true', $group_tax_inc) . '&nbsp;' . $CLICSHOPPING_Groups->getDef('text_group_tax_inc') . '&nbsp;' . HTML::radioField('group_tax', 'false', $group_tax_ex). '&nbsp;' . $CLICSHOPPING_Groups->getDef('text_group_tax_ex'); ?>
                    <?php echo $CLICSHOPPING_Groups->getDef('entry_group_tax_note'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="adminformAide">
            <div class="row">
              <span class="col-md-12">
                <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Groups->getDef('help_title_onglet_general')); ?>
                <strong><?php echo '&nbsp;' . $CLICSHOPPING_Groups->getDef('help_title_onglet_general'); ?></strong>
              </span>
            </div>
            <div class="separator"></div>
            <div class="row">
              <span class="col-md-12">
                <?php echo $CLICSHOPPING_Groups->getDef('help_group_tax'); ?>
              </span>
            </div>
          </div>
        </div>
<!-- //################################################################################################################ -->
<!--          ONGLET Facturation          //-->
<!-- //################################################################################################################ -->
        <div class="tab-pane" id="tab2">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_order_customer_default'); ?></div>
          <div class="adminformTitle">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <div class="col-md-5">
<?php
  if ($error === true) {
    if ($cInfo->group_order_taxe == '0') echo $CLICSHOPPING_Groups->getDef('options_order_taxe');
    if ($cInfo->group_order_taxe == '1') echo $CLICSHOPPING_Groups->getDef('options_order_no_taxe');
    echo HTML::hiddenField('group_order_taxe');
  } else {
    echo HTML::radioField('group_order_taxe', '0', $status_order_taxe) . '&nbsp;' . $CLICSHOPPING_Groups->getDef('options_order_taxe') . '<br />' . HTML::radioField('group_order_taxe', '1', $status_order_no_taxe) . '&nbsp;' . $CLICSHOPPING_Groups->getDef('options_order_no_taxe') . ' ' . $CLICSHOPPING_Groups->getDef('entry_group_tax_note');
  }
?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_group_paiement_default'); ?></div>
          <div class="adminformTitle">
<?php
  // Search payment module
  $payments_unallowed = explode (',',$cInfo->group_payment_unallowed);
  $module_directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/payment/';
  $module_key = 'MODULE_PAYMENT_INSTALLED';

  $Qconfiguration_payment = $CLICSHOPPING_Groups->db->prepare('select configuration_value
                                                        from :table_configuration
                                                        where configuration_key = :configuration_key
                                                      ');
  $Qconfiguration_payment->bindValue(':configuration_key', $module_key);
  $Qconfiguration_payment->execute();

  $modules_payment = explode(';', $Qconfiguration_payment->value('configuration_value'));
  $module_active = $modules_payment;

  $include_modules = [];

  foreach($modules_payment as $value) {
    if (strpos($value, '\\') !== false) {
      $class = Apps::getModuleClass($value, 'Payment');

      $include_modules[] = ['class' => $value,
                            'file' => $class
                           ];
    } else {
      $class = basename($value, '.php');
      $include_modules[] = ['class' => $class,
                            'file' => $value
                           ];
    }
  }

  for ($i=0, $n=count($include_modules); $i<$n; $i++) {
    if (strpos($include_modules[$i]['class'], '\\') !== false) {
      Registry::set('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
      $module = Registry::get('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']));
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <div class="col-md-12">
                    <?php echo HTML::checkboxField('payment_unallowed[' . $i . ']', $module->code , (in_array ($module->code, $payments_unallowed)) ?  true : false); ?>
                    <?php echo $module->title; ?>
                  </div>
                </div>
              </div>
            </div>
<?php
        if ($_POST['payment_unallowed'][$i]) {
          $_POST['group_payment_unallowed'] .= $_POST['payment_unallowed'][$i] . ',';
        }
      }

/*
      else {
        $file = $include_modules[$i]['file'];

         if (in_array ($include_modules[$i]['file'], $modules_payment)) {

          $CLICSHOPPING_Language->loadDefinitions($CLICSHOPPING_Template->getPathLanguageShopDirectory() . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/payment/' . $include_modules[$i]['file']);

          if (is_file($module_directory . $file)) {
           include($module_directory . $file);

           $class = substr($file, 0, strrpos($file, '.'));

           if (class_exists($class)) {
             $module = new $class;
             if ($module->check() > 0) {
               $installed_modules[] = $file;
             }
           }
          }
?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <div class="col-md-12">
                    <?php echo HTML::checkboxField('payment_unallowed[' . $i . ']', $module->code , (in_array ($module->code, $payments_unallowed)) ?  true : false); ?>
                    <?php echo $module->title; ?>
                  </div>
                </div>
              </div>
            </div>
<?php
        if ($_POST['payment_unallowed'][$i]) {
          $_POST['group_payment_unallowed'] .= $_POST['payment_unallowed'][$i] . ',';
        }
      } // end in_array
    } // end class
*/

  } // end for
?>

          </div>
          <div class="separator"></div>
            <div class="alert alert-info">
              <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Groups->getDef('help_title_onglet_facturation')) . ' ' . $CLICSHOPPING_Groups->getDef('help_title_onglet_facturation') ?></div>
              <div class="separator"></div>
              <div><?php echo $CLICSHOPPING_Groups->getDef('help_order_tax'); ?></div>
            </div>
          </div>
<!-- //################################################################################################################ -->
<!--          ONGLET Livraison          //-->
<!-- //################################################################################################################ -->
        <div class="tab-pane" id="tab3">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_group_shipping_default'); ?></div>
          <div class="adminformTitle">
<?php
  // Seach shipping module
  $shipping_unallowed = explode (',', $cInfo->group_shipping_unallowed);
  $module_directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/shipping/';

  $module_key = 'MODULE_SHIPPING_INSTALLED';

  $Qconfiguration_shipping = $CLICSHOPPING_Groups->db->prepare('select configuration_value
                                                          from :table_configuration
                                                          where configuration_key = :configuration_key
                                                        ');
  $Qconfiguration_shipping->bindValue(':configuration_key', $module_key);
  $Qconfiguration_shipping->execute();

  $modules_shipping = explode(';', $Qconfiguration_shipping->value('configuration_value'));
  $module_active = $modules_shipping;

  $include_modules = [];

  foreach($modules_shipping as $value) {
    if (strpos($value, '\\') !== false) {
      $class = Apps::getModuleClass($value, 'Shipping');

      $include_modules[] = ['class' => $value,
                            'file' => $class
                            ];
    } else {
      $class = basename($value, '.php');
      $include_modules[] = ['class' => $class,
                            'file' => $value
                            ];
    }
  }

  for ($i=0, $n=count($include_modules); $i<$n; $i++) {
    if (strpos($include_modules[$i]['class'], '\\') !== false) {
      Registry::set('Shipping_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
      $module = Registry::get('Shipping_' . str_replace('\\', '_', $include_modules[$i]['class']));
?>
          <div class="row">
            <div class="col-md-5">
              <div class="form-group row">
                <div class="col-md-12">
                  <?php echo HTML::checkboxField('shipping_unallowed[' . $i . ']', $module->code , (in_array ($module->code, $shipping_unallowed)) ?  true : false); ?>
                  <?php echo $module->title; ?>
                </div>
              </div>
            </div>
          </div>
<?php
      if ($_POST['shipping_unallowed'][$i]) {
        $_POST['group_shipping_unallowed'] .= $_POST['shipping_unallowed'][$i] . ',';
      }
    }
    /*
       else {

         $file = $include_modules[$i]['file'];

         if (in_array ($include_modules[$i]['file'], $modules_shipping)) {
           $CLICSHOPPING_Language->loadDefinitions($CLICSHOPPING_Template->getPathLanguageShopDirectory() . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/shipping/' . $include_modules[$i]['file']);
           if (is_file($module_directory . $file)) {
             include($module_directory . $file);

             $class = substr($file, 0, strrpos($file, '.'));
             if (class_exists($class)) {
               $module = new $class;
               if ($module->check() > 0) {
                 $installed_modules[] = $file;
               }
             }
           }
   ?>
             <div class="row">
               <div class="col-md-5">
                 <div class="form-group row">
                   <div class="col-md-12">
                     <?php echo HTML::checkboxField('shipping_unallowed[' . $i . ']', $module->code , (in_array ($module->code, $shipping_unallowed)) ?  true : false); ?>
                     <?php echo $module->title; ?>
                   </div>
                 </div>
               </div>
             </div>
   <?php
           if ($_POST['shipping_unallowed'][$i]) {
             $_POST['group_shipping_unallowed'] .= $_POST['shipping_unallowed'][$i] . ',';
           }
         } // end in_array
       } // end class
   */
  } // end for
?>
          </div>
        </div>
      </form>
<!-- //END FORM -->
<!-- //################################################################################################################ -->
<!--          ONGLET General discount des categories          //-->
<!-- //################################################################################################################ -->
        <div class="tab-pane" id="tab4">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_categorie_discount'); ?></div>
          <div class="adminformTitle">

            <?php echo HTML::form('InsertCategories', $CLICSHOPPING_Groups->link('Groups&InsertCategories#tab4'), 'post') . HTML::hiddenField('cID', $_GET['cID']); ?>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_group_categories'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_group_categories'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::selectMenu('categories_id', $CLICSHOPPING_CategoriesAdmin->getCategoryTree()); ?>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group row">
                  <?php echo HTML::button($CLICSHOPPING_Groups->getDef('button_insert'), null, null, 'success');  ?>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount'); ?>" class="col-5 col-form-label"><?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount'); ?></label>
                  <div class="col-md-8">
                    <?php echo HTML::inputField('discount', '0', 'maxlength="5" size="5" placeholder="%"', false); ?>
                    <?php echo $CLICSHOPPING_Groups->getDef('entry_valeur_discount_note'); ?></span>
                  </div>
                </div>
              </div>
            </div>
            </form>
          </div>

            <div class="separator"></div>
            <div class="mainTitle"><?php echo $CLICSHOPPING_Groups->getDef('title_list_categories_discount'); ?></div>
            <div class="adminformTitle">
              <table class="table table-sm table-hover">
                <thead>
                  <tr>
                    <td class="formAreaTitle"><?PHP echo $CLICSHOPPING_Groups->getDef('text_categories'); ?></td>
                    <td class="formAreaTitle text-md-center"><?PHP echo $CLICSHOPPING_Groups->getDef('table_heading_discount'); ?></td>
                    <td class="formAreaTitle text-md-right"><?PHP echo $CLICSHOPPING_Groups->getDef('table_heading_action'); ?></td>
                  </tr>
                </thead>
                <tbody>
<?php
  $index = 0;

  $QgroupToCategories = $CLICSHOPPING_Groups->db->prepare('select distinct c.discount,
                                                                     c.categories_id,
                                                                     c.customers_group_id,
                                                                     g.categories_name,
                                                                     g.language_id,
                                                                     f.parent_id
                                                    from :table_groups_to_categories  c,
                                                         :table_categories_description g,
                                                         :table_categories f
                                                    where c.customers_group_id = :customers_group_id
                                                    and c.categories_id = g.categories_id
                                                    and c.categories_id = f.categories_id
                                                    and g.language_id = :language_id
                                                    order by g.categories_name
                                                  ');
  $QgroupToCategories->bindInt(':customers_group_id', (int)$_GET['cID']);
  $QgroupToCategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

  $QgroupToCategories->execute();

  while ($group_to_categories =  $QgroupToCategories->fetch() ) {
    if ($index == 0){
      $index = 1;
      $background = 'bgcolor:white';
    } else {
      $index = 0;
      $background = '';
    }

    $Qparents = $CLICSHOPPING_Groups->db->prepare('select categories_name
                                             from :table_categories_description
                                             where categories_id = :categories_id
                                           ');
    $Qparents->bindInt(':categories_id', $QgroupToCategories->valueInt('parent_id') );
    $Qparents->execute();

    if(!is_null($Qparents->value('categories_name'))) {
      $add = $Qparents->value('categories_name') . " - ";
    } else {
      $add = '';
    }
?>
                </tbody>
                  <tr style="<?php echo $background; ?>">
<?php
  echo HTML::form('insert_categories', $CLICSHOPPING_Groups->link('Groups&UpdateCategories') );
  echo HTML::hiddenField('customers_groups_id', $customers_groups_id);
?>
                    <?php echo HTML::hiddenField('catID', $group_to_categories['categories_id']); ?>
                    <td><?php echo $group_to_categories['categories_name'];?></td>
                    <td class="text-md-center"><?php echo HTML::inputField('upddiscount', $group_to_categories['discount'], 'maxlength="5" size="5"', false); ?></td>
                    <td class="text-md-right"><?php echo HTML::button($CLICSHOPPING_Groups->getDef('button_update'), null, null, 'info', null, 'sm'); ?></form>
                    </form>
<?php
  echo HTML::form('delete_categories', $CLICSHOPPING_Groups->link('Groups&DeleteCategories'));
  echo HTML::hiddenField('customers_groups_id', $customers_groups_id);
  echo HTML::hiddenField('catID', $group_to_categories['categories_id']);
  echo HTML::button($CLICSHOPPING_Groups->getDef('button_delete'), null, null, 'danger', null, 'sm');
?>
                    </form>
                  </td>
                </tr>
    <?php
  } // end while
?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>