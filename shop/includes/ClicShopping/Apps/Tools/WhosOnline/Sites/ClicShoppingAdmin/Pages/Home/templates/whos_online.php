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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $xx_mins_ago = (time() - 900);

  $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_ShoppingCartAdmin = Registry::get('ShoppingCartAdmin');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  // remove entries that have expired
  $Qclean = $CLICSHOPPING_WhosOnline->db->prepare('delete
                                            from :table_whos_online
                                            where time_last_click = :time_last_click
                                            ');
  $Qclean->bindValue(':time_last_click', $xx_mins_ago);
  $Qclean->execute();

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  // more standard use of get vars on refresh
  if(  isset($_GET['refresh'])&& is_numeric($_GET['refresh'])  ){
    echo '<meta http-equiv="refresh" content="' . htmlspecialchars($_GET['refresh']) . ';URL=' . 'whos_online.php' . '?' . htmlspecialchars($_SERVER['QUERY_STRING']) . '">';
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/whos_online.gif', $CLICSHOPPING_WhosOnline->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_WhosOnline->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <td>
        <table class="table table-sm table-hover table-striped">
          <thead>
          <tr class="dataTableHeadingRow">
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_online'); ?></th>
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_customer_id'); ?></th>
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_full_name'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_ip_address'); ?></th>
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_entry_time'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_last_click'); ?></th>
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_user_agent'); ?>&nbsp;</th>
            <th><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_http_referer'); ?>&nbsp;</th>
            <th style="width:150px;"><?php echo $CLICSHOPPING_WhosOnline->getDef('table_heading_last_page_url'); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
  $QwhosOnline = $CLICSHOPPING_WhosOnline->db->prepare('select SQL_CALC_FOUND_ROWS customer_id,
                                                                            full_name,
                                                                            ip_address,
                                                                            time_entry,
                                                                            time_last_click,
                                                                            last_page_url,
                                                                            session_id,
                                                                            user_agent,
                                                                            http_referer
                                                 from :table_whos_online
                                                 limit :page_set_offset,
                                                      :page_set_max_results
                                                 ');
  $QwhosOnline->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $QwhosOnline->execute();

  while ($QwhosOnline->fetch() ) {

    $time_online = (time() - $QwhosOnline->value('time_entry'));

    if ((!isset($_GET['info']) || (isset($_GET['info']) && ($_GET['info'] === $QwhosOnline->value('session_id')))) && !isset($info)) {
      $info = new ObjectInfo($QwhosOnline->toArray());
    }

    $ip_address = $QwhosOnline->value('ip_address');
?>
              <th scope="row"><?php echo gmdate('H:i:s', $time_online); ?></th>
              <td><?php echo $QwhosOnline->value('customer_id'); ?></td>
              <td><?php echo $QwhosOnline->value('full_name'); ?></td>
              <td><?php echo '<a href="https://ip-lookup.net/index.php?ip='. urlencode($ip_address). '" title="Lookup" target="_blank" rel="noreferrer">'. $ip_address .'</a>'; ?></td>
              <td><?php echo date('H:i:s', $QwhosOnline->value('time_entry')); ?></td>
              <td class="text-md-center;"><?php echo date('H:i:s', $QwhosOnline->value('time_last_click')); ?></td>
              <td><?php echo  $QwhosOnline->value('user_agent'); ?></td>
              <td><?php echo  $QwhosOnline->value('http_referer'); ?></td>

    <td>
<?php
    if (preg_match('/^(.*)Lorsid=[A-Z0-9,-]+[&]*(.*)/i', $QwhosOnline->value('last_page_url'), $array)) {
      echo $array[1] . $array[2];
    } else {
      echo $QwhosOnline->value('last_page_url');
    }
?>
              </td>

            </tr>
            <tr>
              <td>
<?php
    if (isset($info)) {
      if ( $info->customer_id > 0 ) {

        echo '<strong>' . $CLICSHOPPING_WhosOnline->getDef('table_heading_shopping_cart') . '</strong><br />';

        $Qproducts = $CLICSHOPPING_WhosOnline->db->get([
                                                  'customers_basket cb',
                                                  'products_description pd'
                                                ], [
                                                  'cb.customers_basket_quantity',
                                                  'cb.products_id',
                                                  'pd.products_name'
                                                ], [
                                                  'cb.customers_id' => (int)$info->customer_id,
                                                  'cb.products_id' => [
                                                    'rel' => 'pd.products_id'
                                                  ],
                                                 'pd.language_id' => $CLICSHOPPING_Language->getId()
                                                ]
                                               );


        if ($Qproducts->fetch() !== false) {

          do {
            $contents[] = [
              'text' => $Qproducts->valueInt('customers_basket_quantity') . ' x ' . $Qproducts->value('products_name')
            ];

            $attributes = [];

            if (strpos($Qproducts->valueInt('products_id'), '{') !== false) {
              $combos = [];
              preg_match_all('/(\{[0-9]+\}[0-9]+){1}/', $Qproducts->valueInt('products_id'), $combos);

              foreach ($combos[0] as $combo) {
                $att = [];
                preg_match('/\{([0-9]+)\}([0-9]+)/', $combo, $att);

                $attributes[$att[1]] = $att[2];
              }
            }

            $CLICSHOPPING_ShoppingCartAdmin->addCart($CLICSHOPPING_ShoppingCartAdmin->getPrid($Qproducts->valueInt('products_id')), $Qproducts->valueint('customers_basket_quantity'), $attributes);
          } while ($Qproducts->fetch());



          echo $CLICSHOPPING_WhosOnline->getDef('text_shopping_cart_subtotal') . ' ' . $CLICSHOPPING_ShoppingCartAdmin->show_total();
        }
      }
    }
?>
                </td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </td>
    </table>

    <div><?php echo $CLICSHOPPING_WhosOnline->getDef('text_number_of_customers', ['number_online' => $QwhosOnline->rowCount()]); ?></div>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QwhosOnline->getPageSetLabel($CLICSHOPPING_WhosOnline->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $QwhosOnline->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
  </div>