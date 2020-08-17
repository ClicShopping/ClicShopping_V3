<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class HTML
  {
    /**
     * Parse a user submited value
     *
     * @param string $string The string to parse and output
     * @param array $translate An array containing the characters to parse
     * @return string
     */

    public static function output(?string $string, array $translate = null): string
    {

      if (is_null($string) || empty($string)) {
        return '';
      }

      if (!isset($translate)) {
          $translate = [
            '"' => '\''
          ];
      }

      return strtr(trim($string), $translate);
    }

    /**
     * Strictly parse a user submited value
     *
     * @param string $string The string to strictly parse and output
     * @return string
     */
    public static function outputProtected(?string $string): string
    {
      if (is_null($string) || empty($string)) {
        return '';
      }

      return htmlspecialchars(trim($string), ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Sanitize a user submited value
     *
     * @param string $string The string to sanitize
     * @return string
     */

    public static function sanitize($string)
    {
      if (is_null($string) || empty($string)) {
        return '';
      }
    $patterns = [
      '/ +/',
      '/[<>]/',
      '/&lt;/',
      '/&gt;/',
      '/%3c/',
      '/%2f/'
    ];

    $replace = [
      ' ',
      '_',
      '_',
      '_',
      '_',
      '_'
    ];

      return preg_replace($patterns, $replace, $string) ?? '';
    }


    /**
     * Generate a <a href> tag and link to an element
     *
     * @param $url The url to link to
     * @param $element The element to link to
     * @param $parameters Additional parameters for the a href tag
     * @return string
     */

    public static function link($url, $element, $parameters = null)
    {
      return '<a href="' . $url . '"' . (!empty($parameters) ? ' ' . $parameters : '') . '>' . $element . '</a>';
    }

    /*
    * read the first bit of a file
    *
    * @param string $url url of image
    * @return string
    */
    public static function getUrlFileExists($url) :bool
    {
      if (@file_get_contents($url, false, NULL, 0, 1)) {
        return true;
      }

      return false;
    }

    /*
    * Generate an <img> tag
    *
    * @param string $image The image filename to display
    * @param string $title The title of the image button
    * @param int $width The width of the image
    * @param int $height The height of the image
    * @param string $parameters Additional parameters for the image
    * @return string
    */

    public static function image(string $src= '',  ?string $alt = null,  ?int $width = null,  ?int $height = null, ?string $parameters = '', bool $responsive = true, string $bootstrap_css = '') :string
    {
      if ((empty($src) || ($src == CLICSHOPPING::linkImage(''))) && (IMAGE_REQUIRED == 'false')) {
        return false;
      }

      if (CLICSHOPPING::getSite() == 'Shop') {
        $CLICSHOPPING_Template = Registry::get('Template');

        if ((empty($src) || is_null($src) || static::getUrlFileExists($src) === false) && IMAGE_REQUIRED == 'true') {
          $image = CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'icons/nophoto.png';

          if (!is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . $image)) {
            $src = 'images/nophoto.png';
          } else {
            $src = $image;
          }
        }
      } else {
        if ((empty($src) || is_null($src) || static::getUrlFileExists($src) === false) && IMAGE_REQUIRED == 'true') {
          $src = CLICSHOPPING::getConfig('http_path', 'Shop') . 'images/nophoto.png';
        }
      }

      if (CLICSHOPPING::getSite() == 'Shop') {
        $image = '<img data-src="' . static::output(CLICSHOPPING::getConfig('http_server') . CLICSHOPPING::getConfig('http_path', 'Shop') . $src) . '" alt="' . static::output($alt) . '"';
      } else {
        $image = '<img src="' . static::output($src) . '" alt="' . static::output($alt) . '"';
      }

      if (isset($alt) && (strlen($alt) > 0)) {
        $image .= ' title="' . static::output($alt) . '"';
      }

      if (isset($width) && (strlen($width) > 0)) {
        $image .= ' width="' . static::output($width) . '"';
      }

      if (isset($height) && (strlen($height) > 0)) {
        $image .= ' height="' . static::output($height) . '"';
      }

      $class = [];

      if (CLICSHOPPING::getSite() == 'Shop') {
        $class[] = 'lozad media-object';
      } else {
        $class[] = 'media-object';
      }

      if ($responsive === true) {
        $class[] = 'img-fluid';
      }

      if (!empty($bootstrap_css)) {
        $class[] = $bootstrap_css;
      }

      if (!empty($class)) {
        $image .= ' class="' . implode(' ', $class) . '"';
      }

      if (!empty($parameters)) {
        $image .= ' ' . $parameters;
      }

      $image .= ' />';

      return $image;
    }

    /**
     * Generate a form
     *
     * @param string $name : form name
     * @param string $action :  action type
     * @param string $method : post method
     * @param string $parameters : parameterstype
     * @param string $tokenize : false - true
     *
     * @return string session_name(), session_name()
     */

    public static function form(string $name, ?string $action = null, ?string $method = 'post', ?string $parameters = '', array $flags = []) :string
    {
      if (!isset($flags['tokenize']) || !is_bool($flags['tokenize'])) {
        $flags['tokenize'] = false;
      }

      if (!isset($flags['session_id']) || !is_bool($flags['session_id'])) {
        $flags['session_id'] = false;
      }

      $form = '<form name="' . static::output($name) . '" action="' . static::output($action) . '" method="' . static::output($method) . '"';

      if (!empty($parameters)) {
        $form .= ' ' . $parameters;
      }

      $form .= '>';

      if (isset($flags['action'])) {
        $form .= static::hiddenField('action', $flags['action']);
      }

      if (($flags['session_id'] === true) && Registry::get('Session')->hasStarted() && (strlen(SID) > 0) && !Registry::get('Session')->isForceCookies()) {
        $form .= static::hiddenField(session_name(), session_id());
      }

      if (($flags['tokenize'] === true) && isset($_SESSION['sessiontoken'])) {
        $form .= static::hiddenField('formid', $_SESSION['sessiontoken']);
      }

      return $form;
    }

    /**
     * Generate a form input field (text/password)
     *
     * @param string $name The name and ID of the input field
     * @param string $value The default value for the input field
     * @param string $parameters Additional parameters for the input field
     * @param string $type The type of input field to use (text/password/file)
     * @param bool $reinsert_value
     * @param string $class
     * @return string
     */

    public static function inputField($name,  $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $class = 'form-control')
    {
      $field = '<input type="' . static::output($type) . '" name="' . static::output($name) . '"';

      if (($reinsert_value === true) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
        if (isset($_GET[$name]) && is_string($_GET[$name])) {
          $value = $_GET[$name];
        } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
          $value = $_POST[$name];
        }
      }

      if (strlen($value) > 0) {
        $field .= ' value="' . static::output($value) . '"';
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters . ' class="' . $class . '"';
      } else {
        if (!empty($class)) {
          $field .= ' class="' . $class . '"';
        }
      }

      $field .= ' />';

      return $field;
    }

    /**
     * Generate a form password field
     *
     * @param string $name The name and ID of the password field
     * @param string|null $value
     * @param string $parameters Additional parameters for the password field
     * @return string
     */

    public static function passwordField(string $name, ?string $value = null, string $parameters = 'maxlength="40"') :string
    {
      return static::inputField($name, $value, $parameters, 'password', false);
    }

    /**
     * Generate a form selection field (checkbox/radio)
     *
     * @param string $name The name and indexed ID of the selection field
     * @param string $type The type of the selection field (checkbox/radio)
     * @param mixed $values The value of, or an array of values for, the selection field
     * @param string $default The default value for the selection field
     * @param string $parameters Additional parameters for the selection field
     * @param string $separator The separator to use between multiple options for the selection field
     * @return string
     */

    protected static function selectionField(string $name, string $type, $values = null, $default = null, $parameters = null, string $separator = '&nbsp;&nbsp;')
    {
      if (!is_array($values)) {
        $values = array($values);
      }

      if (strpos($name, '[') !== false) {
        $name_string = substr($name, 0, strpos($name, '['));

        if (isset($_GET[$name_string])) {
          $default = $_GET[$name_string];
        } elseif (isset($_POST[$name_string])) {
          $default = $_POST[$name_string];
        }
      } else {
        if (isset($_GET[$name])) {
          $default = $_GET[$name];
        } elseif (isset($_POST[$name])) {
          $default = $_POST[$name];
        }
      }

      $field = '';

      $counter = 0;

      foreach ($values as $key => $value) {
        $counter++;

        if (is_array($value)) {
          $selection_value = $value['id'];
          $selection_text = $value['text'];
        } else {
          $selection_value = $value;
          $selection_text = '';
        }

        if (empty($selection_value)) {
          $selection_value = 'on';
        }

        $field .= '<input type="' . static::output($type) . '" name="' . static::outputProtected($name) . '"';


        if (strpos($parameters, 'id=') === false) {
          $field .= ' id="' . static::output($name) . (count($values) > 1 ? '_' . $counter : '') . '"';
        } elseif (count($values) > 1) {
          $offset = strpos($parameters, 'id="');
          $field .= ' id="' . static::output(substr($parameters, $offset + 4, strpos($parameters, '"', $offset + 4) - ($offset + 4))) . '_' . $counter . '"';
        }

        $field .= ' value="' . static::output($selection_value) . '"';

        if (isset($default) && (($default === true) || (!is_array($default) && ((string)$default == (string)$selection_value)) || (is_array($default) && in_array($selection_value, $default)))) {
          $field .= ' checked="checked"';
        }

        if (!empty($parameters)) {
          $field .= ' ' . $parameters;
        }

        $field .= ' />';

        if (!empty($selection_text)) {
          $field .= '<label for="' . static::output($name) . (count($values) > 1 ? '_' . $counter : '') . '" class="fieldLabel">' . $selection_text . '</label>';
        }

        $field .= $separator;
      }

      if (!empty($field)) {
        $field = substr($field, 0, strlen($field) - strlen($separator));
      }

      return $field;
    }

    /**
     * Generate a form checkbox field
     *
     * @param string $name The name and indexed ID of the checkbox field
     * @param mixed $values The value of, or an array of values for, the checkbox field
     * @param string $default The default value for the checkbox field
     * @param string $parameters Additional parameters for the checkbox field
     * @param string $separator The separator to use between multiple options for the checkbox field
     * @return string
     */

    public static function checkboxField(string $name, $values = null, $default = null, $parameters = null, string $separator = '&nbsp;&nbsp;')
    {
      return static::selectionField($name, 'checkbox', $values, $default, $parameters, $separator);
    }

    /**
     * Generate a form radio field
     *
     * @param string $name The name and indexed ID of the radio field
     * @param mixed $values The value of, or an array of values for, the radio field
     * @param string $default The default value for the radio field
     * @param string $parameters Additional parameters for the radio field
     * @param string $separator The separator to use between multiple options for the radio field
     * @return string
     */

    public static function radioField($name, $values, $default = null, $parameters = null, $separator = '&nbsp;&nbsp;')
    {
      return static::selectionField($name, 'radio', $values, $default, $parameters, $separator);
    }

    /**
     * Generate a form textarea field
     *
     * @param string $name The name and ID of the textarea field
     * @param string $value The default value for the textarea field
     * @param int $width The width of the textarea field
     * @param int $height The height of the textarea field
     * @param string $parameters Additional parameters for the textarea field
     * @param boolean $override Override the default value with the value found in the GET or POST scope
     * @return string
     */

    public static function textAreaField($name, $value = null, $width = 60, $height = 5, $parameters = null, $override = true)
    {
      if (!is_bool($override)) {
        $override = true;
      }

      if ($override === true) {
        if (isset($_GET[$name])) {
          $value = $_GET[$name];
        } elseif (isset($_POST[$name])) {
          $value = $_POST[$name];
        }
      }

      if (!is_numeric($width)) {
        $width = 60;
      }

      if (!is_numeric($height)) {
        $width = 5;
      }

      $field = '<textarea name="' . static::output($name) . '" cols="' . static::output($width) . '" rows="' . static::output($height) . '"';

      if (strpos($parameters, 'id=') === false) {
        $field .= ' class="form-control" id="' . static::output($name) . '"';
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters;
      }

      $field .= '>' . static::outputProtected($value) . '</textarea>';

      return $field;
    }


    /**
     * Generate a form select menu field
     *
     * @param string $name The name of the pull down menu field
     * @param array $values Defined values for the pull down menu field [ id, text, group, params (since v3.0.2) ]
     * @param string $default The default value for the pull down menu field
     * @param string $parameters Additional parameters for the pull down menu field
     * @return string
     * =========> template fonctionne pas
     * HTML::selectMenu
     */

    public static function selectMenu($name, array $values, $default = null, $parameters = '', $required = false, $class = 'form-control')
    {
      $group = false;

      $field = '<select name="' . static::output($name) . '"';

      if ($required === true) {
        $field .= ' required aria-required="true"';
      }

      if (strpos($parameters, 'id=') === false) {
        $field .= ' id="' . static::output($name) . '"';
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters;
      }

      if (!empty($class)) {
        $field .= ' class="' . $class . '"';
      }

      $field .= '>';

      if ($required === true) {
        $field .= '<option value="">' . CLICSHOPPING::getDef('text_select') . '</option>';
      }

      if (empty($default) && ((isset($_GET[$name]) && is_string($_GET[$name]) && !is_null($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name] && !is_null($_POST[$name]))))) {
        if (isset($_GET[$name]) && is_string($_GET[$name])) {
          $default = static::output($_GET[$name]);
        } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
          $default = static::output($_GET[$name]);
        }
      }

      $ci = new \CachingIterator(new \ArrayIterator($values), \CachingIterator::TOSTRING_USE_CURRENT); // used for hasNext() below

      foreach ($ci as $v) {
        if (isset($v['group'])) {
          if ($group != $v['group']) {
            $group = $v['group'];

            $field .= '<optgroup label="' . static::output($v['group']) . '">';
          }
        }

        $field .= '<option value="' . static::output($v['id']) . '"';

        if (isset($default) && ($v['id'] == $default)) {
          $field .= ' selected="selected"';
        }

        if (isset($v['params'])) {
          $field .= ' ' . $v['params'];
        }

        $field .= '>' . static::output($v['text']) . '</option>';

        if (($group !== false) && (($group != $v['group']) || ($ci->hasNext() === false))) {
          $group = false;

          $field .= '</optgroup>';
        }
      }

      $field .= '</select>';

      return $field;
    }


    /**
     * Generate a form select menu field
     *
     * @param string $name The country name of the pull down menu field
     * @param array $selected selected country
     * @param string $default The default value for the pull down menu field
     * @param string $parameters Additional parameters for the pull down menu field
     * @return string
     */
    public static function selectMenuCountryList($name, $selected = null, $parameters = null)
    {
      $CLICSHOPPING_Address = Registry::get('Address');

      $countries_array = array(array('id' => '',
        'text' => CLICSHOPPING::getDef('text_select')
      )
      );
      $countries = $CLICSHOPPING_Address->getCountries();

      for ($i = 0, $n = count($countries); $i < $n; $i++) {
        $countries_array[] = ['id' => $countries[$i]['countries_id'],
          'text' => $countries[$i]['countries_name'],
          'iso' => $countries[$i]['countries_iso_code_2']
        ];
      }

      return static::selectMenu($name, $countries_array, $selected, $parameters);
    }

    /**
     * Generate a form hidden element
     *
     * @return string session_name(), session_name()
     */
    public static function hideSessionId(string $session_started, string $SID)
    {
      if (($session_started === true) && (!empty($SID) || !is_null($SID))) {
        return static::hiddenField(session_name(), session_name());
      } else {
        return false;
      }
    }


    /**
     * Generate a form file upload field
     *
     * @param string $name The name and $parameters, parameters like ID of the file upload field
     * @return string input fields
     */

    public static function fileField($name, $parameters = '')
    {
      return static::inputField($name, '', $parameters, 'file', false);
    }

    /**
     * Generate a time zone selection menu
     *
     * @param $name string The name of the selection field
     * @param $default The default value
     * @return string
     *
     */

    public static function timeZoneSelectMenu($name, $default = null)
    {
      if (!isset($default)) {
        $default = date_default_timezone_get();
      }

      $result = array();

      foreach (DateTime::getTimeZones() as $zone => $zones_array) {
        foreach ($zones_array as $key => $value) {
          $result[] = array('id' => $key,
            'text' => $value,
            'group' => $zone
          );
        }
      }

      return HTML::selectMenu($name, $result, $default);
    }

    /**
     * Word in a string if it is longer than a specified length ($len)
     *
     * @param string $string
     * @param string $len
     * @param string $break_char
     *
     */

    public static function breakString($string, $len, $break_char = '-')
    {
      $l = 0;
      $output = '';

      for ($i = 0, $n = strlen($string); $i < $n; $i++) {
        $char = substr($string, $i, 1);
        if ($char != ' ') {
          $l++;
        } else {
          $l = 0;
        }
        if ($l > $len) {
          $l = 1;
          $output .= $break_char;
        }
        $output .= $char;
      }

      return $output;
    }

    /**
     * Generate an image button
     *
     * @param string $image The image filename to display
     * @param string $title The title of the image button
     * @param string $parameters Additional parameters for the image
     * @return string
     */
    public static function imageButton($image, $title = '', $parameters = '', $responsive = false)
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!empty($responsive) && ($responsive === false)) {
        $image_responsive = ' class="img-fluid"';
      }

      return static::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'template/' . SITE_THEMA . '/' . $CLICSHOPPING_Language->get('directory') . '/' . $image, $title, '', '', $parameters, $image_responsive);
    }

    /**
     * Generate a dropdown
     *
     * @param string $name name of the field
     * @param array $values : the values of the field
     * @param string default : defaut value
     * @param string $parameters Additional parameters for the image
     * @param bool required : true false
     * @param $class : css class
     * @return string $field, result of dropdown
     */
    public static function selectField($name, array $values, $default = null, $parameters = '', $required = false, $class = 'form-control')
    {
      $group = false;

      $field = '<select name="' . static::output($name) . '"';

      if ($required === true) {
        $field .= ' required aria-required="true"';
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters;
      }

      if (!empty($class)) {
        $field .= ' class="' . $class . '"';
      }

      $field .= '>';

      if ($required === true) {
        $field .= '<option value="">' . CLICSHOPPING::getDef('entry_text_select') . '</option>';
      }

      if (empty($default) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
        if (isset($_GET[$name]) && is_string($_GET[$name])) {
          $default = $_GET[$name];
        } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
          $default = $_POST[$name];
        }
      }

      $ci = new \CachingIterator(new \ArrayIterator($values), \CachingIterator::TOSTRING_USE_CURRENT); // used for hasNext() below

      foreach ($ci as $v) {
        if (isset($v['group'])) {
          if ($group != $v['group']) {
            $group = $v['group'];

            $field .= '<optgroup label="' . static::output($v['group']) . '">';
          }
        }

        $field .= '<option value="' . static::output($v['id']) . '"';

        if (isset($default) && ($v['id'] == $default)) {
          $field .= ' selected="selected"';
        }

        if (isset($v['params'])) {
          $field .= ' ' . $v['params'];
        }

        $field .= '>' . static::output($v['text'], [
            '"' => '&quot;',
            '\'' => '&#039;',
            '<' => '&lt;',
            '>' => '&gt;'
          ]) . '</option>';

        if (($group !== false) && (($group != $v['group']) || ($ci->hasNext() === false))) {
          $group = false;

          $field .= '</optgroup>';
        }
      }

      $field .= '</select>';

      return $field;
    }

    /**
     * Generate a form hidden field
     *
     * @param string $name The name of the hidden field
     * @param string $value The value for the hidden field
     * @param string $parameters Additional parameters for the hidden field
     * @return string
     */

    public static function hiddenField($name, $value = '', $parameters = '')
    {
      $field = '<input type="hidden" name="' . static::output($name) . '"';

      if (strlen($value) > 0) {
        $field .= ' value="' . static::output($value) . '"';
      } elseif ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name]))) {
        if (isset($_GET[$name]) && is_string($_GET[$name])) {
          $field .= ' value="' . static::output($_GET[$name]) . '"';
        } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
          $field .= ' value="' . static::output($_POST[$name]) . '"';
        }
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters;
      }

      $field .= ' />';

      return $field;
    }

    /**
     * Generate a boostrap button
     *
     * @param string $title , title of the button
     * @param string $$icon, ui icon of the button
     * @param string $link , link
     * @param string $priotiy ; primary or secondary
     * @param string $param primary or secondaty
     * @param string size, sm, xs, md, lg button
     * @param return $button , the button
     */

    public static function button($title = null, $icon = null, $link = null, $style = null, $params = null, $size = null)
    {

      $types = ['submit', 'button', 'reset'];
      $styles = ['primary', 'info', 'success', 'warning', 'danger', 'inverse', 'link', 'new', 'secondary', 'dark', 'light', 'default'];
      $size_button = ['lg', 'md', 'sm'];

      if (!isset($params['type'])) {
        $params['type'] = 'submit';
      }

      if (!in_array($params['type'], $types)) {
        $params['type'] = 'submit';
      }

      if (($params['type'] == 'submit') && isset($link)) {
        $params['type'] = 'button';
      }

      if (isset($style) && !in_array($style, $styles)) {
        unset($style);
      }

      if (isset($size) && !in_array($size, $size_button)) {
        unset($size);
      }

      $button = '';

      if (($params['type'] == 'button') && isset($link)) {
        $button .= '<a href="' . $link . '"';
      } else {
        $button .= '<button type="' . static::outputProtected($params['type']) . '"';
      }

      if (isset($params['params'])) {
        $button .= ' ' . $params['params'];
      }


      $button .= ' class="btn ';

      if (isset($style)) {
        $button .= ' btn-' . $style;
      }

      if (isset($size)) {
        $button .= ' btn-' . $size;
      }

      $button .= '">';

      if (isset($icon) && !empty($icon)) {
        $button .= '<i class="' . $icon . '"></i> ';
      }

      $button .= $title;

      if (($params['type'] == 'button') && isset($link)) {
        $button .= '</a>';
      } else {
        $button .= '</button>';
      }

      return $button;
    }

    /**
     * Generate a star evaluation
     *
     * @param $rating string the nummber of the star
     * @param $empty The default value
     * @return string
     */
    public static function stars(int $rating = 0, bool $meta = true, string $style = 'text-warning'): string
    {
      $stars = str_repeat('<i class="fas fa-star ' . $style . '"></i>', $rating);

      if ($meta !== false) {
        $stars .= '<meta itemprop="rating" content="' . $rating . '" />';
      }

      return $stars;
    }

    /**
     * Generate a ticker on image
     *
     * @param $name : title of ticker
     * @param $css : css applied
     * @param $display :display or not
     * @return string
     */
    public static function tickerImage(?string $name = null, ?string $css = null, bool $display = false): string
    {
      $ticker = '';

      if ($display == 'true') {
        $ticker = '<span class="' . $css . '">' . $name . '</span>';
      }
      return $ticker;
    }

    /**
     * Function to select state with ISO Norm
     * public function
     * @param string $string , $name of the country
     * @param string $string , $selected of the country selected bu default
     * @param string $string , $parameters of the paramter
     * @return dropdown with country
     *
     */
    public static function selectMenuIsoList($name, $selected = '', $parameters = '')
    {
      $CLICSHOPPING_Address = Registry::get('Address');
      $countries_array = array(array('id' => '',
        'text' => CLICSHOPPING::getDef('entry_text_select'))
      );

      $countries = $CLICSHOPPING_Address->getCountries();

      for ($i = 0, $n = count($countries); $i < $n; $i++) {
        $countries_array[] = array('id' => $countries[$i]['countries_iso_code_2'],
          'text' => $countries[$i]['countries_name']
        );
      }

      return HTML::selectMenu($name, $countries_array, $selected, $parameters);
    }

    /**
     * Function to clean the metatag
     * public function
     * @param string $clean_html
     * @return string $its_cleaned  in the meta language
     * clean_html_comments
     */
    public static function cleanHtmlComments(string $clean_html): string
    {
      $its_cleaned = '';

      if (strpos($clean_html, '<!--//*') > 1) {
        $the_end1 = strpos($clean_html, '<!--//*') - 1;
        $the_start2 = strpos($clean_html, '*//-->') + 7;
        $its_cleaned = substr($clean_html, 0, $the_end1);
        $its_cleaned .= substr($clean_html, $the_start2);
      } else {
        $its_cleaned = $clean_html;
      }

      return $its_cleaned;
    }

    /**
     * Return a remove file emphasis
     *
     * @param string $accent , original character
     * @param string $new_accent ,  the remplacement
     * @param string $character , the new character converted
     * @param return $character ,, the new character converted
     * Doesn't work if inserted in general.php
     */

    public static function removeFileAccents(string $character): string
    {
      $accent = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'];
      $new_accent = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'];

      return str_replace($accent, $new_accent, $character);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $name
     * @return string
     */
    public static function replaceString(string $search=' ', string $replace='', string $name): string
    {
      return str_replace($search, $replace, $name);
    }
  }
