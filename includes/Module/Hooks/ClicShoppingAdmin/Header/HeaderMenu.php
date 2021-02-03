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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ErrorHandler;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
  use ClicShopping\Apps\Tools\WhosOnline\Classes\ClicShoppingAdmin\WhosOnlineAdmin;
  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  class HeaderMenu
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  
      $output = '';
      
      if (isset($_SESSION['admin']) && VERTICAL_MENU_CONFIGURATION == 'false') {
        $Qmenus = AdministratorMenu::getHeaderMenu();
  
        $menu_parent = [];
        $menu_sub = [];
  
        foreach ($Qmenus as $menus) {
          if ($menus['parent_id'] == 0) {
            $menu_parent[$menus['id']] = $menus;
          } else {
            if (isset($menu_parent[$menus['parent_id']]) && !is_null($menu_parent[$menus['parent_id']])) {
              $menu_parent[$menus['parent_id']]['sub_menu'][$menus['id']] = $menus['id'];
              $menu_sub[$menus['id']] = $menus;
            } elseif (isset($menu_sub[$menus['parent_id']]) && !is_null($menu_sub[$menus['parent_id']])) {
              $menu_sub[$menus['parent_id']]['sub_menu'][$menus['id']] = $menus['id'];
              $menu_sub[$menus['id']] = $menus;
            }
          }
        }
  
        $output .= '<!-- Start Header Menu -->' . "\n";
        $output .= '<div class="headerFond">
          <span class="headerLogo">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/logo_clicshopping.webp', 'ClicShopping', '166', '55') . '</span>
          <span class="infoHeader">
        ';
  
        if (isset($_SESSION['admin'])) {
          if ($_SESSION['admin']['access'] == 1 && \count(glob(ErrorHandler::getDirectory() . 'errors-*.txt', GLOB_NOSORT)) > 0) {
            $output .= '<span>' . HTML::link(CLICSHOPPING::link(null, 'A&Tools\EditLogError&LogError'), '<i class="bi bi-exclamation-circle-fill text-warning"></i>') . '</span>';
          }
    
          $output .= '<span>' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/administrateur.gif', CLICSHOPPING::getDef('text_header_user_administrator'), '16', '16') . '</span>';
          $output .= '
          <span class="menuJSCookTexte">' . (isset($_SESSION['admin']) ? '&nbsp;' . AdministratorAdmin::getUserAdmin() . '&nbsp; - &nbsp;<a href="' . CLICSHOPPING::link('login.php', 'action=logoff') . '" class="headerLink"><i class="bi bi-power" aria-hidden="true"></i></a>' : '') . ' &nbsp;&nbsp;</span>
          <span class="InfosHeaderWhoOnline">' . (isset($_SESSION['admin']) ? HTML::link(CLICSHOPPING::link(null, 'A&Tools\WhosOnline&WhosOnline'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'header/clients.gif', CLICSHOPPING::getDef('text_header_online_customers'), '16', '16')) : '') . '</span>
          <span class="menuJSCookTexte InfosHeaderWhoOnline">' . (isset($_SESSION['admin']) ? '&nbsp;' . CLICSHOPPING::getDef('text_header_number_of_customers', ['online_customer' => WhosOnlineAdmin::getCountWhosOnline()]) . '&nbsp;&nbsp;' : '') . '</span>
          ';
        }
  
        $output .= '</span>';
        $output .= '</div>';
        $output .= '<div class="headerLine"></div>';
        $output .= '<div class="backgroundMenu">';
        $output .= '<span class="float-start">';
        $output .= '<nav class="main-nav" role="navigation">';
        $output .= '<input id="main-menu-state" type="checkbox"/>';
        $output .= '<label class="main-menu-btn" for="main-menu-state">';
        $output .= '<span class="main-menu-btn-icon"></span>';
        $output .= '</label>';
  
        $output .= '<ul id="main-menu" class="sm sm-mint">';
  
        // level 1
        foreach ($menu_parent as $key => $menus) {
          $image = '';
    
          if ($menus['link'] != '') {
            $output .= '<li>' . HTML::link(CLICSHOPPING::link($menus['link']), $menus['label']) . '</li>';
          } else {
            $output .= '<li><a class="nav-link">' . $image . ' ' . $menus['label'] . '</a>';
          }
    
          //--------------------------------------------------------------
          // level 2
          if (isset($menus['sub_menu'])) {
            $output .= '<ul>';
            foreach ($menus['sub_menu'] as $second_level) {
              $image = '';
        
              if ($menu_sub[$second_level]['link'] != '') {
                $output .= '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$second_level]['link']), $image . ' ' . $menu_sub[$second_level]['label']) . '</li>';
              } else {
                $output .= '<li class="sub_menu_1st_level"><a class="nav-link">' . $image . ' ' . $menu_sub[$second_level]['label'] . '</a>';
              }
              //--------------------------------------------------------------
              // level 3
              if (isset($menu_sub[$second_level]['sub_menu'])) {
                $output .= '<ul>';
          
                foreach ($menu_sub[$second_level]['sub_menu'] as $third_level) {
                  $image = '';
            
                  if (!is_null($menu_sub[$third_level]['link'])) {
                    $output .= '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$third_level]['link']), $image . ' ' . $menu_sub[$third_level]['label']) . '</li>';
                  } else {
                    $output .= '<li><a class="nav-link">' . $image . ' ' . $menu_sub[$third_level]['label'] . '</a>';
                  }
            
                  //--------------------------------------------------------------
                  // level 4
                  if (isset($menu_sub[$third_level]['sub_menu'])) {
                    //              $output .= '<ul>';
              
                    foreach ($menu_sub[$third_level]['sub_menu'] as $fourth_level) {
                      $image = '';
                
                      if (!is_null($menu_sub[$fourth_level]['link'])) {
                        $output .= '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$fourth_level]['link']), $image . ' ' . $menu_sub[$fourth_level]['label']) . '</li>';
                      } else {
                  
                        $output .= '<li><a class="nav-link">' . $image . ' ' . $menu_sub[$fourth_level]['label'] . '</a>';
                      }
                    }
              
                    if (is_null($menu_sub[$fourth_level]['link'])) {
                      $output .= '</li>';
                    }
                    //              $output .= '</ul>';
                  }
            
                  //--------------------------------------------------------------
                  if (is_null($menu_sub[$third_level]['link'])) {
                    $output .= '</li>';
                  }
                }
          
                $output .= '</ul>'; //énd menu
              }
        
              if (is_null($menu_sub[$second_level]['link'])) {
                $output .= '</li>';
              }
            }
            $output .= '</ul>';
          }
          if ($menus['link'] != '') {
            $output .= '</li>';
          }
        }
  
        $output .= '</ul>';
        $output .= '</nav>';
        $output .= '</span>';
        $output .= '</div>';
        $output .= '<div class="clearfix"></div>';
        $output .= '<!-- End Header Menu -->' . "\n";
      }
      
        return $output;
    }
  }