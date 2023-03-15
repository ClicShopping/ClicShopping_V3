<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\SEO;

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
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/SEO/seo_title');

      if(empty(STORE_NAME)) {
        return false;
      }

      $store_name = HTML::sanitize(STORE_NAME);
      $question_title = $this->app->getDef('text_seo_page_title_question');
      $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
      $question_description = $this->app->getDef('text_seo_page_description_question');
      $question_tag = $this->app->getDef('text_seo_page_tag_question');

      $text_tag_specials = $this->app->getDef('text_tag_specials');
      $text_tag_favorite = $this->app->getDef('text_tag_favorite');
      $text_tag_featured = $this->app->getDef('text_tag_featured');
      $text_tag_products_new = $this->app->getDef('text_tag_products_new');
      $text_tag_review = $this->app->getDef('text_tag_review');

      $url = Chat::getAjaxUrl(false);

      $content = '<button type="button" class="btn btn-primary btn-sm submit-button" data-index="0">';
      $content .= '<i class="bi-chat-square-dots" title="' . $this->app->getDef('text_seo_page_title') . '"></i>';
      $content .= '</button>';

$output = <<<EOD
<!------------------>
<!-- ChatGpt start tag-->
<!------------------>

<!-- product seo  meta title h1 -->
<script defer>
$('[id^="seo_default_title_h"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoDefaultTitleH = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_default_title_h_" + idSeoDefaultTitleH).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- product seo  meta title -->
<script defer>
$('[id^="seo_default_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoDefaultLanguageTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_default_title_tag_" + idSeoDefaultLanguageTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo meta default description -->
<script defer>
$('[id^="seo_default_desc_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoDefaultDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_default_desc_tag_" + idSeoDefaultDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>


<!-- product seo  meta keywords -->
<script defer>
$('[id^="seo_defaut_language_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoDefautLanguageKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_defaut_language_keywords_" + IdSeoDefautLanguageKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo meta footer -->
<script defer>
$('[id^="seo_defaut_language_footer"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoDefautLanguageFooter = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_tag}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_defaut_language_footer_" + IdSeoDefautLanguageFooter).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>
<!-- ------------------ 
 Product Description 
-------------------- -->

<!-- product seo  description title -->
<script defer>
$('[id^="seo_product_description_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoProductDescriptionTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_product_description_title_tag_" + idSeoProductDescriptionTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo product description -->
<script defer>
$('[id^="seo_product_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoProductDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_product_description_" + idSeoProductDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>

<!-- product product meta keywords -->
<script defer>
$('[id^="seo_product_description_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoProductDescriptionKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_product_description_keywords_" + IdSeoProductDescriptionKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- ------------------ 
 New Product  
-------------------- -->
<!-- product seo  prducts new title -->
<script defer>
$('[id^="seo_product_new_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoProductNewTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_product_new_title_tag_" + idSeoProductNewTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- product seo product new description -->
<script defer>
$('[id^="seo_product_new_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoProductNewDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_product_new_description_" + idSeoProductNewDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>

<!-- product seo  meta keywords -->
<script defer>
$('[id^="seo_product_new_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoproductNewKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_product_new_keywords_" + IdSeoproductNewKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- ------------------ 
Specials 
-------------------- -->
<!-- product seo  prducts new title -->
<script defer>
$('[id^="seo_special_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoSpecialTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_special_title_tag_" + idSeoSpecialTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- product seo special description -->
<script defer>
$('[id^="seo_special_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoSpecialDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_special_description_" + idSeoSpecialDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>

<!-- Special seo  meta keywords -->
<script defer>
$('[id^="seo_special_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoSpecialKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_special_keywords_" + IdSeoSpecialKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- ------------------ 
Review 
-------------------- -->
<!-- product seo review title -->
<script defer>
$('[id^="seo_review_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let idSeoReviewTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_review_title_tag_" + idSeoReviewTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo review description -->
<script defer>
$('[id^="seo_review_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoReviewDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_review_description_" + idSeoReviewDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>

<!-- Review seo  meta keywords -->
<script defer>
$('[id^="seo_review_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoReviewKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_review_keywords_" + IdSeoReviewKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- ------------------ 
Favorite 
-------------------- -->
<!-- Favorites seo  meta title -->
<script defer>
$('[id^="seo_favorite_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoFavoriteTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_favorite_title_tag_" + IdSeoFavoriteTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

<!-- product seo favorite description -->
<script defer>
$('[id^="seo_favorite_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoFavoriteDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_favorite_description_" + idSeoFavoriteDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>


<!-- Favorite seo  meta keywords -->
<script defer>
$('[id^="seo_favorite_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoFavoriteKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_favorite_keywords_" + IdSeoFavoriteKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- ------------------ 
Featured 
-------------------- -->
<!-- Featured seo  meta title -->
<script defer>
$('[id^="seo_featured_title_tag"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoFeaturedTitle = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_featured_title_tag_" + IdSeoFeaturedTitle).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>


<!-- product seo featured description -->
<script defer>
$('[id^="seo_featured_description"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g; // Expression régulière pour extraire l'id
    let idSeoFeaturedDescription = textareaId.match(regex)[0]; // Extraire l'id du textarea
    let questionResponse = '{$question_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
    
    newButton.click(function() { // Ajouter un listener pour chaque bouton
      let message = questionResponse; // Valeur envoyée à Open AI
      let engine = $("#engine").val();

      $.post("{$url}", {message: message, engine: engine}, function(data) {
        $("#chatGpt-output-input").val(data);
        $("#seo_featured_description_" + idSeoFeaturedDescription).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
      });
    });

    $(this).append(newButton);
  }
});
</script>


<!-- featured seo  meta keywords -->
<script defer>
$('[id^="seo_featured_keywords"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);
  let inputId = $(this).attr('id'); // Récupérer l'id de l'input pour l'itération actuelle
  let regex = /(\d+)/g; // Expression régulière pour extraire l'id
  let IdSeoFeaturedKeywords = regex.exec(inputId)[0]; // Extraire l'id de l'input   
  let questionResponse = '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
  
  newButton.click(function() { // Ajouter un listener pour chaque bouton
    let message = questionResponse; // Valeur envoyée à Open AI
    let engine = $("#engine").val();

    $.post("{$url}", {message: message, engine: engine}, function(data) {
      $("#chatGpt-output-input").val(data);
      $("#seo_featured_keywords_" + IdSeoFeaturedKeywords).val(data); // Remplir automatiquement l'input avec la réponse de OPEN AI pour l'itération actuelle
    });
  });

  $(this).append(newButton); 
});
</script>

EOD;
      return $output;
    }
  }
