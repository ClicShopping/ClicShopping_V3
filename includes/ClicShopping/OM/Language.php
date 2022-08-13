<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Service\Shop\SEFU;

  class Language
  {
    protected string $language;
    protected array $languages = [];
    protected array $definitions = [];
    protected array $detectors = [];
    protected bool $use_cache = false;
    protected mixed $db;
    public string $code;

    /**
     * Language constructor.
     * @param null $code
     */
    public function __construct($code = null)
    {
      $this->db = Registry::get('Db');

      if (CLICSHOPPING::getSite() === 'Shop') {
        $Qlanguages = $this->db->prepare('select languages_id,
                                                  name,
                                                  code,
                                                  image,
                                                  directory,
                                                  status,
                                                  locale
                                           from :table_languages
                                           where status = 1
                                           order by sort_order
                                          ');
        $Qlanguages->setCache('languages-system');
        $Qlanguages->execute();
      } else {
        $Qlanguages = $this->db->prepare('select languages_id,
                                                 name,
                                                 code,
                                                 image,
                                                 directory,
                                                 status,
                                                 locale
                                           from :table_languages
                                           order by sort_order
                                          ');
        $Qlanguages->setCache('languages-system-admin');
        $Qlanguages->execute();
      }

      while ($Qlanguages->fetch()) {
        $this->languages[$Qlanguages->value('code')] = [
          'id' => (int)$Qlanguages->valueInt('languages_id'),
          'code' => $Qlanguages->value('code'),
          'name' => $Qlanguages->value('name'),
          'image' => $Qlanguages->value('image'),
          'directory' => $Qlanguages->value('directory'),
          'status' => (int)$Qlanguages->value('status'),
          'locale' => $Qlanguages->value('locale'),
        ];
      }

      if (!isset($code) || !$this->exists($code)) {
        if (isset($_SESSION['language'])) {
          $code = $_SESSION['language'];
        } else {
          $client = $this->getBrowserSetting();
          $code = ($client !== false) ? $client : DEFAULT_LANGUAGE;
        }
      }

      $this->set($code);

// Prevent LC_ALL from setting LC_NUMERIC to a locale with 1,0 float/decimal values instead of 1.0 (see bug #634)
      $system_locale_numeric = setlocale(LC_NUMERIC, 0);
      setlocale(LC_ALL, explode(',', $this->getLocale()));
      setlocale(LC_NUMERIC, $system_locale_numeric);
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
      $code = $this->getCode();
      return $this->get('locale', $code);
    }

    /**
     * Set Code
     * @param $code
     */
    protected function set($code)
    {
      $this->code = $code;

      if ($this->exists($this->code)) {
        $this->language = $this->code;
      } else {
        trigger_error('ClicShopping\OM\Language::set() - The language does not exist: ' . $this->code);
      }
    }

    public function getCode()
    {
      return $this->language;
    }

    /**
     * Check browser
     * @return bool|int|string
     */
    public function getBrowserSetting()
    {
      if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $languages = [
          'ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
          'be' => 'be|belarusian',
          'bg' => 'bg|bulgarian',
          'br' => 'pt[-_]br|brazilian portuguese',
          'ca' => 'ca|catalan',
          'cs' => 'cs|czech',
          'da' => 'da|danish',
          'de' => 'de([-_][[:alpha:]]{2})?|german',
          'el' => 'el|greek',
          'en' => 'en([-_][[:alpha:]]{2})?|english',
          'es' => 'es([-_][[:alpha:]]{2})?|spanish',
          'et' => 'et|estonian',
          'eu' => 'eu|basque',
          'fa' => 'fa|farsi',
          'fi' => 'fi|finnish',
          'fo' => 'fo|faeroese',
          'fr' => 'fr([-_][[:alpha:]]{2})?|french',
          'ga' => 'ga|irish',
          'gl' => 'gl|galician',
          'he' => 'he|hebrew',
          'hi' => 'hi|hindi',
          'hr' => 'hr|croatian',
          'hu' => 'hu|hungarian',
          'id' => 'id|indonesian',
          'it' => 'it|italian',
          'ja' => 'ja|japanese',
          'ko' => 'ko|korean',
          'ka' => 'ka|georgian',
          'lt' => 'lt|lithuanian',
          'lv' => 'lv|latvian',
          'mk' => 'mk|macedonian',
          'mt' => 'mt|maltese',
          'ms' => 'ms|malaysian',
          'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
          'no' => 'no|norwegian',
          'pl' => 'pl|polish',
          'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
          'ro' => 'ro|romanian',
          'ru' => 'ru|russian',
          'sk' => 'sk|slovak',
          'sq' => 'sq|albanian',
          'sr' => 'sr|serbian',
          'sv' => 'sv|swedish',
          'sz' => 'sz|sami',
          'sx' => 'sx|sutu',
          'th' => 'th|thai',
          'ts' => 'ts|tsonga',
          'tr' => 'tr|turkish',
          'tn' => 'tn|tswana',
          'uk' => 'uk|ukrainian',
          'ur' => 'ur|urdu',
          'vi' => 'vi|vietnamese',
          'tw' => 'zh[-_]tw|chinese traditional',
          'zh' => 'zh|chinese simplified',
          'ji' => 'ji|yiddish',
          'zu' => 'zu|zulu'
        ];

        foreach ($browser_languages as $browser_language) {
          foreach ($languages as $key => $value) {
            if (preg_match('/^(' . $value . ')(;q=[0-9]\\.[0-9])?$/i', $browser_language) && $this->exists($key)) {
              return $key;
            }
          }
        }
      }

      return false;
    }

    /**
     * Get language
     * @param null $data
     * @param null $language_code
     * @return mixed
     */
    public function get($data = null, $language_code = null)
    {
      if (!isset($data)) {
        $data = 'code';
      }

      if (!isset($language_code)) {
        $language_code = $this->language;
      }

      return $this->languages[$language_code][$data];
    }

    /**
     * get the  language id
     * @param null $language_code
     * @return int
     */
    public function getId($language_code = null)
    {
      return (int)$this->get('id', $language_code);
    }

    /**
     * get all language in array
     * @return array
     */
    public function getAll()
    {
      return $this->languages;
    }

    /**
     * Check the language
     *
     * @param return the code of the language
     *
     */
    public function exists($code)
    {
      return isset($this->languages[$code]);
    }

    /**
     * get image svg 4:3
     * @param $language_code
     * @param null $width
     * @param null $height
     * @return string
     */
    public function getImage(string $language_code, ?int $width = null, ?int $height = null) :string
    {
      if (!isset($width) || !is_int($width)) {
        $width = 28;
      }

      if (!isset($height) || !is_int($height)) {
        $height = 24;
      }

      if (CLICSHOPPING::getSite() === 'Shop') {
        $image = HTML::image('sources/third_party/flag-icon-css/flags/4x3/' . $this->get('image', $language_code) . '.svg', $this->get('name', $language_code), $width, $height);
      } else {
        $image = HTML::image('../sources/third_party/flag-icon-css/flags/4x3/' . $this->get('image', $language_code) . '.svg', $this->get('name', $language_code), $width, $height);
      }

      return $image;
    }

    public function getDef($key, $values = null, $scope = 'global')
    {
      if (isset($this->definitions[$scope][$key])) {
        $def = $this->definitions[$scope][$key];

        if (\is_array($values) && !empty($values)) {
          $def = $this->parseDefinition($def, $values);
        }

        return $def;
      }

      return $key;
    }

    /**
     * Parse the definition
     * @param $string
     * @param $values
     * @return null|string|string[]
     */
    public static function parseDefinition($string, $values)
    {
      if (\is_array($values) && !empty($values)) {
        $string = preg_replace_callback('/\{\{([A-Za-z0-9-_]+)\}\}/', function ($matches) use ($values) {
          return isset($values[$matches[1]]) ? $values[$matches[1]] : $matches[1];
        }, $string);
      }

      return $string;
    }

    /**
     * check if defintion exist
     * @param $group
     * @param null $language_code
     * @return bool|mixed
     */
    public function definitionsExist($group, $language_code = null)
    {
      $language_code = isset($language_code) && $this->exists($language_code) ? $language_code : $this->get('code');

      $site = CLICSHOPPING::getSite();

      if ((str_contains($group, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $group, $matches) === 1) && CLICSHOPPING::siteExists($matches[1])) {
        $site = $matches[1];
        $group = $matches[2];
      }

      If ($site == 'ClicShoppingAdmin') {
        $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'includes/languages/' . $this->get('directory', $language_code) . '/' . $group;
      } else {
        $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'sources/languages/' . $this->get('directory', $language_code) . '/' . $group;
      }

      $pathname .= '.txt';

      if (is_file($pathname)) {
        return true;
      }

      if ($language_code != DEFAULT_LANGUAGE) {
        return \call_user_func([$this, __FUNCTION__], $group, DEFAULT_LANGUAGE);
      }

      return false;
    }

    /**
     * Load the language
     * @param $group
     * @param null $language_code
     * @param null $scope
     * @param null $force_directory_language
     * @return bool
     */
    public function loadDefinitions($group, $language_code = null, $scope = null, $force_directory_language = null)
    {
      $language_code = isset($language_code) && $this->exists($language_code) ? $language_code : $this->get('code');

      if (!isset($scope)) {
        $scope = 'global';
      }

      $site = CLICSHOPPING::getSite();

      if ((str_contains($group, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $group, $matches) === 1) && CLICSHOPPING::siteExists($matches[1])) {
        $site = $matches[1];
        $group = $matches[2];
      }

      if (!\is_null($force_directory_language)) $site = $force_directory_language;

      If ($site == 'ClicShoppingAdmin') {
        $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'includes/languages/' . $this->get('directory', $language_code) . '/' . $group;
      } else {
        $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'sources/languages/' . $this->get('directory', $language_code) . '/' . $group;
      }

      $pathname .= '.txt';

      if ($language_code != DEFAULT_LANGUAGE) {
        \call_user_func([$this, __FUNCTION__], $group, DEFAULT_LANGUAGE, $scope);
      }

      $defs = $this->getDefinitions($site . '/' . $group, $language_code, $pathname);

      $this->injectDefinitions($defs, $scope);
    }

    /**
     * Get definition
     * @param $group
     * @param $language_code
     * @param $pathname
     * @return array|mixed
     */
    public function getDefinitions($group, $language_code, $pathname)
    {
      $defs = [];

      $group_key = str_replace(['/', '\\'], '-', $group);

      if ($this->use_cache === false) {
        return $this->getDefinitionsFromFile($pathname);
      }

      $DefCache = new Cache('languages-defs-' . $group_key . '-lang' . $this->getId($language_code));

      if ($DefCache->exists()) {
        $defs = $DefCache->get();
      } else {
        $Qdefs = $this->db->get('languages_definitions', [
          'definition_key',
          'definition_value'
        ], [
            'languages_id' => $this->getId($language_code),
            'content_group' => $group_key
          ]
        );

        while ($Qdefs->fetch()) {
          $defs[$Qdefs->value('definition_key')] = $Qdefs->value('definition_value');
        }

        if (empty($defs)) {
          $defs = $this->getDefinitionsFromFile($pathname);

          foreach ($defs as $key => $value) {
            $sql_array = [
              'languages_id' => $this->getId($language_code),
              'content_group' => $group_key,
              'definition_key' => $key,
              'definition_value' => $value
            ];

            $this->db->save('languages_definitions', $sql_array);
          }
        }

        $DefCache->save($defs);
      }

      return $defs;
    }

    /**
     * Get definition from file
     * @param $filename
     * @return array
     */
    public function getDefinitionsFromFile($filename)
    {
      $defs = [];

      if (is_file($filename)) {
        foreach (file($filename) as $line) {
          $line = trim($line);

          if (!empty($line) && (substr($line, 0, 1) != '#')) {
            $delimiter = strpos($line, '=');

            if (($delimiter !== false) && (preg_match('/^[A-Za-z0-9_-]/', substr($line, 0, $delimiter)) === 1) && (substr_count(substr($line, 0, $delimiter), ' ') === 1)) {
              $key = trim(substr($line, 0, $delimiter));
              $value = trim(substr($line, $delimiter + 1));

              $defs[$key] = $value;
            } elseif (isset($key)) {
              $defs[$key] .= "\n" . $line;
            }
          }
        }
      }

      return $defs;
    }

    /**
     * Inject definition
     * @param $defs
     * @param $scope
     */
    public function injectDefinitions($defs, $scope)
    {
      if (isset($this->definitions[$scope])) {
        $this->definitions[$scope] = array_merge($this->definitions[$scope], $defs);
      } else {
        $this->definitions[$scope] = $defs;
      }
    }

    /**
     * Set cache is used
     * @param $flag
     */
    public function setUseCache($flag)
    {
      $this->use_cache = ($flag === true);
    }

    /**Detect encoding
     * @param $filename
     * @return bool
     */
    public function detectFileEncoding($filename)
    {
      $response_encoding = 'UTF-8';
      $response_bom = ' without BOM';
      $handle = @fopen($filename, "r");

      if ((filesize($filename)) > 2) {
        $bom = fread($handle, 3);

        if ($bom == b"\xEF\xBB\xBF") {
          $response_bom = '-BOM';
        }

        $contents = fread($handle, filesize($filename));
        $response_encoding = mb_detect_encoding($contents, "UTF-8, ISO-8859-1", true);

        if ($response_encoding . $response_bom != 'UTF-8 without BOM') {
// error message
          error_log('ERROR: ' . $filename . ' file is not UTF-8 without BOM encoding');
        }
      }
      fclose($handle);

      return ($response_encoding . $response_bom == 'UTF-8 without BOM');
    }


    /**
     * Get the code language to display (french english)
     *
     * @param return code of the language
     *
     *
     */
    public function getLanguageCode()
    {
      if (!\is_null($this->getUrlValueLanguage())) {
        $_GET['language'] = $this->getUrlValueLanguage();
      }

      if (!isset($_SESSION['language']) || isset($_GET['language'])) {
        if (isset($_GET['language']) && !empty($_GET['language']) && $this->exists($_GET['language'])) {
          $this->set($_GET['language']);
        }

        $_SESSION['language'] = $this->get('code');

        return $_SESSION['language'];
      } else {
        return false;
      }
    }

    /**
     * Get the language value of the URL when Search engine is activate
     * @return $value_language, the value of the language
     * @return mixed|null
     */
    public function getUrlValueLanguage()
    {
      if (\defined('SEARCH_ENGINE_FRIENDLY_URLS') && (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && SEFU::start())) {
        $value_language = SEFU::getUrlValue();
      } else {
        $value_language = null;
      }

      return $value_language;
    }

    /**
     * Display the diffrent language under text
     *
     * @param string
     * @return string $languages_string, flag language
     *
     */
    public function getLanguageText($tag = ' - ')
    {
      $get_params = [];

      if (!isset($_GET['Checkout'])) {
        $languages_string = '';

        if (\is_array($_GET)) {
          foreach ($_GET as $key => $value) {
            if (($key != 'language') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y')) {
              $get_params[] = ($value) ? "$key=$value" : $key;
            }
          }
        }

        $get_params = implode('&', $get_params);

        if (!empty($get_params)) {
          $get_params .= '&';
        }

        foreach ($this->getAll() as $value) {
          $languages_string .= ' ' . HTML::link(CLICSHOPPING::link(null, $get_params . 'language=' . $value['code']), $value['name']) . $tag;
        }
      } // end language

      return $languages_string;
    }

    /**
     * Display the language flag image in catalog when the status is valid
     *
     * @param string
     * @return string $flag, flag language
     *
     */
    public function getFlag()
    {
      $get_params = [];
      $content = '';

      $languages = '';

      if (!isset($_GET['Checkout'])) {
// If only one language is selected
        if (CLICSHOPPING::getSite('Shop') == 'Shop') {
          $Qlanguages = $this->db->prepare('select languages_id,
                                                   code,
                                                   status
                                           from :table_languages
                                           where status = 1
                                           order by sort_order
                                          ');
          $Qlanguages->execute();
          $languages = $Qlanguages->fetchAll();
        } else {
          $languages = $this->getAll();
        }

        if (\is_array($_GET)) {
          foreach ($_GET as $key => $value) {
            if (($key != 'language') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y')) {
              $get_params[] = ($value) ? "$key=$value" : $key;
            }
          }
        }
      }

      $get_params = implode('&', $get_params);

      if (!empty($get_params)) {
        $get_params .= '&';
      }

      if (\is_array($languages)) {
        foreach ($languages as $value) {
          $content .= HTML::link(CLICSHOPPING::link(null, $get_params . 'language=' . $value['code']), $this->getImage($value['code'])) . '&nbsp;&nbsp;';
        }
      }

      return $content;
    }

    /**
     * the language
     *
     * @param string
     * @return string $languages_array,
     *
     */
    public function getLanguages()
    {

      $languages_array = [];

      $arraay = [
        'languages_id',
        'name',
        'code',
        'image',
        'directory'
      ];
      $Qlanguages = Registry::get('Db')->get('languages', $arraay, null, 'sort_order');

      while ($Qlanguages->fetch()) {
        $languages_array[] = [
          'id' => $Qlanguages->valueInt('languages_id'),
          'name' => $Qlanguages->value('name'),
          'code' => $Qlanguages->value('code'),
          'image' => $Qlanguages->value('image'),
          'directory' => $Qlanguages->value('directory')
        ];
      }

      return $languages_array;
    }

    /**
     * get language name in function the id of the language
     *
     * @param string
     * @return string name, name of the language id
     *
     */
    public function getLanguagesName($id)
    {
      $Qlanguages = Registry::get('Db')->get('languages', [
        'languages_id',
        'name'
      ],
        ['languages_id' => (int)$id]
      );

      return $Qlanguages->value('name');
    }

    /**
     * get All language or not
     *
     * @param string $option to display all language or not true, false
     * @return array $values_languages_id,, languages
     *
     */
    public function getAllLanguage($option = false)
    {
      $languages = $this->getLanguages();

      if ($option === true) {
        $values_languages_id[0] = [
          'id' => '0',
          'text' => CLICSHOPPING::getDef('text_all_languages')
        ];
      }

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $values_languages_id[$i + 1] = [
          'id' => $languages[$i]['id'],
          'text' => $languages[$i]['name']
        ];
      }

      return $values_languages_id;
    }
  }
