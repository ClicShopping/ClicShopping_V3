<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin;

class ChatJsAdminSeo
{
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $url
   */
  public static function getInfoSeoDefaultTitleH1(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $url)
  {
    $script = "
        <script defer>
          $('[id^=\"seo_default_title_h\"]').each(function(index) {
            let inputId = $(this).attr('id');
            let regex = /(\d+)/g;
            let idSeoDefaultTitleH = regex.exec(inputId)[0];
          
            let language_id = parseInt(idSeoDefaultTitleH);
            let button = '{$content}';
            let newButton = $(button).attr('data-index', index);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                  let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}';
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_default_title_h_' + idSeoDefaultTitleH).val(data);
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
        </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $url
   */
  public static function getInfoSeoDefaultTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $url)
  {
    $script = "
      <script defer>
    $('[id^=\"seo_default_title_tag\"]').each(function(index) {
      let inputId = $(this).attr('id');
      let regex = /(\d+)/g;
      let idSeoDefaultLanguageTitle = regex.exec(inputId)[0];
    
      let language_id = parseInt(idSeoDefaultLanguageTitle);
      let button = '{$content}';
      let newButton = $(button).attr('data-index', index);
    
      // Envoi d'une requête AJAX pour récupérer le nom de la langue
      let self = this;
      $.ajax({
        url: '{$urlMultilanguage}',
        data: {id: language_id},
        success: function(language_name) {
          let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}';
          
          newButton.click(function() {
            let message = questionResponse;
            let engine = $('#engine').val();
    
            $.ajax({
              url: '{$url}',
              type: 'POST',
              data: {message: message, engine: engine},
              success: function(data) {
                $('#chatGpt-output-input').val(data);
                $('#seo_default_title_tag_' + idSeoDefaultLanguageTitle).val(data);
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
</script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoDefaultDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"seo_default_desc_tag\"]').each(function(index) {
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
        // Vérifier si le textarea a été trouvé
        if (textareaId !== undefined) {
          let regex = /(\d+)/g;
          let idSeoDefaultDescription = regex.exec(textareaId)[0];
        
          let language_id = parseInt(idSeoDefaultDescription);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_defaut_language_description_' + idSeoDefaultDescription).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoDefaultKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"seo_defaut_language_keywords\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoDefautLanguageKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoDefautLanguageKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_defaut_language_keywords_' + IdSeoDefautLanguageKeywords).val(data);
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
       </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_tag
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoDefaultFooter(string $content, string $urlMultilanguage, string $translate_language, string $question_tag, string $store_name, string $url)
  {
    $script = "
      <script defer>   
        $('[id^=\"seo_defaut_language_footer\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoDefautLanguageFooter = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoDefautLanguageFooter);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_tag}' + ' ' + '{$store_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_defaut_language_footer_' + IdSeoDefautLanguageFooter).val(data);
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
     </script>";

    return $script;
  }
//------------------------------------------------------
// product Description
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductDescriptionTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_product_description_title_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let idSeoProductDescriptionTitle = regex.exec(inputId)[0];
        
          let language_id = parseInt(idSeoProductDescriptionTitle);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_product_description_title_tag_' + idSeoProductDescriptionTitle).val(data);
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

     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_product_description\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idSeoProductDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idSeoProductDescription);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_product_description_' + idSeoProductDescription).val(data);
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
     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductkeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_product_description_keywords\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoProductDescriptionKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoProductDescriptionKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_product_description_keywords_' + IdSeoProductDescriptionKeywords).val(data);
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
     </script>";

    return $script;
  }

//------------------------------------------------------
// New
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_products_new
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductsNewTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $text_tag_products_new, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_product_new_title_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let idSeoProductNewTitle = regex.exec(inputId)[0];
        
          let language_id = parseInt(idSeoProductNewTitle);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +'{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_product_new_title_tag_' + idSeoProductNewTitle).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $text_tag_products_new
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductsNewDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $text_tag_products_new, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_product_new_description\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idSeoProductNewDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idSeoProductNewDescription);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_product_new_description_' + idSeoProductNewDescription).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $text_tag_products_new
   * @param string $url
   * @return string
   */
  public static function getInfoSeoProductsNewKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $text_tag_products_new, string $url)
  {
    $script = "
      <script defer> 
      $('[id^=\"seo_product_new_keywords\"]').each(function(index) {
        let inputId = $(this).attr('id');
        let regex = /(\d+)/g;
        let IdSeoproductNewKeywords = regex.exec(inputId)[0];
      
        let language_id = parseInt(IdSeoproductNewKeywords);
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        // Envoi d'une requête AJAX pour récupérer le nom de la langue
        let self = this;
        $.ajax({
          url: '{$urlMultilanguage}',
          data: {id: language_id},
          success: function(language_name) {
            let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_products_new}';
            
            newButton.click(function() {
              let message = questionResponse;
              let engine = $('#engine').val();
      
              $.ajax({
                url: '{$url}',
                type: 'POST',
                data: {message: message, engine: engine},
                success: function(data) {
                  $('#chatGpt-output-input').val(data);
                  $('#seo_product_new_keywords_' + IdSeoproductNewKeywords).val(data);
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
      </script>";

    return $script;
  }

//------------------------------------------------------
// Specials
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_specials
   * @param string $url
   * @return string
   */
  public static function getInfoSeoSpecialsTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $text_tag_specials, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"seo_special_title_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let idSeoSpecialTitle = regex.exec(inputId)[0];
        
          let language_id = parseInt(idSeoSpecialTitle);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_special_title_tag_' + idSeoSpecialTitle).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $text_tag_specials
   * @param string $url
   * @return string
   */
  public static function getInfoSeoSpecialsDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $text_tag_specials, string $url)
  {
    $script = "
      <script defer> 
$('[id^=\"seo_special_description\"]').each(function(index) {
  let button = '{$content}';
  let newButton = $(button).attr('data-index', index);

  let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
  // Vérifier si le textarea a été trouvé
  if (textareaId !== undefined) {
    let regex = /(\d+)/g;
    let idSeoSpecialDescription = regex.exec(textareaId)[0];
  
    let language_id = parseInt(idSeoSpecialDescription);
  
    // Envoi d'une requête AJAX pour récupérer le nom de la langue
    let self = this;
    $.ajax({
      url: '{$urlMultilanguage}',
      data: {id: language_id},
      success: function(language_name) {
        let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
        
        newButton.click(function() {
          let message = questionResponse;
          let engine = $('#engine').val();
  
          $.ajax({
            url: '{$url}',
            type: 'POST',
            data: {message: message, engine: engine},
            success: function(data) {
              $('#chatGpt-output-input').val(data);
              $('#seo_special_description_' + idSeoSpecialDescription).val(data);
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
     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $text_tag_specials
   * @param string $url
   * @return string
   */
  public static function getInfoSeoSpecialsKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $text_tag_specials, string $url)
  {
    $script = "
      <script defer> 
      $('[id^=\"seo_special_keywords\"]').each(function(index) {
        let inputId = $(this).attr('id');
        let regex = /(\d+)/g;
        let IdSeoSpecialKeywords = regex.exec(inputId)[0];
      
        let language_id = parseInt(IdSeoSpecialKeywords);
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        // Envoi d'une requête AJAX pour récupérer le nom de la langue
        let self = this;
        $.ajax({
          url: '{$urlMultilanguage}',
          data: {id: language_id},
          success: function(language_name) {
            let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_specials}';
            
            newButton.click(function() {
              let message = questionResponse;
              let engine = $('#engine').val();
      
              $.ajax({
                url: '{$url}',
                type: 'POST',
                data: {message: message, engine: engine},
                success: function(data) {
                  $('#chatGpt-output-input').val(data);
                  $('#seo_special_keywords_' + IdSeoSpecialKeywords).val(data);
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
      </script>";

    return $script;
  }

//------------------------------------------------------
// Reviews
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_specials
   * @param string $url
   * @return string
   */
  public static function getInfoSeoReviewsTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $text_tag_review, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"seo_review_title_tag\"]').each(function(index) {
        let inputId = $(this).attr('id');
        let regex = /(\d+)/g;
        let idSeoReviewTitle = regex.exec(inputId)[0];
      
        let language_id = parseInt(idSeoReviewTitle);
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        // Envoi d'une requête AJAX pour récupérer le nom de la langue
        let self = this;
        $.ajax({
          url: '{$urlMultilanguage}',
          data: {id: language_id},
          success: function(language_name) {
            let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
            
            newButton.click(function() {
              let message = questionResponse;
              let engine = $('#engine').val();
      
              $.ajax({
                url: '{$url}',
                type: 'POST',
                data: {message: message, engine: engine},
                success: function(data) {
                  $('#chatGpt-output-input').val(data);
                  $('#seo_review_title_tag_' + idSeoReviewTitle).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_specials
   * @param string $url
   * @return string
   */
  public static function getInfoSeoReviewsDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $text_tag_review, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_review_description\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idSeoReviewDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idSeoReviewDescription);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_review_description_' + idSeoReviewDescription).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $text_tag_review
   * @param string $url
   * @return string
   */
  public static function getInfoSeoReviewsKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $text_tag_review, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_review_keywords\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoReviewKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoReviewKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_review}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_review_keywords_' + IdSeoReviewKeywords).val(data);
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
      </script>";

    return $script;
  }

//------------------------------------------------------
// Favorites
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_favorite
   * @param string $url
   * @return string
   */
  public static function getInfoFavoritesTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $text_tag_favorite, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"seo_favorite_title_tag\"]').each(function(index) {
        let inputId = $(this).attr('id');
        let regex = /(\d+)/g;
        let IdSeoFavoriteTitle = regex.exec(inputId)[0];
      
        let language_id = parseInt(IdSeoFavoriteTitle);
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        // Envoi d'une requête AJAX pour récupérer le nom de la langue
        let self = this;
        $.ajax({
          url: '{$urlMultilanguage}',
          data: {id: language_id},
          success: function(language_name) {
            let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
            
            newButton.click(function() {
              let message = questionResponse;
              let engine = $('#engine').val();
      
              $.ajax({
                url: '{$url}',
                type: 'POST',
                data: {message: message, engine: engine},
                success: function(data) {
                  $('#chatGpt-output-input').val(data);
                  $('#seo_favorite_title_tag_' + IdSeoFavoriteTitle).val(data);
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
     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $text_tag_favorite
   * @param string $url
   * @return string
   */
  public static function getInfoFavoritesDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $text_tag_favorite, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"seo_favorite_description\"]').each(function(index) {
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
        // Vérifier si le textarea a été trouvé
        if (textareaId !== undefined) {
          let regex = /(\d+)/g;
          let idSeoFavoritesDescription = regex.exec(textareaId)[0];
        
          let language_id = parseInt(idSeoFavoritesDescription);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_favorite_description_' + idSeoFavoritesDescription).val(data);
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
     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $text_tag_favorite
   * @param string $url
   * @return string
   */
  public static function getInfoFavoritesKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $text_tag_favorite, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_favorite_keywords\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoFavoriteKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoFavoriteKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_favorite}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_favorite_keywords_' + IdSeoFavoriteKeywords).val(data);
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
     </script>";

    return $script;
  }

//------------------------------------------------------
// Favorites
//------------------------------------------------------
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $text_tag_featured
   * @param string $url
   * @return string
   */
  public static function getInfoFeaturedTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $text_tag_featured, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_featured_title_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoFeaturedTitle = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoFeaturedTitle);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_featured_title_tag_' + IdSeoFeaturedTitle).val(data);
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
     </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $text_tag_featured
   * @param string $url
   * @return string
   */
  public static function getInfoFeaturedDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $text_tag_featured, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_featured_description\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idSeoFeaturedDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idSeoFeaturedDescription);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_featured_description_' + idSeoFeaturedDescription).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $text_tag_featured
   * @param string $url
   * @return string
   */
  public static function getInfoFeaturedKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $text_tag_featured, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"seo_featured_keywords\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let IdSeoFeaturedKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(IdSeoFeaturedKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}' + ' ' + '{$text_tag_featured}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#seo_featured_keywords_' + IdSeoFeaturedKeywords).val(data);
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
      </script>";

    return $script;
  }

//**************************************
// Categories
//**************************************
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question
   * @param string $categories_name
   * @param string $url
   * @return string
   */
  public static function getCategoriesSeoTitle(string $content, string $urlMultilanguage, string $translate_language, string $question, string $categories_name, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"categories_head_title_tag\"]').each(function(index) {
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
       </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $categories_name
   * @param string $url
   * @return string
   */
  public static function getCategoriesSeoDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $categories_name, string $url)
  {
    $script = "
      <script defer>
      $('[id^=\"categories_head_desc_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $categories_name
   * @param string $url
   * @return string
   */
  public static function getCategoriesSeoKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $categories_name, string $url)
  {
    $script = "
      <script defer>    
      $('[id^=\"categories_head_keywords_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }

//*********************
// Manufacturer
//*********************
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question
   * @param string $manufacturer_name
   * @param string $url
   * @return string
   */
  public static function getManufacturerSeoTitle(string $content, string $urlMultilanguage, string $translate_language, string $question, string $manufacturer_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"manufacturer_seo_title\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $manufacturer_name
   * @param string $url
   * @return string
   */
  public static function getManufacturerSeoDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $manufacturer_name, string $url)
  {
    $script = "
      <script defer>
        $('[id^=\"manufacturer_seo_description\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $manufacturer_name
   * @param string $url
   * @return string
   */
  public static function getManufacturerSeoKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $manufacturer_name, string $url)
  {
    $script = "
      <script defer>    
      $('[id^=\"manufacturer_seo_keyword\"]').each(function(index) {
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
      </script>";

    return $script;
  }

//*********************
// Page Manager
//*********************
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question
   * @param string $page_manager_name
   * @param string $url
   * @return string
   */
  public static function getPageManagerSeoTitle(string $content, string $urlMultilanguage, string $translate_language, string $question, string $page_manager_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"page_manager_head_title_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let idPageManagerSeoTitle = regex.exec(inputId)[0];
        
          let language_id = parseInt(idPageManagerSeoTitle);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question}' + ' ' + '{$page_manager_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#page_manager_head_title_tag_' + idPageManagerSeoTitle).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $page_manager_name
   * @param string $url
   * @return string
   */
  public static function getPageManagerSeoDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $page_manager_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"page_manager_head_desc_tag\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idPageManagerSeoDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idPageManagerSeoDescription);
          
            // Envoi d'une requête AJAX pour récupérer le nom de la langue
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$page_manager_name}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#page_manager_head_desc_tag_' + idPageManagerSeoDescription).val(data);
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $page_manager_name
   * @param string $url
   * @return string
   */
  public static function getPageManagerSeoKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $page_manager_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"page_manager_head_keywords_tag\"]').each(function(index) {
          let inputId = $(this).attr('id');
          let regex = /(\d+)/g;
          let idPageManagerSeoKeywords = regex.exec(inputId)[0];
        
          let language_id = parseInt(idPageManagerSeoKeywords);
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          // Envoi d'une requête AJAX pour récupérer le nom de la langue
          let self = this;
          $.ajax({
            url: '{$urlMultilanguage}',
            data: {id: language_id},
            success: function(language_name) {
              let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$page_manager_name}';
              
              newButton.click(function() {
                let message = questionResponse;
                let engine = $('#engine').val();
        
                $.ajax({
                  url: '{$url}',
                  type: 'POST',
                  data: {message: message, engine: engine},
                  success: function(data) {
                    $('#chatGpt-output-input').val(data);
                    $('#page_manager_head_keywords_tag_' + idPageManagerSeoKeywords).val(data);
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
      </script>";

    return $script;
  }

//*********************
// Products
//*********************
  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question
   * @param string $product_name
   * @param string $url
   * @return string
   */
  public static function getProductsSeoTitle(string $content, string $urlMultilanguage, string $translate_language, string $question, string $product_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"products_head_title_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $product_name
   * @param string $url
   * @return string
   */
  public static function getProductsSummaryDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $product_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"SummaryDescription\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $product_name
   * @param string $url
   * @return string
   */
  public static function getProductsSeoDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $product_name, string $url)
  {
    $script = "
      <script defer>    
        $('[id^=\"products_head_desc_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }


  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $product_name
   * @param string $url
   * @return string
   */
  public static function getProductsSeoKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $product_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"products_head_keywords_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_tag
   * @param string $product_name
   * @param string $url
   * @return string
   */
  public static function getProductsSeoTags(string $content, string $urlMultilanguage, string $translate_language, string $question_tag, string $product_name, string $url)
  {
    $script = "
      <script defer> 
        $('[id^=\"products_head_tag\"]').each(function(index) {
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
      </script>";

    return $script;
  }

//**************************************
//Recommendations
//**************************************

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_title
   * @param string $store_name
   * @param string $url
   */
  public static function getInfoSeoRecommendationsTitle(string $content, string $urlMultilanguage, string $translate_language, string $question_title, string $store_name, string $url): string
  {
    $script = "
        <script defer>
      $('[id^=\"seo_recommendations_title_tag\"]').each(function(index) {
        let inputId = $(this).attr('id');
        let regex = /(\d+)/g;
        let idSeoRecommendationsLanguageTitle = regex.exec(inputId)[0];
      
        let language_id = parseInt(idSeoRecommendationsLanguageTitle);
        let button = '{$content}';
        let newButton = $(button).attr('data-index', index);
      
        let self = this;
        $.ajax({
          url: '{$urlMultilanguage}',
          data: {id: language_id},
          success: function(language_name) {
            let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_title}' + ' ' + '{$store_name}';
            
            newButton.click(function() {
              let message = questionResponse;
              let engine = $('#engine').val();
      
              $.ajax({
                url: '{$url}',
                type: 'POST',
                data: {message: message, engine: engine},
                success: function(data) {
                  $('#chatGpt-output-input').val(data);
                  $('#seo_recommendations_title_tag_' + idSeoRecommendationsLanguageTitle).val(data);
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
  </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_summary_description
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoRecommendationsDescription(string $content, string $urlMultilanguage, string $translate_language, string $question_summary_description, string $store_name, string $url): string
  {
    $script = "
        <script defer>
        $('[id^=\"seo_recommendations_description_tag\"]').each(function(index) {
          let button = '{$content}';
          let newButton = $(button).attr('data-index', index);
        
          let textareaId = $(this).find('textarea').attr('id'); // Récupérer l'id du textarea pour l'itération actuelle
          // Vérifier si le textarea a été trouvé
          if (textareaId !== undefined) {
            let regex = /(\d+)/g;
            let idSeoRecommendationsDescription = regex.exec(textareaId)[0];
          
            let language_id = parseInt(idSeoRecommendationsDescription);
          
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' +  '{$question_summary_description}' + ' ' + '{$store_name}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_recommendations_description_tag_' + idSeoRecommendationsDescription).val(data);
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
        </script>";

    return $script;
  }

  /**
   * @param string $content
   * @param string $urlMultilanguage
   * @param string $translate_language
   * @param string $question_keywords
   * @param string $store_name
   * @param string $url
   * @return string
   */
  public static function getInfoSeoRecommendationsKeywords(string $content, string $urlMultilanguage, string $translate_language, string $question_keywords, string $store_name, string $url): string
  {
    $script = "
        <script defer>    
          $('[id^=\"seo_recommendations_keywords_tag\"]').each(function(index) {
            let inputId = $(this).attr('id');
            let regex = /(\d+)/g;
            let IdSeoRecommendationsKeywords = regex.exec(inputId)[0];
          
            let language_id = parseInt(IdSeoRecommendationsKeywords);
            let button = '{$content}';
            let newButton = $(button).attr('data-index', index);
          
            let self = this;
            $.ajax({
              url: '{$urlMultilanguage}',
              data: {id: language_id},
              success: function(language_name) {
                let questionResponse = '{$translate_language}' + ' ' + language_name + ' : ' + '{$question_keywords}' + ' ' + '{$store_name}';
                
                newButton.click(function() {
                  let message = questionResponse;
                  let engine = $('#engine').val();
          
                  $.ajax({
                    url: '{$url}',
                    type: 'POST',
                    data: {message: message, engine: engine},
                    success: function(data) {
                      $('#chatGpt-output-input').val(data);
                      $('#seo_recommendations_keywords_tag_' + IdSeoRecommendationsKeywords).val(data);
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
         </script>";

    return $script;
  }
}