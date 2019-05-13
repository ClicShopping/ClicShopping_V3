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
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter as AppNewsletter;

  class productNotification {
    public $show_choose_audience;
    public $title;
    public $content;

    public function __construct($title, $content) {

      if (!Registry::exists('Newsletter')) {
        Registry::set('Newsletter', new AppNewsletter());
      }

      $this->app = Registry::get('Newsletter');

      $this->show_choose_audience = true;
      $this->title = $title;
      $this->content = $content;
    }

    public function choose_audience() {
      $CLICSHOPPING_Language = Registry::get('Language');

      $products_array = [];

      $Qproducts = $this->app->db->get([
                                        'products p',
                                        'products_description pd'
                                      ], [
                                        'pd.products_id',
                                        'pd.products_name'
                                      ], [
                                            'pd.language_id' =>  (int)$CLICSHOPPING_Language->getId(),
                                            'pd.products_id' => [ 'rel' => 'p.products_id'],
                                            'p.products_status' => '1',
                                            'p.products_view' => '1',
                                            'p.products_archive' => '0',
                                          ],
                                        'pd.products_name'
                                      );

      while ($Qproducts->fetch()) {
        $products_array[] = [
                             'id' => $Qproducts->valueInt('products_id'),
                             'text' => $Qproducts->value('products_name')
                            ];
      }

$choose_audience_string = '<script type="text/javascript"><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.products.length); x++) {
      if (document.notifications.products.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.products.options[x].text,document.notifications.products.options[x].value);
        }
        document.notifications.products.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.products) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' .$this->app->getDef('js_please_select_products') . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';

      $global_button = '<script language="javascript"><!--' . "\n" .
                       'document.write(\'<input type="button" value="' . $this->app->getDef('button_global')  . '" style="width: 8em;" onclick="document.location=\\\'' . $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true') . '\\\'">\');' . "\n" .
                       '//--></script><noscript><a href="' . $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true') . '">[ ' .$this->app->getDef('button_global') . ' ]</a></noscript>';

      $choose_audience_string .= '    <td class="pageHeading text-md-right"><table border="0" cellspacing="0" cellpadding="0">' .
                                 '     <form name="notifications" action="' . $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm') . '" method="post" onSubmit="return selectAll(\'notifications\', \'chosen[]\')">' . "\n" .
                                 '      <tr>' .
                                 '          <td class="text-md-right">' . HTML::button($this->app->getDef('button_send'), null, null, 'primary') . '</td>' .
                                 '          <td>&nbsp;</td>' .
                                 '          <td class="text-md-right"><a href="' . $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' . HTML::button($this->app->getDef('button_cancel'), null, null, 'danger') . '</a></td>' .
                                 '        </tr>' .
                                 '      </table></td>' .
                                 '    </tr>' .
                                 '  </table></td>' .
                                 '</tr>' .
                                 '<tr>' .
                                 '  <td>&nbsp;</td>' .
                                 '</tr>';

      $choose_audience_string .= '<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr>' . "\n" .
                                 '  <tr>' . "\n" .
                                 '    <td class="text-md-center"><b>' . $this->app->getDef('text_products') . '</b><br />' . HTML::selectMenu('products', $products_array, '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '    <td class="text-md-center">&nbsp;<br />' . $global_button . '<br /><br /><br /><input type="button" value="' .$this->app->getDef('button_select') . '" style="width: 8em;" onClick="mover(\'remove\');"><br /><br /><input type="button" value="' . $this->app->getDef('button_unselect') . '" style="width: 8em;" onClick="mover(\'add\');"></td>' . "\n" .
                                 '    <td class="text-md-center"><b>' . $this->app->getDef('text_selected_products') . '</b><br />' . HTML::selectMenu('chosen[]', array(), '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '  </tr>' . "\n" .
                                 '</table></form>';

      return $choose_audience_string;
    }

    public function confirm() {
      $audience = [];

      if (isset($_GET['global']) && ($_GET['global'] == 'true')) {

        $Qproducts = $this->app->db->get('products_notifications', 'distinct customers_id');

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = '1';
        }

        $Qcustomers = $this->app->db->get('customers_info', 'customers_info_id', ['global_product_notifications' => '1']);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_info_id')] = '1';
        }

      } else {
        $chosen = [];

        foreach ($_POST['chosen'] as $id) {
          if (is_numeric($id) && !in_array($id, $chosen)) {
            $chosen[] = $id;
          }
        }

        $ids = array_map(function($k) {
                                    return ':products_id_' . $k;
                                  },
                                  array_keys($chosen)
                        );

        $Qproducts = $this->app->db->prepare('select distinct customers_id
                                              from :table_products_notifications
                                              where products_id in (' . implode(', ', $ids) . ')
                                             ');

        foreach ($chosen as $k => $v) {
          $Qproducts->bindInt(':products_id_' . $k, $v);
        }

        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = '1';
        }

        $Qcustomers = $this->app->db->get('customers_info', 'customers_info_id', ['global_product_notifications' => '1']);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_info_id')] = '1';
        }
      }

      if (count($audience) > 0) {
        if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
          $confirm_button_string .= HTML::hiddenField('global', 'true');
        } else {
          for ($i = 0, $n = count($chosen); $i < $n; $i++) {
            $confirm_button_string .= HTML::hiddenField('chosen[]', $chosen[$i]);
          }
        }
        $confirm_button_string .= HTML::button($this->app->getDef('button_submit'), null, null, 'primary')  . ' ';
      }

      $confirm_string = '    <td class="pageHeading text-md-right"><table border="0" cellspacing="0" cellpadding="0">' .
'      <tr>' . HTML::form('confirm', $this->app->link('Newsletter&ConfirmSend&page=' . $_GET['page'] . '&nID=' . $_GET['nID'])) .
'          <td  class="text-md-right">' . $confirm_button_string . '</td>' .
'          <td>&nbsp;</td>' .
'          <td class="text-md-right">' .  HTML::button($this->app->getDef('button_back'), null, $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=send'), 'primary') . '</a></td>' .
'          <td>&nbsp;</td>' .
'          <td class="text-md-right">' . HTML::button($this->app->getDef('button_cancel'), null, $this->app->link('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID']), 'danger') . '</a></td>' .
'        </tr>' .
'      </table></td>' .
'    </tr>' .
'  </table></td>' .
'</tr>' .
                        '<tr>' .
                        '  <td>&nbsp;</td>' .
                        '</tr>';

      $confirm_string .= '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><p style="color:#ff0000;"><strong>' .$this->app->getDef('text_count_customers', ['audience' => count($audience)]) . '</strong></p></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><strong>' . $this->title . '</strong></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main">' . $this->content . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '</table>';

      return $confirm_string;
    }

// Envoie du mail sans gestion de Fckeditor
    public function send($newsletter_id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Mail= Registry::get('Mail');

      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }

      $audience = [];

      if (isset($_POST['global']) && ($_POST['global'] == 'true')) {

        $Qproducts = $CLICSHOPPING_Db->get([
                                    'customers c',
                                    'products_notifications pn'
                                  ], [
                                    'distinct pn.customers_id',
                                    'c.customers_firstname',
                                    'c.customers_lastname',
                                    'c.customers_email_address'
                                  ], [
                                    'c.customers_id' => [
                                      'rel' => 'pn.customers_id'
                                    ]
                                  ]
                                 );

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
                                                        'firstname' => $Qproducts->value('customers_firstname'),
                                                        'lastname' => $Qproducts->value('customers_lastname'),
                                                        'email_address' => $Qproducts->value('customers_email_address')
                                                      ];
        }

        $Qcustomers = $CLICSHOPPING_Db->get([
                                    'customers c',
                                    'customers_info ci'
                                  ], [
                                    'c.customers_id',
                                    'c.customers_firstname',
                                    'c.customers_lastname',
                                    'c.customers_email_address'
                                  ], [
                                    'c.customers_id' => [
                                      'rel' => 'ci.customers_info_id'
                                    ],
                                    'ci.global_product_notifications' => '1',
                                    'c.customers_email_validation' => '0'
                                  ]
                                  );

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_id')] = [
                                                        'firstname' => $Qcustomers->value('customers_firstname'),
                                                        'lastname' => $Qcustomers->value('customers_lastname'),
                                                        'email_address' => $Qcustomers->value('customers_email_address')
                                                      ];
        }
      } else {
       $chosen = [];

        foreach ($_POST['chosen'] as $id) {
          if (is_numeric($id) && !in_array($id, $chosen)) {
            $chosen[] = $id;
          }
        }

        $ids = array_map(function($k) {
                                    return ':products_id_' . $k;
                                  }, array_keys($chosen)
                      );

        $Qproducts = $CLICSHOPPING_Db->prepare('select distinct pn.customers_id,
                                                         c.customers_firstname,
                                                         c.customers_lastname,
                                                         c.customers_email_address
                                           from :table_customers c,
                                           :table_products_notifications pn
                                           where c.customers_id = pn.customers_id
                                           and pn.products_id in (' . implode(', ', $ids) . ')
                                         ');

        foreach ($chosen as $k => $v) {
          $Qproducts->bindInt(':products_id_' . $k, $v);
        }

        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
                                                        'firstname' => $Qproducts->value('customers_firstname'),
                                                        'lastname' => $Qproducts->value('customers_lastname'),
                                                        'email_address' => $Qproducts->value('customers_email_address')
                                                      ];
        }


        $Qcustomers = $CLICSHOPPING_Db->get([
                                    'customers c',
                                    'customers_info ci'
                                  ], [
                                    'c.customers_id',
                                    'c.customers_firstname',
                                    'c.customers_lastname',
                                    'c.customers_email_address'
                                  ], [
                                    'c.customers_id' => [
                                      'rel' => 'ci.customers_info_id'
                                    ],
                                    'ci.global_product_notifications' => '1',
                                    'c.customers_email_validation' => '0'
                                  ]
                                 );

        while ($Qcustomers->fetch() ) {
          $audience[$Qcustomers->valueInt('customers_id')] = array('firstname' => $Qcustomers->value('customers_firstname'),
                                                                  'lastname' => $Qcustomers->value('customers_lastname'),
                                                                  'email_address' => $Qcustomers->value('customers_email_address'));
        }
      } //end else

// Build the text version
        $text = strip_tags($this->content);

        $CLICSHOPPING_Mail->addText($text . $this->app->getDef('text_unsubscribe') . HTTP::getShopUrlDomain() . 'index.php?Account&Newsletters');
        $CLICSHOPPING_Mail->build_message();

        foreach ( $audience as $key => $value ) {
          $CLICSHOPPING_Mail->send($value['firstname'] . ' ' . $value['lastname'], $value['email_address'], '', $this->app->getDef('email_from'), $this->title);
        }

        $newsletter_id = HTML::sanitize($newsletter_id);

        $CLICSHOPPING_Db->save('newsletters', ['date_sent' => 'now()',
                                                'status' => '1'
                                               ], [
                                                'newsletters_id' => (int)$newsletter_id
                                               ]
                              );

    } //end function send


// Envoie du mail avec gestion des images pour Fckeditor et Imanager.
    public function sendCkeditor($newsletter_id) {
      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Mail= Registry::get('Mail');

      $audience = [];

      if (isset($_POST['global']) && ($_POST['global'] == 'true')) {

        $Qproducts = $CLICSHOPPING_Db->prepare('select distinct pn.customers_id,
                                                        c.customers_firstname,
                                                        c.customers_lastname,
                                                        c.customers_email_address
                                        from :table_customers c,
                                             :table_products_notifications pn
                                        where c.customers_id = pn.customers_id
                                        and customers_email_validation = 0
                                        ');

        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
                                                              'firstname' => $Qproducts->value('customers_firstname'),
                                                              'lastname' => $Qproducts->value('customers_lastname'),
                                                              'email_address' => $Qproducts->value('customers_email_address')
                                                            ];
        }

        $Qcustomers = $CLICSHOPPING_Db->get([
                                        'customers c',
                                        'customers_info ci'
                                      ], [
                                        'c.customers_id',
                                        'c.customers_firstname',
                                        'c.customers_lastname',
                                        'c.customers_email_address'
                                      ], [
                                        'c.customers_id' => [
                                                            'rel' => 'ci.customers_info_id'
                                                            ],
                                        'ci.global_product_notifications' => '1',
                                        'c.customers_email_validation' => '0'
                                      ]
                                     );


        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_id')] = [
                                                              'firstname' => $Qcustomers->value('customers_firstname'),
                                                              'lastname' => $Qcustomers->value('customers_lastname'),
                                                              'email_address' => $Qcustomers->value('customers_email_address')
                                                              ];
        }
      } else {
        $chosen = $_POST['chosen'];

        $ids = implode(',', $chosen);

        $Qproducts = $CLICSHOPPING_Db->prepare('select distinct pn.customers_id,
                                                        c.customers_firstname,
                                                        c.customers_lastname,
                                                        c.customers_email_address
                                         from :table_customers c,
                                              :table_products_notifications pn
                                         where c.customers_id = pn.customers_id
                                         and pn.products_id in ( :products_id )
                                         and customers_email_validation = 0
                                        ');
        $Qproducts->binInt('products_id', $ids);
        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = array('firstname' => $Qproducts->value('customers_firstname'),
                                                                  'lastname' => $Qproducts->value('customers_lastname'),
                                                                  'email_address' => $Qproducts->value('customers_email_address')
                                                                  );
        }

        $Qcustomers = $CLICSHOPPING_Db->get([
                                    'customers c',
                                    'customers_info ci'
                                  ], [
                                    'c.customers_id',
                                    'c.customers_firstname',
                                    'c.customers_lastname',
                                    'c.customers_email_address'
                                  ], [
                                    'c.customers_id' => [
                                      'rel' => 'ci.customers_info_id'
                                    ],
                                    'ci.global_product_notifications' => '1',
                                    'c.customers_email_validation' => '0'
                                  ]
                                 );

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
                                                            'firstname' => $Qproducts->value('customers_firstname'),
                                                            'lastname' => $Qproducts->value('customers_lastname'),
                                                            'email_address' => $Qproducts->value('customers_email_address')
                                                            ];
        }

      } // end else

      $message = html_entity_decode($this->content . $this->app->getDef('text_unsubscribe') . HTTP::getShopUrlDomain() . 'index.php?Account&Newsletters');
      $CLICSHOPPING_Mail = str_replace('src="/', 'src="' . HTTP::getShopUrlDomain() . '/', $message);

      $CLICSHOPPING_Mail->addHtmlCkeditor($message);
      $CLICSHOPPING_Mail->build_message();

      foreach ( $audience as $key => $value ) {
        $CLICSHOPPING_Mail->send($value['firstname'] . ' ' . $value['lastname'], $value['email_address'], '', $this->app->getDef('email_from'), $this->title);
      }

      $CLICSHOPPING_Db->save('newsletters', [
                                    'date_sent' => 'now()',
                                    'status' => '1'
                                  ], [
                                    'newsletters_id' => (int)$newsletter_id
                                  ]
                     );

    }
  } // end class
