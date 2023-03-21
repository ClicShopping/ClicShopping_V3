<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Chat;

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
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      if (Chat::checkGptStatus() === false) {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/seo_chat_gpt');

      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);

        $question = $this->app->getDef('text_seo_page_title_question');
        $question_tag = $this->app->getDef('text_seo_page_tag_question');
        $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
        $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
        $translate_language = $this->app->getDef('text_seo_page_translate_language');

        $product_name = $CLICSHOPPING_ProductsAdmin->getProductsName($id);

        $url = Chat::getAjaxUrl();
        $urlMultilanguage = Chat::getAjaxSeoMultilanguageUrl();

        $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
        $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
        $content .= '</button>';

        $output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>

<!-- product seo meta description -->
<script defer>
$('[id^="SummaryDescription"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g;
    let idproductsSummaryDescription = regex.exec(textareaId)[0];
  
    let language_id = parseInt(idproductsSummaryDescription);
  
    // Envoi d'une requête AJAX pour récupérer le nom de la langue
    let self = this;
    $.ajax({
      url: '{$urlMultilanguage}',
      data: {id: language_id},
      success: function(language_name) {
        let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$product_name}';
        
        newButton.click(function() {
          let message = questionResponse;
          let engine = $('#engine').val();
  
          $.ajax({
            url: '{$url}',
            type: 'POST',
            data: {message: message, engine: engine},
            success: function(data) {
              $('#chatGpt-output-input').val(data);
              $('#SummaryDescription_' + idproductsSummaryDescription).val(data);
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


<!-- products seo title meta tag -->
<script defer>
$('[id^="products_head_title_tag"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idProductsHeadTitleTag = regex.exec(inputId)[0];

  let language_id = parseInt(idProductsHeadTitleTag);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question}' + ' ' + '{$product_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#products_head_title_tag_' + idProductsHeadTitleTag).val(data);
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


<!-- product seo meta description -->
<script defer>
$('[id^="products_head_desc_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g;
    let idproductsSeoDescription = regex.exec(textareaId)[0];
  
    let language_id = parseInt(idproductsSeoDescription);
  
    // Envoi d'une requête AJAX pour récupérer le nom de la langue
    let self = this;
    $.ajax({
      url: '{$urlMultilanguage}',
      data: {id: language_id},
      success: function(language_name) {
        let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$product_name}';
        
        newButton.click(function() {
          let message = questionResponse;
          let engine = $('#engine').val();
  
          $.ajax({
            url: '{$url}',
            type: 'POST',
            data: {message: message, engine: engine},
            success: function(data) {
              $('#chatGpt-output-input').val(data);
              $('#products_head_desc_tag_' + idproductsSeoDescription).val(data);
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

<!-- product seo  meta keyword -->
<script defer>
$('[id^="products_head_keywords_tag"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idProductsSeoKeywords = regex.exec(inputId)[0];

  let language_id = parseInt(idProductsSeoKeywords);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$product_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#products_head_keywords_tag_' + idProductsSeoKeywords).val(data);
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

<!-- product seo tag -->
<script defer>
$('[id^="products_head_tag"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idProductsSeoTag = regex.exec(inputId)[0];

  let language_id = parseInt(idProductsSeoTag);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_tag}' + ' ' + '{$product_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#products_head_tag_' + idProductsSeoTag).val(data);
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
            <div class="row" id="productOptionGptSummary">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_summary_description') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_summary_description') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_summary_description', '1', true, 'class="success"') . '
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
            <div class="row" id="productOptionGptSeotags">
              <div class="col-md-9">
                <div class="form-group row">
                  <label for="' . $this->app->getDef('text_options_gpt_seo_tags') . '"
                         class="col-7 col-form-label">' . $this->app->getDef('text_options_gpt_seo_tags') . '</label>
                  <div class="col-md-2">
                    <ul class="list-group-slider list-group-flush">
                      <li class="list-group-item-slider">
                        <label class="switch">
                          ' . HTML::checkboxField('option_gpt_seo_tags', '1', true, 'class="success"') . '
                          <span class="slider"></span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
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
$('#section_OptionsGptApp_content').appendTo('#productsTabs .tab-content');
$('#productsTabs .nav-tabs').append('    <li class="nav-item"><a data-bs-target="#section_OptionsGptApp_content" role="tab" data-bs-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
<!-- ######################## -->
<!-- End OptionsGptApp  -->
<!-- ######################## -->
EOD;
      }

      return $output;
    }
  }
