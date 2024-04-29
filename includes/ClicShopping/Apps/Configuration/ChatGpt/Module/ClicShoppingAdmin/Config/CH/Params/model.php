<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\CH\Params;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;
use ClicShopping\OM\HTML;

class model extends \ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = 'gpt-3.5-turbo';
  public ?int $sort_order = 15;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_chatgpt_model_title');
    $this->description = $this->app->getDef('cfg_chatgpt_model_description');
  }

  public function getInputField()
  {
    $array = Gpt::getGptModel();

    $input = HTML::selectField($this->key, $array, $this->getInputValue(), 'id="engine"');

    return $input;
  }
}
