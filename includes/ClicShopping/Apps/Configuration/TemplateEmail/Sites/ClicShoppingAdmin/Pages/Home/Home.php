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

  namespace ClicShopping\Apps\Configuration\TemplateEmail\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\TemplateEmail\TemplateEmail;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_TemplateEmail = new TemplateEmail();
      Registry::set('TemplateEmail', $CLICSHOPPING_TemplateEmail);

      $this->app = $CLICSHOPPING_TemplateEmail;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
