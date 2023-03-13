<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\PageManager;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Chat;
  use ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin\PageManagerAdmin;

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
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/PageManager/seo_title');

      if (isset($_GET['bID'])) {
        $id = HTML::sanitize($_GET['bID']);
      } else {
        return false;
      }

      $question = $this->app->getDef('text_seo_page_title_question');
      $questionKeywordsTag = $this->app->getDef('text_seo_page_keywords_question');
      $questionDescriptionTag = $this->app->getDef('text_seo_page_description_question');

      $page_manager_name = PageManagerAdmin::getPageManagerTitle($id, $CLICSHOPPING_Language->getId());

      $url = Chat::getAjaxUrl(false);

      $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
      $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
      $content .= '</button>';

$output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>
<script defer>
$('[id^="page_manager_head_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idPageManagerSeoTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input
  let questionResponse = '{$question}' + ' ' + '{$page_manager_name}';

  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#page_manager_head_title_tag_" + idPageManagerSeoTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo  meta keyword -->
<script defer>
$('[id^="page_manager_head_keywords_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idPageManagerSeoKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$questionKeywordsTag}' + ' ' + '{$page_manager_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#page_manager_head_keywords_tag_" + idPageManagerSeoKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo meta description -->
<script defer>
$('[id^="page_manager_head_desc_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idPageManagerSeoDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$questionDescriptionTag}' + ' ' + '{$page_manager_name}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#page_manager_head_desc_tag_" + idPageManagerSeoDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>
EOD;
      return $output;
    }
  }
