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
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;

  class SeoTitle implements \ClicShopping\OM\Modules\HooksInterface
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

      if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/seo_title');

      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);
      } else {
        return false;
      }

      $question = $this->app->getDef('text_seo_page_title_question');
      $questionTag = $this->app->getDef('text_seo_page_tag_question');
      $questionKeywordsTag = $this->app->getDef('text_seo_page_keywords_question');
      $questionDescriptionTag = $this->app->getDef('text_seo_page_description_question');

      $product_name = $CLICSHOPPING_ProductsAdmin->getProductsName($id);

      $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGptSEO.php';

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
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idproductsSummaryDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$questionDescriptionTag}' + ' ' + '{$product_name}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#SummaryDescription_" + idproductsSummaryDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>



<script defer>
$('[id^="products_head_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idProductsHeadTitleTag = regex.exec(inputId)[0]; // Extraire l'id de l'input
  let questionResponse = '{$question}' + ' ' + '{$product_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#products_head_title_tag_" + idProductsHeadTitleTag).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- product seo  meta keyword -->
<script defer>
$('[id^="products_head_keywords_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idProductsSeoKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$questionKeywordsTag}' + ' ' + '{$product_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#products_head_keywords_tag_" + idProductsSeoKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});

</script>

<!-- product seo tag -->
<script defer>
$('[id^="products_head_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idProductsSeoTag = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$questionTag}' + ' ' + '{$product_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#products_head_tag_" + idProductsSeoTag).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
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
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idproductsSeoDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$questionDescriptionTag}' + ' ' + '{$product_name}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#products_head_desc_tag_" + idproductsSeoDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
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
