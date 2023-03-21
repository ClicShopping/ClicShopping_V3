<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Categories;

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
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (Chat::checkGotStatus() === false) {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Categories/seo_chat_gpt');

      if (isset($_GET['cID'])) {
        $id = HTML::sanitize($_GET['cID']);
      } else {
        return false;
      }

      $question = $this->app->getDef('text_seo_page_title_question');
      $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
      $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
      $translate_language = $this->app->getDef('text_seo_page_translate_language');

      $categories_name = $CLICSHOPPING_CategoriesAdmin->getCategoryName($id, $CLICSHOPPING_Language->getId());

      $url = Chat::getAjaxUrl(false);
      $urlMultilanguage = Chat::getAjaxSeoMultilanguageUrl();

      $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
      $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
      $content .= '</button>';

$output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>

<!-- categories seo meta title -->
<script defer>
$('[id^="categories_head_title_tag"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idCategoriesHeadTitleTag = regex.exec(inputId)[0];

  let language_id = parseInt(idCategoriesHeadTitleTag);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question}' + ' ' + '{$categories_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#categories_head_title_tag_' + idCategoriesHeadTitleTag).val(data);
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

<!-- categories meta description -->
<script defer>
$('[id^="categories_head_desc_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g;
    let idCategoriesSeoDescription = regex.exec(textareaId)[0];
  
    let language_id = parseInt(idCategoriesSeoDescription);
  
    // Envoi d'une requête AJAX pour récupérer le nom de la langue
    let self = this;
    $.ajax({
      url: '{$urlMultilanguage}',
      data: {id: language_id},
      success: function(language_name) {
        let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$categories_name}';
        
        newButton.click(function() {
          let message = questionResponse;
          let engine = $('#engine').val();
  
          $.ajax({
            url: '{$url}',
            type: 'POST',
            data: {message: message, engine: engine},
            success: function(data) {
              $('#chatGpt-output-input').val(data);
              $('#categories_head_desc_tag_' + idCategoriesSeoDescription).val(data);
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

<!-- categoires seo  meta keyword -->
<script defer>
$('[id^="categories_head_keywords_tag"]').each(function(index) {
  let inputId = $(this).attr('id');
  let regex = /(\d+)/g;
  let idCategoriesSeoKeywords = regex.exec(inputId)[0];

  let language_id = parseInt(idCategoriesSeoKeywords);
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  // Envoi d'une requête AJAX pour récupérer le nom de la langue
  let self = this;
  $.ajax({
    url: '{$urlMultilanguage}',
    data: {id: language_id},
    success: function(language_name) {
      let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$categories_name}';
      
      newButton.click(function() {
        let message = questionResponse;
        let engine = $('#engine').val();

        $.ajax({
          url: '{$url}',
          type: 'POST',
          data: {message: message, engine: engine},
          success: function(data) {
            $('#chatGpt-output-input').val(data);
            $('#categories_head_keywords_tag_' + idCategoriesSeoKeywords).val(data);
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
      return $output;
    }
  }
