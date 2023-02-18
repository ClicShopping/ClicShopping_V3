<?php
  /**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use OpenAI;

  class Chat
  {
     public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function getModel(): string
    {
      $array = [
        ['id' => 'text-davinci-003',
         'text' =>'Davinci (texte sophistiqué)'
        ],
        ['id' => 'davinci-codex',
         'text' =>'Code (expérimental)'
        ],

        ['id' => 'text-curie-001',
         'text' => 'Curie (texte moins complexes)'
        ],

      ];

      $menu = HTML::selectField('engine', $array, null, 'id="engine"');

      return $menu;
    }

    /**
     * @return String
     */
    public static function ChatGptModal() : String
    {
      $menu = '';

      if (\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') && CLICSHOPPING_APP_CHATGPT_CH_STATUS =='True') {
        $menu .= '
              <span class="col-md-2">
                <!-- Modal -->
                <a href="#chatModal" data-bs-toggle="modal" data-bs-target="#chatModal"><span class="text-white"><i class="bi bi-chat-left-dots-fill" title="' . CLICSHOPPING::getDef('text_chat_open') . '"></i><span></a>
                <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="chatModalLabel">What do you want to resolve ?</h5>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . CLICSHOPPING::getDef('text_chat_close') . '</button>
                      </div>
                      <div class="modal-body">
                        <div class="separator"></div>
                        <div class="row">' . static::getModel() .'</div>
                        <div class="separator"></div>
                        <div class="form-group">
                          <textarea class="form-control" id="messageGpt" rows="3" placeholder="' . CLICSHOPPING::getDef('text_chat_message') . '"></textarea>
                        </div>
                        <div class="separator"></div>
                        <div class="form-group text-end">
                          <div class="row">
                            <span class="col-md-6 text-start">
                              <ul class="list-group-slider list-group-flush">
                                <span class="text-slider col-6">' . CLICSHOPPING::getDef('text_chat_save') . '</span>
                                <li class="list-group-item-slider">
                                  <label class="switch">
                                    ' . HTML::checkboxField('save', '1', null, 'class="success" id="saveGpt"') . '
                                    <span class="slider"></span>
                                  </label>
                                </li>
                              </ul>
                            </span>
                            <span class="col-md-6 text-end">                          
                              <button type="button" class="btn btn-primary btn-sm" id="sendGpt">' . CLICSHOPPING::getDef('text_chat_send') . '</button>
                            </span>                         
                          </div>
                        </div>
                        <div class="separator"></div>
                        <div class="card">
                          <div class="chat-box-message text-start">
                            <div id="chatGpt-output" class="text-bg-light"></div>                                                    
                          </div>
                        </div>  
                      </div>
                    </div>
                  </div>
                </div>
              </span>
          ';
      }

      return $menu;
    }
  }