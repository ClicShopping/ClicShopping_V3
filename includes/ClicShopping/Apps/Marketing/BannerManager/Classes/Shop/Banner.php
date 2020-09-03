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

  namespace ClicShopping\Apps\Marketing\BannerManager\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  class Banner
  {

    /**
     * Sets the status of a banner
     *
     * @param int $id The $banners_id of the banner to set the status to
     * @param boolean $active_flag A flag that enables or disables the banner
     * @access private
     * @return boolean
     */

    private static function setBannerStatus($banners_id, $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == '1') {

        return $CLICSHOPPING_Db->save('banners', ['status' => 1,
          'date_status_change' => 'now()',
          'date_scheduled' => 'null'],
          ['banners_id' => (int)$banners_id]
        );


      } elseif ($status == '0') {
        return $CLICSHOPPING_Db->save('banners', ['status' => 0,
          'date_status_change' => 'now()'],
          ['banners_id' => (int)$banners_id]
        );
      } else {
        return -1;
      }
    }

    /**
     * Activate a banner that has been on schedule
     *
     * @param int $id The ID of the banner to activate
     *
     * @return boolean
     */
    public static function activateBanners()
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
     * Deactivate a banner
     *
     * @param int $id The ID of the banner to deactivate
     *
     * @return boolean
     */
    public static function expireBanners()
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
     * Display a banner. If no ID is passed, the value defined in $_exists_id is
     * used.
     *
     * @param int $action of the banner (dynamic or static)
     * @param The $identifier of the banner to show
     *
     * @return string
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
                                                          languages_id
                                                 from :table_banners
                                                 where banners_group = :banners_group
                                                 and status = 1
                                                 and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 order by rand()
                                                 limit 1
                                              ');

            $Qbanner->bindValue(':banners_group', $identifier);
            $Qbanner->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
            $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
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
                                                          languages_id
                                                 from :table_banners
                                                 where banners_group = :banners_group
                                                 and status = 1
                                                 and (customers_group_id = 0 or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 order by rand()
                                                 limit 1
                                              ');


            $Qbanner->bindValue(':banners_group', 'Developpement_shopping_cart');
            $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());

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
                                                         languages_id
                                                 from :table_banners
                                                 where status = 1
                                                 and banners_group = :banners_group
                                                 and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 limit 1
                                               ');
            $Qbanner->bindValue(':banners_group', $identifier);
            $Qbanner->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
            $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
            $Qbanner->execute();

          } else {

            $Qbanner = $CLICSHOPPING_Db->prepare('select banners_id,
                                                         banners_title,
                                                         banners_image,
                                                         banners_target,
                                                         banners_html_text,
                                                         customers_group_id,
                                                         languages_id
                                                 from :table_banners
                                                 where status = 1
                                                 and banners_group = :banners_group
                                                 and (customers_group_id = 0 or customers_group_id = 99)
                                                 and (languages_id  = :languages_id or languages_id = 0)
                                                 limit 1
                                               ');
            $Qbanner->bindValue(':banners_group', $identifier);
            $Qbanner->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());
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
            $output = HTML::link(CLICSHOPPING::link('redirect.php', 'action=banner&goto=' . (int)$banner['banners_id']) . '" target="' . $banner['banners_target'], HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $banner['banners_image'], HTML::outputProtected($banner['banners_title'])));
          }
        }

        if (is_numeric($banner['banners_id'])) {
          static::updateBannerDisplayCount($banner['banners_id']);
        }
      }

      return $output;
    }


    /**
     * Check if an existing banner is active
     *
     * @param int $id The ID of the banner to check
     *
     * @return boolean
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
                                                       customers_group_id
                                               from :table_banners
                                               where banners_group = :banners_group
                                               and status = 1
                                               and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                               and (languages_id = :languages_id or languages_id = 0)
                                               order by rand()
                                               limit 1
                                              ');

          $Qbanners->bindValue(':banners_group', $identifier);
          $Qbanners->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
          $Qbanners->bindInt(':languages_id', $CLICSHOPPING_Language->getId());

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
                                                       customers_group_id
                                               from :table_banners
                                               where banners_group = :banners_group
                                               and status = 1
                                               and (customers_group_id = 0 or customers_group_id = 99)
                                               and (languages_id = :languages_id or languages_id = 0)
                                               order by rand()
                                               limit 1
                                              ');

          $Qbanners->bindValue(':banners_group', $identifier);
          $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());

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
                                                       languages_id
                                                from :table_banners
                                                where status = 1
                                                and banners_group = :banners_group
                                                and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                and (languages_id = :languages_id or languages_id = 0)
                                              ');


          $Qbanners->bindValue(':banners_group', $identifier);
          $Qbanners->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
          $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());

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
                                                       languages_id
                                                  from :table_banners
                                                  where status = 1
                                                  and banners_group = :banners_group
                                                  and (customers_group_id = 0 or customers_group_id = 99 )
                                                  and (languages_id = :languages_id or languages_id = 0)
                                                ');

          $Qbanners->bindValue(':banners_group', $identifier);
          $Qbanners->bindInt(':languages_id', (int)$CLICSHOPPING_Language->getId());

          $Qbanners->execute();

          $result = $Qbanners->toArray();

          return $result;
        }
      }
    }


    /**
     * Increment the display count of the banner
     *
     * @param int $id The ID of the banner
     * @access private
     */
    private static function updateBannerDisplayCount($banner_id)
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
     * Increment the click count of the banner
     *
     * @param int $banner_id The ID of the banner
     * @access private
     */

    public static function updateBannerClickCount($banner_id)
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