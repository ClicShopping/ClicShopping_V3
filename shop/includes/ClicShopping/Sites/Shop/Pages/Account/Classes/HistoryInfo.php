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

  namespace ClicShopping\Sites\Shop\Pages\Account\Classes;

  use ClicShopping\OM\Registry;

  class HistoryInfo {

    public static function getHistoryInfoCheck() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->prepare('select o.customers_id
                                           from :table_orders o,
                                                :table_orders_status s
                                           where o.orders_id = :orders_id
                                           and o.orders_status = s.orders_status_id
                                           and s.language_id = :language_id
                                           and s.public_flag = 1
                                          ');
      $Qcheck->bindInt(':orders_id', (int)$_GET['order_id']);
      $Qcheck->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

      $Qcheck->execute();

      $check = $Qcheck->fetch();

      return $check;
    }

    public static function getHistoryInfoCount() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcount = $CLICSHOPPING_Db->prepare('select count(orders_status_id) as count
                                           from :table_orders_status_history
                                           where orders_id = :orders_id
                                          ');
      $Qcount->bindInt(':orders_id', $_GET['order_id']);

      $Qcount->execute();

      $count = $Qcount->valueInt('count');

      return $count;
    }

    public static function getDisplayHistoryInfoSupport() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qsupport = $CLICSHOPPING_Db->prepare('select support_orders_flag
                                              from :table_orders_status
                                              where support_orders_flag = 0
                                              and orders_status_id = 5
                                             ');

      $Qsupport->execute();

      $support = $Qsupport->valueInt('support_orders_flag');

      return $support;
    }

    public static function getHistoryInfoDisplay() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qstatuse = $CLICSHOPPING_Db->prepare('select distinct os.orders_status_name,
                                                              osh.date_added,
                                                              osh.comments,
                                                              osh.orders_tracking_number,
                                                              osh.orders_status_support_id
                                            from :table_orders_status os,
                                                 :table_orders_status_history osh
                                            where osh.orders_id = :orders_id
                                            and osh.orders_status_id = os.orders_status_id
                                            and os.language_id = :language_id
                                            and os.public_flag = 1
                                            order by osh.date_added
                                            ');
      $Qstatuse->bindInt(':orders_id', $_GET['order_id']);
      $Qstatuse->bindInt(':language_id', $CLICSHOPPING_Language->getId());

      $Qstatuse->execute();

      $statuse = $Qstatuse->fetchAll();

      return $statuse;
    }

    public static function getHistoryInfoDownloadFiles() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QdonwloadProductsFiles = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                            p.products_download_filename,
                                                                            op.products_id,
                                                                            op.orders_id,
                                                                            o.orders_id
                                                             from :table_products p,
                                                                  :table_orders_products  op,
                                                                  :table_orders o,
                                                                  :table_orders_status os
                                                             where p.products_id = op.products_id
                                                             and o.orders_id = op.orders_id
                                                             and op.orders_id = :orders_id
                                                             and o.orders_status = 3
                                                            ');
      $QdonwloadProductsFiles->bindInt(':orders_id', $_GET['order_id']);

      $QdonwloadProductsFiles->execute();

      $download = $QdonwloadProductsFiles->fetch();

      return $download;
    }

    public static function getHistoryInfoSupportCustomer($orders_status_support_id) {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QCustomerSupport= $CLICSHOPPING_Db->prepare('select oss.orders_status_support_name
                                                     from :table_orders_status_history osh,
                                                          :table_orders_status_support oss
                                                     where osh.orders_status_support_id = :orders_status_support_id
                                                     and osh.orders_status_support_id = oss.orders_status_support_id
                                                     and oss.language_id = :language_id
                                                     order by osh.date_added desc
                                                     limit 1
                                                    ');

      $QCustomerSupport->bindInt(':orders_status_support_id', $orders_status_support_id);
      $QCustomerSupport->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QCustomerSupport->execute();

      $support = $QCustomerSupport->fetch();

      return $support;
    }


/**
 * Get the tracking number
 *
 * @param string $tracking, $CLICSHOPPING_Language->getId()
 * @return string tracking_url, the url of the tracking
 * @access public
 */
    public static function getTrackingLink() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QordersTracking = $CLICSHOPPING_Db->prepare('select orders_status_tracking_id,
                                                           orders_status_tracking_link
                                                    from :table_orders_status_tracking
                                                    where  language_id = :language_id
                                                  ');

      $QordersTracking->bindInt(':language_id', $CLICSHOPPING_Language->getId() );

      $QordersTracking->execute();
      $orders_tracking = $QordersTracking->fetch();


      if (empty($QordersTracking->value('orders_status_tracking_link')) || (empty($QordersTracking->value('orders_tracking_number')) )) {
        $tracking_url = ' (<a href="http://www.track-trace.com/" target="_blank">http://www.track-trace.com/</a>)';
      } else {
        $tracking_url = ' (<a href="'.$QordersTracking->value('orders_status_tracking_link') .  $QordersTracking->value('orders_tracking_number') .'" target="_blank" rel="noreferrer">' . $orders_tracking->value('orders_status_tracking_link') .  $QordersTracking->value('orders_tracking_number') . '</a>)';
      }
      return $tracking_url;
    }


    public static function getDownloadFilesPurchased() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qdownload = $CLICSHOPPING_Db->prepare('select date_format(o.date_purchased, "%Y-%m-%d") as date_purchased_day,
                                                     opd.download_maxdays,
                                                     opd.download_count,
                                                     opd.download_maxdays,
                                                     opd.orders_products_filename
                                              from :table_orders o,
                                                   :table_orders_products op,
                                                   :table_orders_products_download opd,
                                                   :table_orders_status os
                                              where o.orders_id = :orders_id
                                              and o.customers_id = :customers_id
                                              and o.orders_id = op.orders_id
                                              and op.orders_products_id = opd.orders_products_id
                                              and opd.orders_products_download_id = :orders_products_download_id
                                              and opd.orders_products_filename != ""
                                              and o.orders_status = os.orders_status_id
                                              and os.downloads_flag = "1"
                                              and os.language_id = :language_id
                                            ');
      $Qdownload->bindInt(':orders_id', $_GET['order']);
      $Qdownload->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qdownload->bindInt(':orders_products_download_id', $_GET['id']);
      $Qdownload->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qdownload->execute();

      return $Qdownload;
    }
 }