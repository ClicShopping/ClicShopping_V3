<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sites\Shop\Pages\Token;

use ClicShopping\Apps\Configuration\Api\Classes\Shop\Login;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Token extends \ClicShopping\OM\PagesAbstract
{
  protected ?string $file = null;
  protected bool $use_site_template = false;
  private mixed $lang;
  private mixed $db;

  public function init()
  {
    if (!\defined('CLICSHOPPING_APP_API_AI_STATUS') && CLICSHOPPING_APP_API_AI_STATUS == 'False') {
      return false;
    }

    $username = HTML::sanitize($_POST['username']);
    $key = HTML::sanitize($_POST['key']);

    Registry::set('Login', new Login($username, $key, ''));
    $CLICSHOPPING_login = Registry::get('Login');

    $token = $CLICSHOPPING_login->getLogin();

    echo $token;

    exit;
  }
}