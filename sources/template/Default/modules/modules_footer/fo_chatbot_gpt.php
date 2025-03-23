<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop\GptShop;

class fo_chatbot_gpt
{
  public string $code;
  public string $group;
  public $title;
  public $description;
  public int|null $sort_order = 0;
  public bool $enabled = false;


  public function __construct()
  {
    $this->code = get_class($this);
    $this->group = basename(__DIR__);

    $this->title = CLICSHOPPING::getDef('module_footer_chatbot_gpt_title');
    $this->description = CLICSHOPPING::getDef('module_footer_chatbot_gpt_description');

    if (\defined('MODULES_FOOTER_CHATBOT_GPT_STATUS')) {
      $this->sort_order = MODULES_FOOTER_CHATBOT_GPT_SORT_ORDER;
      $this->enabled = (MODULES_FOOTER_CHATBOT_GPT_STATUS == 'True');
    }

    if (\defined('MODULES_FOOTER_CHATBOT_GPT_MAX_TOKEN')) {
      if (GptShop::checkMaxTokenPerDay(MODULES_FOOTER_CHATBOT_GPT_MAX_TOKEN) === false) {
        $this->enabled = false;
      }
    }

    if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False' || empty('CLICSHOPPING_APP_CHATGPT_CH_API_KEY')) {
      $this->enabled = false;
    }
  }

  public function execute()
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    $image = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'sources/images/icons/chat_support.png';

    $url = GptShop::getAjaxUrl(false);
    $promt_text = CLICSHOPPING::getDef('module_footer_chatbot_gpt_prompt_text');

    $footer_tag = '<!--  chatbot start -->' . "\n";
    $footer_tag .= '<script defer>';
    $footer_tag .= '
    var isUserAtBottom = true;

    // Function to toggle the chatbox visibility
function toggleChatbox() {
    var chatbotSection = document.getElementById(\'chatbot-section\');
    var chatToggleContainer = document.getElementById(\'chat-toggle-container\');
    
    if (chatbotSection.style.display === \'none\' || chatbotSection.style.display === \'\') {
        chatbotSection.style.display = \'block\';
        chatToggleContainer.style.display = \'none\';
    } else {
        chatbotSection.style.display = \'none\';
        chatToggleContainer.style.display = \'block\';
    }
}

function closeChatbox() {
    var chatbotSection = document.getElementById(\'chatbot-section\');
    var chatToggleContainer = document.getElementById(\'chat-toggle-container\');
    
    chatbotSection.style.display = \'none\';
    chatToggleContainer.style.display = \'block\';
    
    // Remove the class to restore the chatbox size
    var chatbox = document.getElementById(\'chatbot-section\');
    chatbox.classList.remove(\'chatbox-small\');
}

function displayMessage(message, messageType) {
    var chatMessages = document.getElementById(\'chat-messages\');

    // Determine if the user is already at the bottom of the chat
    var shouldScroll = chatMessages.scrollTop + chatMessages.clientHeight === chatMessages.scrollHeight;

    // Create a new message element and append it to the chat-messages div
    var messageElement = document.createElement(\'div\');
    messageElement.className = \'message\' + (messageType === \'system\' ? \' system-message\' : \'\'); // Add the system-message class

    messageElement.innerHTML = message + \'<div class="chatbot-hr"></div>\';
    chatMessages.appendChild(messageElement);

    // Scroll to the bottom if the user was already at the bottom
    if (shouldScroll) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Apply the class to the chatbox to reduce its size
    var chatbox = document.getElementById(\'chatbot-section\');
    chatbox.classList.add(\'chatbox-small\');
}

function removeSystemMessage() {
    var systemMessageElement = document.querySelector(\'.system-message\');
    if (systemMessageElement) {
        systemMessageElement.remove();
    }
}

function sendMessage() {
    var message = document.getElementById(\'message\').value;
    if (message.trim() === \'\') return;

    // Display a system message to indicate that the request is being processed
    var systemProcessingMessage = \'<div class="system-message">' . $promt_text . '</div>\';
    displayMessage(systemProcessingMessage, \'system\');

    // Assume you have an AJAX call here to send the message to the chatbot backend
    // Replace the URL and other details as needed
    var xhr = new XMLHttpRequest();
    xhr.open(\'POST\', \'' . $url . '\', true);
    xhr.setRequestHeader(\'Content-Type\', \'application/x-www-form-urlencoded\');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Handle the chatbot response and display it in the chatbox
                displayMessage(xhr.responseText);

                // Remove the system message after a delay (e.g., 2 seconds)
                setTimeout(function() {
                    removeSystemMessage();
                }, 2000);
            } else {
                console.log(xhr.responseText);
            }
        }
    };
    xhr.send(\'message=\' + encodeURIComponent(message));

    // Clear the input field after sending the message
    document.getElementById(\'message\').value = \'\';
}

// Function to check if user is at the bottom of the textarea
function checkScrollPosition() {
    var chatMessages = document.getElementById(\'chat-messages\');
    var isUserAtBottom = chatMessages.scrollTop + chatMessages.clientHeight === chatMessages.scrollHeight;

    if (isUserAtBottom) {
        chatMessages.classList.add(\'scroll-bottom\');
    } else {
        chatMessages.classList.remove(\'scroll-bottom\');
    }
}

// Add scroll event listener to the chat-messages div
document.getElementById(\'chat-messages\').addEventListener(\'scroll\', checkScrollPosition);
';

    $footer_tag .= '</script>';
    $footer_tag .= '<!--  chatbot end -->' . "\n";

    $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

    $footer_tag = '<!-- footer chatbot start -->' . "\n";

    ob_start();
    require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/footer_chatbot_gpt'));
    $footer_tag .= ob_get_clean();

    $footer_tag .= '<!-- footer chatbot end -->' . "\n";

    $CLICSHOPPING_Template->addBlock($footer_tag, $this->group);
  }

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function check()
  {
    return \defined('MODULES_FOOTER_CHATBOT_GPT_STATUS');
  }

  public function install()
  {
    $CLICSHOPPING_Db = Registry::get('Db');


    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Do you want to enable this module ?',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to enable this module in your shop ?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please insert the temperature',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_TEMPERATURE',
        'configuration_value' => '0.5',
        'configuration_description' => 'The temperature is the creativity of the AI for answer',
        'configuration_group_id' => '6',
        'sort_order' => '4',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'please insert the token used',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_TOKEN',
        'configuration_value' => '100',
        'configuration_description' => 'Please insert an integer number',
        'configuration_group_id' => '6',
        'sort_order' => '5',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please insert the name of your Chatbot',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_NAME',
        'configuration_value' => 'Rachelle',
        'configuration_description' => 'Please insert the name of your Chatbot',
        'configuration_group_id' => '6',
        'sort_order' => '5',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please insert the total token you want consumme by days',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_MAX_TOKEN',
        'configuration_value' => '10000',
        'configuration_description' => 'If the total token consumme is upper as your acception, the module will be not displayed until end of the day.',
        'configuration_group_id' => '6',
        'sort_order' => '5',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Sort order',
        'configuration_key' => 'MODULES_FOOTER_CHATBOT_GPT_SORT_ORDER',
        'configuration_value' => '1000',
        'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
        'configuration_group_id' => '6',
        'sort_order' => '4',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function remove()
  {
    return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
  }

  public function keys()
  {
    return array('MODULES_FOOTER_CHATBOT_GPT_STATUS',
      'MODULES_FOOTER_CHATBOT_GPT_TEMPERATURE',
      'MODULES_FOOTER_CHATBOT_GPT_TOKEN',
      'MODULES_FOOTER_CHATBOT_GPT_NAME',
      'MODULES_FOOTER_CHATBOT_GPT_MAX_TOKEN',
      'MODULES_FOOTER_CHATBOT_GPT_SORT_ORDER'
    );
  }
}

