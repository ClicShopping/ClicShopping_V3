<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

// Alias function for module [boxes] configuration value
// template system
  function clic_cfg_set_select_pages_list($key_value, $key = null) {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    $name = ((!is_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = $CLICSHOPPING_Template->getListCatalogFilesNotIncluded();
    sort($select_array);

    $selected_array = explode(';', $key_value);

    if($key_value === 'all') {
      $checkall = "CHECKED";
    } else {
      $checkall = "UNCHECKED";
    }

    $string = '<fieldset>';
    $string .= HTML::radioField($name, 'all', $checkall, 'class="AllPages"') . CLICSHOPPING::getDef('text_all_pages') . '<br />';

    $string .= '<p><strong>&nbsp;&nbsp;' . CLICSHOPPING::getDef('text_one_by_one') . '</strong><br />';
    $string .= HTML::checkboxField('CheckAll', null, null, 'id="CheckAll" class="CheckAll"') . '<label id="CheckAllLabel" for="CheckAll">' . CLICSHOPPING::getDef('text_chek_all') . '</label></p>';

    for ($i=0, $n=count($select_array); $i<$n; $i++) {
      $string .= '&nbsp;&nbsp;<input type="checkbox" id="file_' . $i . '" class="ThisPage" name="' . $name . '" value="' . $select_array[$i] . ';"';
      if ( isset($selected_array) ) {
        foreach($selected_array as $value) {
          if ($select_array[$i] == $value) $string .= ' CHECKED';
        }
      }
      $string .= '><label class="ThisPage" for="file_' . $i . '">' . $select_array[$i] . '</label><br />';
    }
    $string .= '</fieldset>';
    $string .= "<script type=\"text/javascript\">
    jQuery(document).ready(function () {
      $('.AllPages').click(
        function() {
          $('.ThisPage').prop('checked', false);
          $('.CheckAll').prop('checked', false);
          $('#CheckAllLabel').text('" . CLICSHOPPING::getDef('text_chek_all') . "');
        }
      );
      $('.CheckAll').click(
        function () {
          $(this).parents('fieldset:eq(0)').find(':checkbox').prop('checked', this.checked);
          $('.AllPages').prop('checked', (!this.checked));
          if (this.checked) {
            $('#CheckAllLabel').text('" . CLICSHOPPING::getDef('text_deselect_all') . "');
          } else {
            $('#CheckAllLabel').text('" . CLICSHOPPING::getDef('text_chek_all') . "');
          }
        }
      );
      $('.ThisPage').click(
        function() {
          var n = $( \"input.ThisPage:checked\" ).length;
          if (n >0) {
            $('.AllPages').prop('checked', false);
          } else {
            $('.AllPages').prop('checked', true);
          }
        }
      );
    });
  </script>";
    return $string;
  }