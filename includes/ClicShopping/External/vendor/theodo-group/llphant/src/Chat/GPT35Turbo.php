<?php

namespace LLPhant\Chat;

use LLPhant\Chat\Enums\OpenAIChatModel;
use LLPhant\OpenAIConfig;

class GPT35Turbo extends OpenAIChat
{
    /**
     * @throws \Exception
     */
    public function __construct(?OpenAIConfig $config = null)
    {
        parent::__construct($config);
        $this->model = $config->model ?? OpenAIChatModel::Gpt35Turbo->getModelName();
    }
}
