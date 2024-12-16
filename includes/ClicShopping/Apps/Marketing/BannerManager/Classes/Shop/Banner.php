<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\BannerManager\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function is_array;

class Banner
{
  /**
   * Update the status of the banner
   *
   * @param int $banners_id The ID of the banner
   * @param int $status The new status of the banner (1 for active, 0 for inactive)
   * @access private
   */

  private static function setBannerStatus(int $banners_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == '1') {
      $insert_array = [
        'status' => 1,
        'date_status_change' => 'now()',
        'date_scheduled' => 'null'
      ];

      return $CLICSHOPPING_Db->save('banners', $insert_array, ['banners_id' => (int)$banners_id]);


    } elseif ($status == '0') {
      $insert_array = [
        'status' => 0,
        'date_status_change' => 'now()'
      ];

      return $CLICSHOPPING_Db->save('banners', $insert_array, ['banners_id' => (int)$banners_id]);
    } else {
      return -1;
    }
  }

  /**
   * Activate banners that are scheduled and not currently active
   *
   * This method checks for banners with a scheduled activation date
   * that has passed and updates their status to active.
   *
   * @return void
   * @access public
   */
  public static function activateBanners(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qbanners = $CLICSHOPPING_Db->query('select banners_id
                                            from :table_banners
                                            where date_scheduled is not null
                                            and date_scheduled <= now()
                                            and status <> 1
                                           ');

    $Qbanners->execute();

    if ($Qbanners->fetch() !== false) {
      do {
        static::setBannerStatus($Qbanners->valueInt('banners_id'), 1);
      } while ($Qbanners->fetch());
    }
  }

  /**
   * Expires banners based on their expiration date or the number of impressions
   *
   * Checks all active banners and deactivates them if:
   * - The current date is equal to or exceeds their expiration date.
   * - The total number of impressions has reached or surpassed the set limit.
   *
   * @return void
   * @access private
   */
  public static function expireBanners(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qbanners = $CLICSHOPPING_Db->query('select b.banners_id,
                                                  sum(bh.banners_shown) as banners_shown
                                            from :table_banners b,
                                                 :table_banners_history bh
                                            where b.status = 1
                                            and b.banners_id = bh.banners_id
                                            and ((b.expires_date is not null and now() >= b.expires_date)
                                                 or (b.expires_impressions >= banners_shown)
                                                )
                                            group by b.banners_id
                                          ');

    $Qbanners->execute();

    if ($Qbanners->fetch() !== false) {
      do {
        static::setBannerStatus($Qbanners->valueInt('banners_id'), 0);
      } while ($Qbanners->fetch());
    }
  }

  /**
   * Displays a banner based on the specified action and identifier.
   *
   * @param string $action The type of banner retrieval ('dynamic' or 'static').
   * @param mixed $identifier The banner identifier, could be a group name or an array containing banner details.
   * @return string The rendered HTML output of the banner, or an empty string if no banner is available.
   * @access public
   */
  public static function displayBanner($action, $identifier)
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Template = Registry::get('Template');

    $banner = null;

    if ($action == 'dynamic') {
      $Qcheck = $CLICSHOPPING_Db->prepare('select banners_id
                                             from :table_banners
                                             where banners_group = :banners_group
                                             and status = 1
                                             limit 1
                                             ');
      $Qcheck->bindValue(':banners_group', $identifier);
      $Qcheck->execute();

      if ($Qcheck !== false) {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != '0') {
          $Qbanner = $CLICSHOPPING_Db->prepare('select  banners_id,
                                                          banners_title,
                                                          banners_image,
                                                          banners_target,
                                                          banners_html_text,
                                                          customers_group_id,
                                                          banners_group,
                                                          languages_id,
                                                          banners_theme
                                                 from :table_banners
                                                 where banners_group = :banners_group
                                                 and status = 1
                                                 and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 and (banners_theme = :banners_theme or banners_theme is null)
                                                 order by rand()
                                                 limit 1
                                              ');
          $Qbanner->bindValue(':banners_group', $identifier);
          $Qbanner->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
          $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
          $Qbanner->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));
          $Qbanner->execute();

          $banner = $Qbanner->fetch();
        } else {
          $Qbanner = $CLICSHOPPING_Db->prepare('select  banners_id,
                                                          banners_title,
                                                          banners_image,
                                                          banners_target,
                                                          banners_html_text,
                                                          customers_group_id,
                                                          banners_group,
                                                          languages_id,
                                                          banners_theme
                                                 from :table_banners
                                                 where banners_group = :banners_group
                                                 and status = 1
                                                 and (customers_group_id = 0 or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 and (banners_theme = :banners_theme or banners_theme is null)
                                                 order by rand()
                                                 limit 1
                                              ');

          $Qbanner->bindValue(':banners_group', $identifier);
          $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
          $Qbanner->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));
          $Qbanner->execute();

          $banner = $Qbanner->fetch();
        }
      }
    } elseif ($action == 'static') {
      if (is_array($identifier)) {
        $banner = $identifier;
      } else {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
          $Qbanner = $CLICSHOPPING_Db->prepare('select banners_id,
                                                         banners_title,
                                                         banners_image,
                                                         banners_target,
                                                         banners_html_text,
                                                         customers_group_id,
                                                         languages_id,
                                                         banners_theme
                                                 from :table_banners
                                                 where status = 1
                                                 and banners_group = :banners_group
                                                 and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 and (banners_theme = :banners_theme or banners_theme is null)
                                                 limit 1
                                               ');
          $Qbanner->bindValue(':banners_group', $identifier);
          $Qbanner->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
          $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
          $Qbanner->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

          $Qbanner->execute();
        } else {
          $Qbanner = $CLICSHOPPING_Db->prepare('select banners_id,
                                                         banners_title,
                                                         banners_image,
                                                         banners_target,
                                                         banners_html_text,
                                                         customers_group_id,
                                                         languages_id,
                                                         banners_theme
                                                 from :table_banners
                                                 where status = 1
                                                 and banners_group = :banners_group
                                                 and (customers_group_id = 0 or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 and (banners_theme = :banners_theme  or banners_theme is null)
                                                 limit 1
                                               ');
          $Qbanner->bindValue(':banners_group', $identifier);
          $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
          $Qbanner->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

          $Qbanner->execute();
        }

        $banner = $Qbanner->toArray();
      }
    }

    $output = '';

    if (is_array($banner)) {
      if (!empty($banner['banners_html_text'])) {
        $output = $banner['banners_html_text'];
      } else {
        if (is_numeric($banner['banners_id'])) {
          $output = HTML::link(CLICSHOPPING::link('redirect.php', 'action=banner&goto=' . (int)$banner['banners_id'], true, false) . '" target="' . $banner['banners_target'], HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $banner['banners_image'], HTML::outputProtected($banner['banners_title'])));
        }
      }

      if (is_numeric($banner['banners_id'])) {
        static::updateBannerDisplayCount($banner['banners_id']);
      }
    }

    return $output;
  }

  /**
   * Checks if a banner exists based on the specified type of action (dynamic or static) and its identifier.
   *
   * @param string $action The type of action to determine the query (dynamic|static).
   * @param string $identifier The identifier or group name of the banner.
   *
   * @return array|false Returns banner details as an associative array if found, or false if no banner matches.
   */

  public static function bannerExists($action, $identifier)
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    if ($action == 'dynamic') {
      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qbanners = $CLICSHOPPING_Db->prepare('select banners_id,
                                                        banners_title,
                                                        banners_image,
                                                        banners_target,
                                                        banners_html_text,
                                                        languages_id,
                                                        customers_group_id,
                                                        banners_theme
                                               from :table_banners
                                               where banners_group = :banners_group
                                               and status = 1
                                               and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                               and (languages_id = :languages_id or languages_id = 0)
                                               and (banners_theme = :banners_theme or banners_theme is null)
                                               order by rand()
                                               limit 1
                                              ');

        $Qbanners->bindValue(':banners_group', $identifier);
        $Qbanners->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
        $Qbanners->bindInt(':languages_id', $CLICSHOPPING_Language->getId());
        $Qbanners->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

        $Qbanners->execute();

        $result = $Qbanners->toArray();

        return $result;
      } else {
        $Qbanners = $CLICSHOPPING_Db->prepare('select banners_id,
                                                        banners_title,
                                                        banners_image,
                                                        banners_target,
                                                        banners_html_text,
                                                        languages_id,
                                                        customers_group_id,
                                                        banners_theme
                                               from :table_banners
                                               where banners_group = :banners_group
                                               and status = 1
                                               and (customers_group_id = 0 or customers_group_id = 99)
                                               and (languages_id = :languages_id or languages_id = 0)
                                               and (banners_theme = :banners_theme or banners_theme is null)
                                               order by rand()
                                               limit 1
                                              ');

        $Qbanners->bindValue(':banners_group', $identifier);
        $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
        $Qbanners->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

        $Qbanners->execute();

        $result = $Qbanners->toArray();

        return $result;
      }
    } elseif ($action == 'static') {
      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qbanners = $CLICSHOPPING_Db->prepare('select banners_id,
                                                       banners_title,
                                                       banners_image,
                                                       banners_target,
                                                       banners_html_text,
                                                       customers_group_id,
                                                       languages_id,
                                                       banners_theme
                                                from :table_banners
                                                where status = 1
                                                and banners_group = :banners_group
                                                and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                and (languages_id = :languages_id or languages_id = 0)
                                                and (banners_theme = :banners_theme  or banners_theme is null)
                                              ');

        $Qbanners->bindValue(':banners_group', $identifier);
        $Qbanners->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
        $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
        $Qbanners->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

        $Qbanners->execute();

        $result = $Qbanners->toArray();

        return $result;
      } else {
        $Qbanners = $CLICSHOPPING_Db->prepare('select banners_id,
                                                       banners_title,
                                                       banners_image,
                                                       banners_target,
                                                       banners_html_text,
                                                       customers_group_id,
                                                       languages_id,
                                                       banners_theme
                                                  from :table_banners
                                                  where status = 1
                                                  and banners_group = :banners_group
                                                  and (customers_group_id = 0 or customers_group_id = 99 )
                                                  and (languages_id = :languages_id or languages_id = 0)
                                                  and (banners_theme = :banners_theme  or banners_theme is null)
                                                ');

        $Qbanners->bindValue(':banners_group', $identifier);
        $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
        $Qbanners->bindValue(':banners_theme', HTML::sanitize(SITE_THEMA));

        $Qbanners->execute();

        $result = $Qbanners->toArray();

        return $result;
      }
    }
  }

  /**
   * Updates the display count of a banner for the current day.
   * If an entry for the specified banner and date already exists, the display count is incremented.
   * Otherwise, a new entry is created with an initial display count of 1.
   *
   * @param int $banner_id The unique identifier of the banner for which the display count should be updated.
   * @return void
   */
  private static function updateBannerDisplayCount(int $banner_id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_numeric($banner_id)) {
      $Qcheck = $CLICSHOPPING_Db->prepare('select banners_history_id
                                              from :table_banners_history
                                              where banners_id = :banners_id
                                              and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")
                                              limit 1
                                             ');

      $Qcheck->bindInt(':banners_id', $banner_id);
      $Qcheck->execute();

      $count = $Qcheck->rowCount();

      if ($Qcheck->fetch() !== false) {
        if ($count > 0) {
          $Qview = $CLICSHOPPING_Db->prepare('update :table_banners_history
                                                set banners_shown = banners_shown + 1
                                                where banners_id = :banners_id
                                                and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")
                                                ');
          $Qview->bindInt(':banners_id', $banner_id);
          $Qview->execute();
        } else {
          $Qbanner = $CLICSHOPPING_Db->prepare('insert into :table_banners_history (banners_id,
                                                                                      banners_shown,
                                                                                      banners_history_date)
                                                  values (:banners_id,
                                                          1, now()
                                                          )
                                                ');
          $Qbanner->bindInt(':banners_id', $banner_id);
          $Qbanner->execute();
        }
      }
    }
  }

  /**
   * Updates the click count for a specific banner, either incrementing the count
   * for the current date or creating a new entry if none exists for the banner on the current date.
   *
   * @param int $banner_id The ID of the banner for which the click count is being updated.
   * @return void
   */
  public static function updateBannerClickCount(int $banner_id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->prepare('select count(*) as count
                                            from :table_banners_history where banners_id = :banners_id
                                            and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")
                                           ');
    $Qcheck->bindInt(':banners_id', $banner_id);
    $Qcheck->execute();

    if (($Qcheck->fetch() !== false) && ($Qcheck->value('count') > 0)) {

      $Qbanner = $CLICSHOPPING_Db->prepare('update :table_banners_history
                                              set banners_clicked = banners_clicked + 1
                                              where banners_id = :banners_id
                                              and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")
                                             ');
    } else {
      $Qbanner = $CLICSHOPPING_Db->prepare('insert into :table_banners_history (banners_id,
                                                                            banners_clicked,
                                                                            banners_history_date)
                                              values (:banners_id,
                                                      1,
                                                      now()
                                                     )
                                             ');
    }

    $Qbanner->bindInt(':banners_id', $banner_id);
    $Qbanner->execute();
  }
}