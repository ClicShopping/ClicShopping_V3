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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (isset($_SESSION['admin']['access'])) {
    if ($_SESSION['admin']['access'] == 1) {
      $access_level = 0;
    } elseif ($_SESSION['admin']['access'] == 2) {
      $access_level = 2;
    } elseif ($_SESSION['admin']['access'] == 3) {
      $access_level = 2;
    } else {
      $access_level = 0;
    }
  } else {
    $access_level = 0;
  }

  if ($access_level == 0) {
      $Qmenus = $CLICSHOPPING_Db->prepare('select am.id,
                                                  am.link,
                                                  am.parent_id,
                                                  am.access,
                                                  am.sort_order,
                                                  am.image,
                                                  am.b2b_menu,
                                                  amd.label,
                                                  ad.access
                                            from :table_administrator_menu am  left join :table_administrators ad on ad.access =  am.access,
                                                :table_administrator_menu_description amd
                                            where am.id = amd.id
                                            and amd.language_id = :language_id
                                            order by am.parent_id,
                                                     am.sort_order
                                            ');
    $Qmenus->bindInt(':language_id', $CLICSHOPPING_Language->getId());

  } elseif ($access_level == 2) {
    $Qmenus = $CLICSHOPPING_Db->prepare('select am.id,
                                                am.link,
                                                am.parent_id,
                                                am.access,
                                                am.sort_order,
                                                am.image,
                                                am.b2b_menu,
                                                amd.label,
                                                ad.access
                                          from :table_administrator_menu am  left join :table_administrators ad on ad.access =  am.access,
                                              :table_administrator_menu_description amd
                                          where am.id = amd.id
                                          and amd.language_id = :language_id
                                          and (am.access = 0 or am.access > 1)
                                          order by am.parent_id,
                                                   am.sort_order
                                          ');

    $Qmenus->bindInt(':language_id', $CLICSHOPPING_Language->getId());

  } elseif ($access_level == 3) {
    $Qmenus = $CLICSHOPPING_Db->prepare('select am.id,
                                                am.link,
                                                am.parent_id,
                                                am.access,
                                                am.sort_order,
                                                am.image,
                                                am.b2b_menu,
                                                amd.label,
                                                ad.access
                                          from :table_administrator_menu am  left join :table_administrators ad on ad.access =  am.access,
                                              :table_administrator_menu_description amd
                                          where am.id = amd.id
                                          and amd.language_id = :language_id
                                          and (am.access = 0 and am.access > 2)
                                          order by am.parent_id,
                                                   am.sort_order
                                          ');

    $Qmenus->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  }

  $Qmenus->setCache('menu-administrator');
  $Qmenus->execute();

  $menu_parent = [];
  $menu_sub = [];

  foreach ($Qmenus as $menus) {
    if ($menus['parent_id'] == 0) {
      $menu_parent[$menus['id']] = $menus;
    } else {
      if (isset($menu_parent[ $menus['parent_id'] ])) {
        $menu_parent[ $menus['parent_id']]['sub_menu'][$menus['id']] = $menus['id'];
        $menu_sub[$menus['id']] = $menus;
      } else if (isset($menu_sub[ $menus['parent_id'] ])) {
        $menu_sub[ $menus['parent_id']]['sub_menu'][$menus['id']] = $menus['id'];
        $menu_sub[$menus['id']] = $menus;
      }
    }
  }
?>

  <div class="headerLine"></div>
  <div class="backgroundMenu">
    <span class="float-md-left">
      <nav class="main-nav" role="navigation">
        <input id="main-menu-state" type="checkbox" />
        <label class="main-menu-btn" for="main-menu-state">
          <span class="main-menu-btn-icon"></span>
        </label>

        <ul id="main-menu" class="sm sm-mint">
<?php
// level 1
  foreach($menu_parent as $key => $menus) {
    $image = '';

    if ($menus['link'] != '') {
      echo '<li>' . HTML::link(CLICSHOPPING::link($menus['link']), $menus['label']) . '</li>';
    } else {
      echo '<li><a class="nav-link">' . $image . ' ' . $menus['label'] . '</a>';
    }

//--------------------------------------------------------------
// level 2
    if (!is_null($menus['sub_menu'])) {
      echo '<ul>';
      foreach($menus['sub_menu'] as $second_level) {
        $image = '';

        if ($menu_sub[$second_level]['link'] != '') {
          echo '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$second_level]['link']),  $image . ' ' . $menu_sub[$second_level]['label']) . '</li>';
        } else {
          echo '<li class="sub_menu_1st_level"><a class="nav-link">' . $image . ' ' . $menu_sub[$second_level]['label'] . '</a>';
        }
//--------------------------------------------------------------
// level 3
        if ($menu_sub[$second_level]['sub_menu'] != '')  {
          echo '<ul>';

          foreach($menu_sub[$second_level]['sub_menu'] as $third_level) {
            $image = '';

            if (!is_null($menu_sub[$third_level]['link'])) {
              echo '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$third_level]['link']), $image . ' ' . $menu_sub[$third_level]['label']) . '</li>';
            } else {
              echo '<li><a class="nav-link">' . $image . ' ' . $menu_sub[$third_level]['label'] . '</a>';
            }

//--------------------------------------------------------------
// level 4
            if ($menu_sub[ $third_level]['sub_menu'] != '')  {
//              echo '<ul>';

              foreach($menu_sub[$third_level]['sub_menu'] as $fourth_level) {
                $image = '';

                if (!is_null($menu_sub[$fourth_level]['link'])) {
                  echo '<li>' . HTML::link(CLICSHOPPING::link($menu_sub[$fourth_level]['link']), $image . ' ' . $menu_sub[$fourth_level]['label']) . '</li>';
                }else {

                  echo '<li><a class="nav-link">' . $image . ' ' . $menu_sub[$fourth_level]['label'] . '</a>';
                }
              }
              if (is_null($menu_sub[$fourth_level]['link'])) {
                echo '</li>';
              }
//              echo '</ul>';
            }

//--------------------------------------------------------------

            if (is_null($menu_sub[$third_level]['link'])) {
              echo '</li>';
            }
          }
          echo '</ul>';
        }
        if (is_null($menu_sub[$second_level]['link'])) {
          echo '</li>';
        }
      }
      echo '</ul>';
    }
    if ($menus['link'] != '') {
      echo '</li>';
    }
  }
?>
      </ul>
    </nav>
  </span>
</div>
<div class="clearfix"></div>