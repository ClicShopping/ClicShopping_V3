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
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;


  $CLICSHOPPING_Members = Registry::get('Members');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  // Permettre l'utilisation de l'approbation des comptes en mode B2B
  if (MODE_B2B_B2C == 'false')  CLICSHOPPING::redirect('index.php');

  $Qcustomers = $CLICSHOPPING_Members->db->prepare('select customers_id,
                                                          customers_lastname,
                                                          customers_firstname
                                                    from :table_customers
                                                    where customers_id = :customers_id
                                                   ');

  $Qcustomers->bindInt(':customers_id', $_GET['cID']);
  $Qcustomers->execute();

  $Qreviews = $CLICSHOPPING_Members->db->prepare('select count(*) as number_of_reviews
                                                   from :table_reviews
                                                   where customers_id = :customers_id
                                                  ');
  $Qreviews->bindInt(':customers_id', $_GET['cID']);
  $Qreviews->execute();

  $cInfo_array = array_merge($Qcustomers->toArray(), $Qreviews->toArray());
  $cInfo = new ObjectInfo($cInfo_array);
?>
  <div class="contentBody">

    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/client_attente.gif', $CLICSHOPPING_Members->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-8 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Members->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12 mainTitle"><strong><?php echo  $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname; ?></strong></div>
    <?php echo HTML::form('customers', CLICSHOPPING::link('Members&ConfirmMembers&cID=' . $cInfo->customers_id)); ?>
    <div class="adminformTitle">
      <div class="row">
        <div class="separator"></div>
        <div class="col-md-12"><?php echo $CLICSHOPPING_Members->getDef('text_accept_intro'); ?><br/><br/></div>
        <div class="separator"></div>
        <div class="col-md-5"><?php echo HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getCustomersGroup($CLICSHOPPING_Members->getDef('visitor_name')), $cInfo->customers_group_id); ?><br/><br/></div>
        <div class="separator"></div>
        <div class="col-md-12"><?php echo HTML::checkboxField('delete_reviews', 'on', true) . ' ' . $CLICSHOPPING_Members->getDef('text_delete_reviews', ['delete_number' => $cInfo->number_of_reviews]); ?><br/><br/></div>
        <div class="separator"></div>
        <div class="col-md-12 text-md-center">
          <span><br /><?php echo HTML::button($CLICSHOPPING_Members->getDef('button_activate'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Members->getDef('button_cancel'), null, CLICSHOPPING::link('Members&page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id), 'warning', null, 'sm'); ?></span>
        </div>
      </div>
    </div>
    </form>
  </div>
