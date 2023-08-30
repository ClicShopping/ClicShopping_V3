<?php

use ClicShopping\OM\CLICSHOPPING;

?>
<div id="chat-toggle-container" style="position: fixed; bottom: 20px; right: 20px;">
  <img src="<?php echo $image; ?>" alt="Customers support" id="chat-toggle" onclick="toggleChatbox()">
</div>

<div id="chatbot-section" style="position: fixed; bottom: 20px; right: 20px; background-color: #eee; display: none;">
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center p-3"
               style="border-top: 4px solid #ffa900;">
            <h7 class="mb-0">
              <img src="<?php echo $image; ?>" height="20px" class="avatar" alt="Chat Icon">
              <?php echo MODULES_FOOTER_CHATBOT_GPT_NAME . ' ' . CLICSHOPPING::getDef('module_footer_chatbot_gpt_listen'); ?>
            </h7>
          </div>
          <div class="card-body" id="chat-messages" style="position: relative; height: 400px">
            <!-- Messages will appear here -->
          </div>
          <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
            <div class="col-md-12">
              <div class="row">
                <textarea class="form-control" id="message" rows="4"
                          placeholder="<?php echo CLICSHOPPING::getDef('module_footer_chatbot_gpt_prompt'); ?>"></textarea>
                <label class="form-label" for="message"></label>
              </div>
              <div class="row">
                <span class="col-md-6"><button class="btn btn-sm btn-primary btn-send"
                                               onclick="sendMessage()"><?php echo CLICSHOPPING::getDef('module_footer_chatbot_gpt_send'); ?></button></span>
                <span class="col-md-6 text-end"><button class="btn btn-sm btn-danger" id="close-button"
                                                        onclick="closeChatbox()"><?php echo CLICSHOPPING::getDef('module_footer_chatbot_gpt_close'); ?></button></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

