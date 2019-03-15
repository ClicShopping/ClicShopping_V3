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

  namespace ClicShopping\Apps\Communication\PageManager\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  class PageManagerShop {

    protected $pages_string;
    protected $pages_introduction;
    protected $pages_accueil;
    protected $pages_contact;
    protected $pages_liste_info_box;
    protected $pages_liste_info_box_secondary;
    protected $pages_liste_info_box_footer;
    protected $pages_informations;
    protected $id;
    protected $db;
    protected $customer;
    protected $lang;
    protected $rewriteUrl;

    public function __construct() {
      $this->db = Registry::get('Db');
      $this->customer = Registry::get('Customer');
      $this->lang = Registry::get('Language');
      $this->rewriteUrl = Registry::get('RewriteUrl');
    }

/**
 * Count the introduction page
 * @return mixed
 */
    private function pageManagerDisplayPageIntroCount() {
      $Qpages = $this->db->prepare('select count(*) as count
                                     from :table_pages_manager
                                     where status = 1
                                     and page_type = 1
                                    ');
      $Qpages->execute();

      return $Qpages->valueInt('count');
    }

/**
 * Time to display the introduction page
 * @return mixed
 */
    public function pageManagerDisplayPageIntroTime() {

      if ($this->pageManagerDisplayPageIntroCount() > 0) {

        $Qpages = $this->db->prepare('select p.pages_id,
                                               p.page_time
                                        from :table_pages_manager p
                                        where p.status = 1
                                        and p.page_type = 1
                                        order by rand()
                                       ');
        $Qpages->execute();

        $pages = ['pages_id' => $Qpages->valueInt('pages_id'),
                  'page_time' => $Qpages->value('page_time')
                  ];
      } else {

        $pages = ['pages_id' => 0,
                  'page_time' => 0
                 ];
      }

      return $pages['page_time'];
    }

/**
 * display the introduction page
 * @return mixed
 */
    public function pageManagerDisplayPageIntro() {
      if ($this->pageManagerDisplayPageIntroCount() > 0) {

        $Qpages = $this->db->prepare('select p.pages_id,
                                              s.pages_html_text
                                       from :table_pages_manager p,
                                            :table_pages_manager_description s
                                       where p.status = 1
                                       and p.pages_id = s.pages_id
                                       and p.page_type = 1
                                       and s.language_id= :language_id
                                       order by rand()
                                       limit 1
                                      ');
        $Qpages->bindValue(':language_id', (int)$this->lang->getId() );
        $Qpages->execute();

        $pages = ['pages_id' => $Qpages->valueInt('pages_id'),
                  'pages_html_text' => $Qpages->value('pages_html_text')
                 ];
      } else {
        $pages = ['pages_id' => 0,
                  'pages_html_text' => ''
                 ];
      }

      return $pages['pages_html_text'];
    }

/**
 * Display informations on frontpage
 * @return mixed
 */
    public function pageManagerDisplayFrontPage() {
      $Qpages = $this->db->prepare('select count(*) as count
                                     from :table_pages_manager
                                     where status = 1
                                     and page_type = 2
                                   ');

      $Qpages->execute();

      if ($Qpages->valueInt('count') > 0) {

        $Qpages = $this->db->prepare('select p.pages_id,
                                              s.pages_html_text
                                       from :table_pages_manager p,
                                            :table_pages_manager_description s
                                       where p.status = 1
                                       and p.page_type = 2
                                       and p.pages_id = s.pages_id
                                       and (s.language_id  = :language_id or
                                            s.language_id = 0)
                                       and (p.customers_group_id = :customers_group_id or
                                            p.customers_group_id = 99
                                           )
                                       order by rand()
                                       limit 1
                                     ');

        $Qpages->bindInt(':language_id', (int)$this->lang->getId() );
        $Qpages->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID()  );
        $Qpages->execute();

        $pages = ['pages_id' => $Qpages->valueInt('pages_id'),
                  'pages_html_text' => $Qpages->value('pages_html_text')
                 ];
      } else {
        $pages = ['pages_id' => 0,
                  'pages_html_text' => ''
                 ];
      }

      return $pages['pages_html_text'];
    }

/**
 * display information in contact page
 * @return mixed
 */
    public function pageManagerDisplayContact() {
      $Qpages = $this->db->prepare('select  p.pages_id,
                                             s.pages_html_text
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and p.page_type = 3
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                  ');

      $Qpages->bindInt(':language_id', (int)$this->lang->getId() );
      $Qpages->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID() );

      $Qpages->execute();

      if ( $Qpages->fetch() !== true ) {
        $pages = ['pages_id' => $Qpages->valueInt('pages_id'),
                  'pages_html_text' => $Qpages->value('pages_html_text')
                 ];
      } else {
        $pages = ['pages_id' => 0,
                  'pages_html_text' => ''
                  ];
      }

      return $pages['pages_html_text'];
    }

/**
 * display information in footer
 * @param string $start_class
 * @param string $end_class
 * @param string $separation
 * @return mixed
 */
    public function pageManagerDisplayBox($start_class = '<div class="pageManagerDisplayBox">', $end_class = '</div>', $separation = '') {

        $QPage = $this->db->prepare('select  p.pages_id,
                                            p.sort_order,
                                            p.status,
                                            p.page_box,
                                            s.pages_title,
                                            s.pages_html_text,
                                            s.externallink,
                                            p.links_target
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and (p.page_type = 4 or p.page_type = 3)
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and p.page_box = 0
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                     order by p.sort_order, s.pages_title
                                    ');

      $QPage->bindInt(':language_id', $this->lang->getId());
      $QPage->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $QPage->setCache('boxe_page_manager_primary-lang' . $this->lang->getId());

      $QPage->execute();

      $separ = $separation;

      $page_liste_box = $start_class;

      while ($QPage->fetch() !== false) {
        if (!empty($QPage->value('externallink')))  {
          $search = strpos($QPage->value('externallink'), 'index.php');

          if ($search === false) {
            $page_liste_box .= '<span>' . HTML::link($QPage->value('externallink'), $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . ' rel="noreferrer"') . '</span>';
          } else {
            $page_liste_box .= '<span>' . HTML::link(CLICSHOPPING::link($QPage->value('externallink')), $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '"') . '</span>' . '<br />';
          }
        } else {
          if ($QPage->valueInt('pages_id') != 3){
            $link = $this->rewriteUrl->getPageManagerContentUrl($QPage->valueInt('pages_id'));
          } else {
            $link = CLICSHOPPING::link(null, 'Info&Contact');
          }

          if (!empty($QPage->value('pages_title'))) {
            $search = strpos($QPage->value('externallink'), 'index.php');

             if ($search === false) {
              $page_liste_box .= $start_class . $separ . HTML::link($link, $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '" rel="noreferrer"') . $end_class;
            } else {
              $page_liste_box .= $start_class . $separ . HTML::link($link, $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '"') . $end_class . '<br />';
            }
          }
        }
      }

      $page_liste_box .= '</div>';
      $page = ['text' => $page_liste_box];

      return  $page['text'];
     }

/**
 * display the secondary box
 * @param string $start_class
 * @param string $end_class
 * @param string $separation
 * @return mixed
 */
    public function pageManagerDisplaySecondaryBox($start_class = '<div class="pageManagerDisplaySecondaryBox">', $end_class = '</div>', $separation = '|') {
      $QPageSecondary = $this->db->prepare('select SQL_CALC_FOUND_ROWS p.pages_id,
                                                                        p.sort_order,
                                                                        p.status,
                                                                        p.page_box,
                                                                        s.pages_title,
                                                                        s.pages_html_text,
                                                                        s.externallink,
                                                                        p.links_target
                                           from :table_pages_manager p,
                                                :table_pages_manager_description s
                                           where p.pages_id = s.pages_id
                                           and p.status = 1
                                           and (p.page_type = 4 or p.page_type = 3)
                                           and (s.language_id  = :language_id or s.language_id  = 0)
                                           and p.page_box = 1
                                           and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                           order by p.sort_order,
                                                    s.pages_title
                                        ');

      $QPageSecondary->bindInt(':language_id', $this->lang->getId());
      $QPageSecondary->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $QPageSecondary->setCache('boxe_page_manager_secondary-' . $this->lang->getId());

      $QPageSecondary->execute();

      $TotalRow = $QPageSecondary->getPageSetTotalRows();

      if ($TotalRow > 0 ) {
        $rows = 0;
        $page_liste_box_secondary = $start_class;

        while ($QPageSecondary->fetch()) {
          $rows++;

          if($rows != 1)  {
            $separ = $separation;
          } else {
            $separ = '';
          }

          if (!empty($QPageSecondary->value('externallink'))) {
            $search = strpos($QPageSecondary->value('externallink'), 'index.php');

            if ($search === false) {
              $page_liste_box_secondary .= '<span>' . HTML::link($QPageSecondary->value('externallink'), $QPageSecondary->value('pages_title'), 'target="' . $QPageSecondary->value('links_target') . '" rel="noreferrer"') . '</span>';
            } else {
              $page_liste_box_secondary .= '<span>' . HTML::link(CLICSHOPPING::link($QPageSecondary->value('externallink')), $QPageSecondary->value('pages_title'), 'target="' . $QPageSecondary->value('links_target') . '"') . '</span><br />';
            }
          } else {
            if ($QPageSecondary->valueInt('pages_id') != 3) {
              $link = $this->rewriteUrl->getPageManagerContentUrl($QPageSecondary->valueInt('pages_id'));
            } else {
              $link_secondary = CLICSHOPPING::link(null, 'Info&Contact');
            }

            if (!empty($QPageSecondary->value('pages_title'))) {
              $search = strpos($QPageSecondary->value('externallink'), 'index.php');

              if ($search === false) {
                $page_liste_box_secondary .= $start_class . $separ . HTML::link($link_secondary, $QPageSecondary->value('pages_title'), 'target="' . $QPageSecondary->value('links_target') . '" rel="noreferrer"') . $end_class;
              } else {
                $page_liste_box_secondary .= $start_class . $separ . HTML::link($link_secondary, $QPageSecondary->value('pages_title'), 'target="' . $QPageSecondary->value('links_target') . '"') . $end_class . '<br />';
              }
            }
          }
        }

        $page_liste_box_secondary .= '</div>';

        $pages_liste_info_box_secondary = ['text' => $page_liste_box_secondary];
      }

      return $pages_liste_info_box_secondary['text'];
    }

/**
 * @param string $start_class
 * @param string $end_class
 * @param string $separation
 * @return mixed
 */
    public function pageManagerDisplayHeaderMenu($start_class = '<span class="menuHeaderPageManager">', $end_class = '</span>', $separation = '|') {
        $QPage = $this->db->prepare('select  p.pages_id,
                                            p.sort_order,
                                            p.status,
                                            p.page_box,
                                            s.pages_title,
                                            s.externallink,
                                            p.links_target
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and p.page_type = 5
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                     order by p.sort_order, s.pages_title
                                    ');

        $QPage->bindInt(':language_id', (int)$this->lang->getId() );
        $QPage->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID() );

        $QPage->setCache('page_manager_display_header_menu-lang' . $this->lang->getId());

        $QPage->execute();

        $rows = 0;
        $page_menu_header = '';

//***********************************
// -------- menu header -----------
//***********************************
      while ($QPage->fetch() !== false) {

        $rows++;

        if($rows != 1)  {
          $separ = $separation;
        } else {
          $separ = '';
        }

        if (!empty($QPage->value('externallink')))  {
          $search = strpos($QPage->value('externallink'), 'index.php');

          if ($search === false) {
            $page_menu_header .= $start_class . $separ . HTML::link($QPage->value('externallink'), $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '" class="menuHeaderPageManager" rel="noreferrer"') . $end_class;
          } else {
            $page_menu_header .= $start_class . $separ . HTML::link(CLICSHOPPING::link($QPage->value('externallink')), $QPage->value('pages_title'), 'class="menuHeaderPageManager" target="' . $QPage->value('links_target') . '"') . $end_class;
          }
        }
      }

      $pages = ['text' => $page_menu_header];

      return $pages['text'];
    }

/**
 * @param string $start_class
 * @param string $end_class
 * @param string $separation
 * @return mixed
 */
    public function pageManagerDisplayFooterMenu($start_class = '<span class="menuFooterPageManager">', $end_class = '</span>', $separation = ' | ') {
      $QPage = $this->db->prepare('select  p.pages_id,
                                            p.sort_order,
                                            p.status,
                                            p.page_box,
                                            s.pages_title,
                                            s.externallink,
                                            p.links_target
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and p.page_type = 6
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                     order by p.sort_order, s.pages_title
                                    ');

      $QPage->bindInt(':language_id', (int)$this->lang->getId() );
      $QPage->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID() );

      $QPage->setCache('page_manager_display_footer_menu-lang' . $this->lang->getId());

      $QPage->execute();

      $rows = 0;
      $page_menu_header = '';

//***********************************
// -------- menu header -----------
//***********************************
      while ($QPage->fetch() !== false) {
        $rows++;

        if($rows != 1)  {
          $separ = $separation;
        } else {
          $separ = '';
        }

        if (!empty($QPage->value('externallink')))  {
          $search = strpos($QPage->value('externallink'), 'index.php');

          if ($search === false) {
            $page_menu_header .= $start_class . $separ . HTML::link($QPage->value('externallink'), $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . ' class="menuFooterPageManager" rel="noreferrer"') . $end_class;
          } else {
            $page_menu_header .= $start_class . $separ . HTML::link(CLICSHOPPING::link($QPage->value('externallink')), $QPage->value('pages_title'), 'class="menuHeaderPageManager" target="' . $QPage->value('links_target') . '"') . $end_class;
          }
        }
      }

      $pages = ['text' => $page_menu_header];

      return $pages['text'];
    }

/**
 * display the footer menu information
 * @return mixed
 */
    public function pageManagerDisplayFooter() {
      $QPage = $this->db->prepare('select  p.pages_id,
                                            p.sort_order,
                                            p.status,
                                            p.page_box,
                                            s.pages_title,
                                            s.pages_html_text,
                                            s.externallink,
                                            p.links_target
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and (p.page_type = 4 or p.page_type = 3)
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and p.page_box = 0
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                     order by p.sort_order, s.pages_title
                                    ');

      $QPage->bindInt(':language_id', (int)$this->lang->getId() );
      $QPage->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID() );

      $QPage->setCache('page_manager_display_footer-lang' . $this->lang->getId());

      $QPage->execute();

      $rows = 0;

//***********************************
// -------- boxe et footer -----------
//***********************************
      $page_liste_footer = '<div class="footerPageManager">';

      while ($QPage->fetch() !== false) {

        $rows++;

        if($rows != 1)  {
          $separation = ' | ';
        } else {
          $separation = '';
        }

        if (!empty($QPage->value('externallink')))  {
          $search = strpos($QPage->value('externallink'), 'index.php');

          if ($search === false) {
            $page_liste_footer .= $separation . HTML::link($QPage->value('externallink'), $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '"', ' class="footerPageManager" rel="noreferrer"');
          } else {
            $page_liste_footer .= $separation. HTML::link(CLICSHOPPING::link($QPage->value('externallink')), $QPage->value('pages_title'), 'class="footerPageManager" target="' . $QPage->value('links_target') . '"');
          }
        } else {
          if ($QPage->valueInt('pages_id') != 3) {
            $link = $this->rewriteUrl->getPageManagerContentUrl($QPage->valueInt('pages_id'));
          } else {
            $link = CLICSHOPPING::link(null, 'Info&Contact');
          }

          if (!empty($QPage->value('pages_html_text'))) {
            $page_liste_footer .= $separation . HTML::link($link, $QPage->value('pages_title'), 'target="' . $QPage->value('links_target') . '"', ' class="footerPageManager" rel="noreferrer"');
          }
        }
      }

      $page_liste_footer .= '</div>';
      $pages = ['text' => $page_liste_footer];

      return $pages['text'];
    }

/**
 * display the content information
 * @param $id
 * @return mixed
 */
    public function pageManagerDisplayInformation($id) {
      $QPage = $this->db->prepare('select p.pages_id,
                                          p.page_type,
                                          s.pages_html_text,
                                          s.pages_title
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and (p.page_type = 3 or p.page_type = 4)
                                     and p.pages_id = :pages_id
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                  ');
      $QPage->bindInt(':pages_id', $id);
      $QPage->bindInt(':language_id', $this->lang->getId());
      $QPage->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $QPage->execute();

      $QPage->setCache('boxe_page_manager_display_information-lang' . $this->lang->getId());

      if ( $QPage->fetch() !== false ) {
        if ($QPage->value('page_type') == 3) {
          CLICSHOPPING::redirect(HTTP::getShopUrlDomain() . 'index.php?Info&Contact');
        } else {
          $pages = ['pages_id' => $QPage->valueInt('pages_id'),
                    'pages_title' => $QPage->value('pages_title'),
                    'pages_html_text' => $QPage->value('pages_html_text')
                   ];
        }
      }  else {
        CLICSHOPPING::redirect(HTTP::getShopUrlDomain() . 'index.php');
      }

      return $pages['pages_html_text'];
    }


/**
 *
 * @param $id
 * @return mixed
 */
    public function pageManagerDisplayTitle($id) {
      $QPage = $this->db->prepare('select p.pages_id,
                                          p.page_type,
                                          s.pages_title
                                     from :table_pages_manager p,
                                          :table_pages_manager_description s
                                     where p.pages_id = s.pages_id
                                     and p.status = 1
                                     and (p.page_type = 3 or p.page_type = 4)
                                     and p.pages_id = :pages_id
                                     and (s.language_id  = :language_id or s.language_id  = 0)
                                     and (p.customers_group_id = :customers_group_id  or p.customers_group_id = 99)
                                  ');
      $QPage->bindInt(':pages_id', $id );
      $QPage->bindInt(':language_id', $this->lang->getId() );
      $QPage->bindInt(':customers_group_id', $this->customer->getCustomersGroupID() );

      $QPage->execute();

      $QPage->setCache('boxe_page_manager_display_title-lang' . $this->lang->getId());

      if ( $QPage->fetch() !== false ) {
        if ($QPage->valueInt('page_type') == 3) {
          CLICSHOPPING::redirect(HTTP::getShopUrlDomain() . 'index.php?Info&Contact');
        } else {
          $pages = ['pages_id' => $QPage->valueInt('pages_id'),
                    'pages_title' => $QPage->value('pages_title')
                   ];
        }
      }

      return $pages['pages_title'];
    }


/**
 * Index and information status
 * @param $pages_id
 * @param $status
 * @return int
 */
    private function setPageManagerStatus($pages_id, $status) {
      if ($status == 1) {

        $Qupdate = $this->db->prepare('update :table_pages_manager
                                        set status = 1,
                                            date_status_change =  now(),
                                            page_date_start = null
                                        where pages_id = :pages_id
                                      ');
        $Qupdate->bindInt(':pages_id', $pages_id);

        $this->getClearCache();

        return $Qupdate->execute();

      } elseif ($status == 0) {

        $Qupdate = $this->db->prepare('update :table_pages_manager
                                        set status = 0,
                                            date_status_change = now(),
                                            page_date_closed = null
                                        where pages_id = :pages_id
                                      ');

        $Qupdate->bindInt(':pages_id', $pages_id);
        $Qupdate->execute();

        $this->getClearCache();

        return $Qupdate->execute();

      } else {
        return -1;
      }
    }

/**
 * Auto activation index and information
 */
    public function  activatePageManager() {

      $QPages = $this->db->query('select pages_id
                                  from :table_pages_manager
                                  where page_date_start is not null
                                  and page_date_start <= now()
                                  and status != 1
                                 ');

      $QPages->execute();

      if ($QPages->fetch() !== false) {
        do {
          $this->setPageManagerStatus($QPages->valueInt('pages_id'), 1);
        } while($QPages->fetch());

        $this->getClearCache();
      }
    }

/**
 * Auto expiration index and information
 */
    public function expirePageManager()  {
      $QPages = $this->db->query('select pages_id
                                    from :table_pages_manager
                                    where status = 1
                                    and page_date_closed is not null
                                    and now() >= page_date_closed
                                  ');

      $QPages->execute();

      if ($QPages->fetch() !== false) {
        do {
          $this->setPageManagerStatus($QPages->valueInt('pages_id'), 0);
        } while($QPages->fetch());

        $this->getClearCache();
      }
    }


/**
 * Get the general condition to include in the order
 *
 * @param string $customer_group, $CLICSHOPPING_Language->getId()
 * @return string page_manager_general_condition, the text of the general condition of sales
 * @access public
 */
    public function pageManagerGeneralCondition() {
      $QpageManagerGeneralGroup = $this->db->prepare('select pages_id,
                                                              customers_group_id
                                                       from :table_pages_manager
                                                       where customers_group_id = 99
                                                       and page_type = 4
                                                       and page_general_condition = 1
                                                       and status = 1
                                                     ');
      $QpageManagerGeneralGroup->bindInt(':customers_group_id', $this->customer->getCustomersGroupID() );
      $QpageManagerGeneralGroup->execute();

      if ($QpageManagerGeneralGroup->valueInt('customers_group_id') == 99) {
        $QpageManagerGeneralCondition = $this->db->prepare('select pmd.pages_html_text,
                                                                    pm.customers_group_id
                                                           from :table_pages_manager pm,
                                                                :table_pages_manager_description pmd
                                                           where  pm.customers_group_id = 99
                                                           and pm.page_general_condition = 1
                                                           and pm.page_type = 4
                                                           and pm.pages_id = :pages_id
                                                           and pmd.language_id = :language_id
                                                           and pmd.pages_id = pm.pages_id
                                                           and pm.status = 1
                                                           limit 1
                                                          ');
        $QpageManagerGeneralCondition->bindInt(':language_id', $this->lang->getId() );
        $QpageManagerGeneralCondition->bindInt(':pages_id', $QpageManagerGeneralGroup->valueInt('pages_id')  );

        $QpageManagerGeneralCondition->execute();

      } else {

        $QpageManagerGeneralGroup = $this->db->prepare('select pages_id
                                                         from :table_pages_manager
                                                         where customers_group_id <> 99
                                                         and page_type = 4
                                                         and page_general_condition = 1
                                                         and status = 1
                                                       ');
        $QpageManagerGeneralGroup->bindInt(':customers_group_id', $this->customer->getCustomersGroupID() );
        $QpageManagerGeneralGroup->execute();

        if ($this->customer->getCustomersGroupID() == 0) {
          $QpageManagerGeneralCondition = $this->db->prepare('select pmd.pages_html_text
                                                               from :table_pages_manager pm,
                                                                    :table_pages_manager_description pmd
                                                               where  pm.customers_group_id = 0
                                                               and pm.page_general_condition = 1
                                                               and pm.page_type = 4
                                                               and pm.pages_id = :pages_id
                                                               and pmd.language_id = :language_id
                                                               and pmd.pages_id = pm.pages_id
                                                               and pm.status = 1
                                                               limit 1
                                                              ');
          $QpageManagerGeneralCondition->bindInt(':language_id', $this->lang->getId() );
          $QpageManagerGeneralCondition->bindInt(':pages_id', $QpageManagerGeneralGroup->valueInt('pages_id') );


          $QpageManagerGeneralCondition->execute();

        } else {

          $QpageManagerGeneralCondition = $this->db->prepare('select pmd.pages_html_text,
                                                                      pm.customers_group_id
                                                               from :table_pages_manager pm,
                                                                    :table_pages_manager_description pmd
                                                               where  pm.customers_group_id = :customers_group_id
                                                               and pm.page_general_condition = 1
                                                               and pm.page_type = 4
                                                               and pm.pages_id = :pages_id
                                                               and pmd.language_id = :language_id
                                                               and pmd.pages_id = pm.pages_id
                                                               and pm.status = 1
                                                               limit 1
                                                              ');
          $QpageManagerGeneralCondition->bindInt(':language_id', $this->lang->getId() );
          $QpageManagerGeneralCondition->bindInt(':customers_group_id', $this->customer->getCustomersGroupID() );
          $QpageManagerGeneralCondition->bindInt(':pages_id', $QpageManagerGeneralGroup->valueInt('pages_id') );

          $QpageManagerGeneralCondition->execute();
        }
      }

      if ($QpageManagerGeneralCondition->fetch() !== false) {
        if (!empty($QpageManagerGeneralCondition->value('pages_html_text'))) {
          $general_condition =  $QpageManagerGeneralCondition->value('pages_html_text');
        }
      } else {
        $general_condition =  '';
      }

      return $general_condition;
    }

/**
 * clear cache
 */
    private function getClearCache() {
      Cache::clear('boxe_page_manager_primary-lang');
      Cache::clear('boxe_page_manager_secondary-lang');
      Cache::clear('page_manager_display_header_menu-lang');
      Cache::clear('page_manager_display_footer_menu-lang');
      Cache::clear('page_manager_display_footer-lang');
      Cache::clear('boxe_page_manager_display_information-lang');
      Cache::clear('boxe_page_manager_display_title-lang');
    }

  }
