<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\CH\Params;

  use ClicShopping\OM\HTML;

  class model extends \ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'text-davinci-003';
    public ?int $sort_order = 15;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_chatgpt_model_title');
      $this->description = $this->app->getDef('cfg_chatgpt_model_description');
    }

    public function getInputField()
    {
      $array = [
        ['id' => 'text-davinci-003',
          'text' =>'gpt-3 Davinci 003'
        ],
        ['id' => 'gpt-4',
         'text' =>'gpt-4'
        ],
        ['id' => 'gpt-4-32k',
          'text' =>'gpt-4-32k'
        ],
      ];

      $input = HTML::selectField($this->key, $array, $this->getInputValue(), 'id="engine"');

      return $input;
    }
  }
