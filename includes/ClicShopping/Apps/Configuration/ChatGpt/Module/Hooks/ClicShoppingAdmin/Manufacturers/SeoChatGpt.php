<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Manufacturers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
  use ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin\ManufacturerAdmin;
  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\ChatGptAdmin;

  class SeoChatGpt implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ChatGpt')) {
        Registry::set('ChatGpt', new ChatGptApp());
      }

      $this->app = Registry::get('ChatGpt');
    }

    public function display()
    {
      if (ChatGptAdmin::checkGptStatus() === false) {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Manufacturer/seo_chat_gpt');

      if (isset($_GET['mID'])) {
        $id = HTML::sanitize($_GET['mID']);

        $question = $this->app->getDef('text_seo_page_title_question');
        $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
        $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
        $translate_language = $this->app->getDef('text_seo_page_translate_language');

        $manufacturer_name = ManufacturerAdmin::getManufacturerNameById($id);

        $url = ChatGptAdmin::getAjaxUrl(false);
        $urlMultilanguage = ChatGptAdmin::getAjaxSeoMultilanguageUrl();

        $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
        $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
        $content .= '</button>';

$output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>
<!-- manufacturer seo  meta title -->
<script defer>
$('[id^="manufacturer_seo_title"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idManufacturerSeoTitle = regex.exec(inputId)[0];

  let language_id = parseInt(idManufacturerSeoTitle);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question}' + ' ' + '{$manufacturer_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#manufacturer_seo_title_' + idManufacturerSeoTitle).val(data);
          },
          error: function(xhr, status, error) {
            console.log(xhr.responseText);
          }
        });
      });

      if (newButton) {
        $(self).append(newButton);
      }
    }
  });
});
</script>

<!-- manufacturer seo  meta description -->
<script defer>
$('[id^="manufacturer_seo_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g;
    let idManufacturerSeoDescription = regex.exec(textareaId)[0];
  
    let language_id = parseInt(idManufacturerSeoDescription);
  
    // Envoi d'une requête AJAX pour récupérer le nom de la langue
    let self = this;
    $.ajax({
      url: '{$urlMultilanguage}',
      data: {id: language_id},
      success: function(language_name) {
        let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$manufacturer_name}';
        
        newButton.click(function() {
          let message = questionResponse;
          let engine = $('#engine').val();
  
          $.ajax({
            url: '{$url}',
            type: 'POST',
            data: {message: message, engine: engine},
            success: function(data) {
              $('#chatGpt-output-input').val(data);
              $('#manufacturer_seo_description_' + idManufacturerSeoDescription).val(data);
            },
            error: function(xhr, status, error) {
              console.log(xhr.responseText);
            }
          });
        });
  
        if (newButton) {
          $(self).append(newButton);
        }
      }
    });
  }
});
</script>

<!-- manufacturer seo  meta keyword -->
<script defer>
$('[id^="manufacturer_seo_keyword"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idManufacturerSeoKeywords = regex.exec(inputId)[0];

  let language_id = parseInt(idManufacturerSeoKeywords);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$manufacturer_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#manufacturer_seo_keyword_' + idManufacturerSeoKeywords).val(data);
          },
          error: function(xhr, status, error) {
            console.log(xhr.responseText);
          }
        });
      });

      if (newButton) {
        $(self).append(newButton);
      }
    }
  });
});
</script>

EOD;
    } else {
    $tab_title = $this->app->getDef('tab_gpt_options');
    $title = $this->app->getDef('text_gpt_options');

    $content = '
              <div class="separator"></div>
              <div class="row" id="productOptionGptDescription">
                <div class="col-md-9">
                  <div class="form-group row">
                    <label for="' . $this->app->getDef('text_options_gpt_description') . '"
                           class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_description') . '</label>
                    <div class="col-md-2">
                      <ul class="list-group-slider list-group-flush">
                        <li class="list-group-item-slider">
                          <label class="switch">
                            ' . HTML::checkboxField('option_gpt_description', '1', true, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="separator"></div>
              <div class="row" id="productOptionGptSeoTitle">
                <div class="col-md-9">
                  <div class="form-group row">
                    <label for="' . $this->app->getDef('text_options_gpt_seo_title') . '"
                           class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_title') . '</label>
                    <div class="col-md-2">
                      <ul class="list-group-slider list-group-flush">
                        <li class="list-group-item-slider">
                          <label class="switch">
                            ' . HTML::checkboxField('option_gpt_seo_title', '1', true, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="separator"></div>
              <div class="row" id="productOptionGptSeoDescription">
                <div class="col-md-9">
                  <div class="form-group row">
                    <label for="' . $this->app->getDef('text_options_gpt_seo_description') . '"
                           class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_description') . '</label>
                    <div class="col-md-2">
                      <ul class="list-group-slider list-group-flush">
                        <li class="list-group-item-slider">
                          <label class="switch">
                            ' . HTML::checkboxField('option_gpt_seo_description', '1', true, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="separator"></div>
              <div class="row" id="productOptionGptSeokeywords">
                <div class="col-md-9">
                  <div class="form-group row">
                    <label for="' . $this->app->getDef('text_options_gpt_seo_keywords') . '"
                           class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_keywords') . '</label>
                    <div class="col-md-2">
                      <ul class="list-group-slider list-group-flush">
                        <li class="list-group-item-slider">
                          <label class="switch">
                            ' . HTML::checkboxField('option_gpt_seo_keywords', '1', true, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="separator"></div>
              <div class="row" id="productOptionGptCreateImage">
                <div class="col-md-9">
                  <div class="form-group row">
                    <label for="' . $this->app->getDef('text_options_gpt_image') . '"
                           class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_image') . '</label>
                    <div class="col-md-2">
                      <ul class="list-group-slider list-group-flush">
                        <li class="list-group-item-slider">
                          <label class="switch">
                            ' . HTML::checkboxField('option_gpt_create_image', '1', false, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="separator"></div>
              <div class="alert alert-info" role="alert">
                <div><h4><i class="bi bi-question-circle" title="' . $this->app->getDef('title_help_seo') . '"></i></h4> ' . $this->app->getDef('title_help_seo') .'</div>
                <div class="separator"></div>
                <div>' . $this->app->getDef('text_help_seo') .'</div>
              </div>
              ';

    $output = <<<EOD
  <!-- ######################## -->
  <!--  Start OptionsGptApp  -->
  <!-- ######################## -->
  <div class="tab-pane" id="section_OptionsGptApp_content">
    <div class="mainTitle">
      <span class="col-md-2">{$title}</span>
    </div>
    {$content}
  </div>
  <script>
  $('#section_OptionsGptApp_content').appendTo('#manufacturersTabs .tab-content');
  $('#manufacturersTabs .nav-tabs').append('    <li class="nav-item"><a data-bs-target="#section_OptionsGptApp_content" role="tab" data-bs-toggle="tab" class="nav-link">{$tab_title}</a></li>');
  </script>
  <!-- ######################## -->
  <!-- End OptionsGptApp  -->
  <!-- ######################## -->
  EOD;
    }

    return $output;
  }
}