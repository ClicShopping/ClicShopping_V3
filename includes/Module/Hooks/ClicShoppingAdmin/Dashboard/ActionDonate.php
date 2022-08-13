<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;

  class ActionDonate
  {

    public function __construct()
    {

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin') {
        CLICSHOPPING::redirect();
      }
    }

    public function execute()
    {
      $output = '<div class="separator"></div>
                 <div class="text-center">
                   <span class="badge bg-light">
                     <a href="https://www.clicshopping.org/forum/clients/donations/" rel="noreferrer" target="_blank">' . CLICSHOPPING::getDef('text_donate') . '</a>
                   </span>
                 </div>
                 <div class="separator"></div>
                 <div class="text-center">' . CLICSHOPPING::getDef('text_follow_us') . ' <a href="https://twitter.com/clicshopping" rel="noreferrer" target="_blank"> Twitter</a> | <a href="https://www.facebook.com/Clicshopping-583928135031577/" rel="noreferrer" target="_blank">Facebook</a></div>
                ';

      return $output;
    }
  }