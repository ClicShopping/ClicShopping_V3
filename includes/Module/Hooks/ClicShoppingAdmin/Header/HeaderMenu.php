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

  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  class HeaderMenu
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

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

      $output = '<!-- Start Header Menu -->' . "\n";
      $output .= '<div class="headerLine"></div>';
      $output .= '<div class="backgroundMenu">';
      $output .= '<span class="float-md-left">';
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

      return $output;
    }
  }