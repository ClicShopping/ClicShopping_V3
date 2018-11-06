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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;

  class ActionDonate {

    public function __construct() {

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }
    }

    public function execute() {
      $output = '<div class="separator"></div>
                 <div class="text-md-center">
                   <span class="badge badge-light">
                     <a href="https://www.clicshopping.org/forum/clients/donations/" rel="nofollow" target="_blank">' . CLICSHOPPING::getDef('text_donate') . '</a>
                   </span>
                 </div>
                 <div class="separator"></div>
                 <div class="text-md-center">' . CLICSHOPPING::getDef('text_follow_us') . ' <a href="https://twitter.com/clicshopping" rel="nofollow" target="_blank"> Twitter</a> | <a href="https://www.facebook.com/Clicshopping-583928135031577/" rel="nofollow" target="_blank">Facebook</a></div>
                ';

      return $output;
    }
  }