<?php
/**
 *
 */

namespace ClicShopping\OM;

use ClicShopping\Service\Shop\SEFU;
use function call_user_func;
use function count;
use function defined;
use function is_array;
use function is_null;

/**
 * Class Language
 *
 * The Language class handles language management within the system. It provides methods to initialize
 * available languages, retrieve language settings, and detect browser language preferences.
 */
class Language
{
  public string $language;
  protected array $languages = [];
  protected array $definitions = [];
  protected array $detectors = [];
  protected bool $use_cache = false;
  private mixed $db;
  public string $code;

  /**
   * Constructor for the language management class.
   *
   * This method initializes the language system by loading available languages
   * from the database and setting the default or preferred language.
   * It also ensures system locale settings do not interfere with numeric formatting.
   *
   * @param string
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
   * Retrieves the locale information based on the current code.
   *
   * This*/
  public function getLocale()
  {
    $code = $this->getCode();

    return $this->get('locale', $code);
  }

  /**
   * Sets the language code and updates the language property.
   *
   * @param string $code The language code to be set.
   * @return void
   */
  protected function set(string $code): void
  {
    $this->code = $code;

    if ($this->exists($this->code)) {
      $this->language = $this->code;
    } else {
      $this->language = 'en';
    }
  }

  /**
   *
   */
  public function getCode(): string
  {
    return $this->language;
  }

  /**
   *
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
   * Retrieves the value associated with the specified data key and language code from the languages array.
   *
   * @param string|null $data The key for the data to be retrieved. Defaults to 'code' if not provided.
   * @param string|null $language_code
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
   * Retrieves the ID value associated with the specified language code.
   *
   * @param string|null $language_code The language code to retrieve the ID for.
   */
  public function getId($language_code = null)
  {
    return (int)$this->get('id', $language_code);
  }

  /**
   *
   */
  public function getAll(): array
  {
    return $this->languages;
  }

  /**
   * Checks if the given code exists in the languages array.
   *
   * @param string $code The code to check for existence.
   * @return bool True if the code exists, false otherwise.
   */
  public function exists(string $code): bool
  {
    return isset($this->languages[$code]);
  }

  /**
   * Generates an image URL based on the given language code and dimensions.
   *
   * @param string $language_code The language code for which the image is generated.
   * @param int|null $width Optional. The width of the image. Defaults to 28 if not provided.
   * @param int|null $height Optional
   */
  public function getImage(string $language_code,  int|null $width = null,  int|null $height = null): string
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

  /**
   * Retrieves the definition associated with a specific key within a given scope.
   *
   * @param string $key The key for the definition to retrieve.
   * @param array|null $values Optional values to replace placeholders in the definition.
   * @param string $scope
   */
  public function getDef($key, $values = null, $scope = 'global')
  {
    if (isset($this->definitions[$scope][$key])) {
      $def = $this->definitions[$scope][$key];

      if (is_array($values) && !empty($values)) {
        $def = $this->parseDefinition($def, $values);
      }

      return $def;
    }

    return $key;
  }

  /**
   * Parses a string*/
  public static function parseDefinition($string, $values)
  {
    if (is_array($values) && !empty($values)) {
      $string = preg_replace_callback('/\{\{([A-Za-z0-9-_]+)\}\}/', function ($matches) use ($values) {
        return isset($values[$matches[1]]) ? $values[$matches[1]] : $matches[1];
      }, $string);
    }

    return $string;
  }

  /**
   *
   */
  public function definitionsExist($group, $language_code = null)
  {
    $language_code = isset($language_code) && $this->exists($language_code) ? $language_code : $this->get('code');

    $site = CLICSHOPPING::getSite();

    if ((str_contains($group, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $group, $matches) === 1) && CLICSHOPPING::siteExists($matches[1])) {
      $site = $matches[1];
      $group = $matches[2];
    }

    if ($site == 'ClicShoppingAdmin') {
      $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'includes/languages/' . $this->get('directory', $language_code) . DIRECTORY_SEPARATOR . $group;
    } else {
      $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'sources/languages/' . $this->get('directory', $language_code) . DIRECTORY_SEPARATOR . $group;
    }

    $pathname .= '.txt';

    if (is_file($pathname)) {
      return true;
    }

    if ($language_code != DEFAULT_LANGUAGE) {
      return call_user_func([$this, __FUNCTION__], $group, DEFAULT_LANGUAGE);
    }

    return false;
  }

  /**
   * Loads language definitions for a specific group and optionally a language code, scope, or forced directory language.
   *
   * @param string $group The group name or identifier for which language definitions are to be loaded.
   * @param string|null $language_code Optional.
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

    if (!is_null($force_directory_language)) $site = $force_directory_language;

    if ($site == 'ClicShoppingAdmin') {
      $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'includes/languages/' . $this->get('directory', $language_code) . DIRECTORY_SEPARATOR . $group;
    } else {
      $pathname = CLICSHOPPING::getConfig('dir_root', $site) . 'sources/languages/' . $this->get('directory', $language_code) . DIRECTORY_SEPARATOR . $group;
    }

    $pathname .= '.txt';

    if ($language_code != DEFAULT_LANGUAGE) {
      call_user_func([$this, __FUNCTION__], $group, DEFAULT_LANGUAGE, $scope);
    }

    $defs = $this->getDefinitions($site . DIRECTORY_SEPARATOR . $group, $language_code, $pathname);

    $this->injectDefinitions($defs, $scope);
  }

  /**
   *
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
   * Parses a file to extract key-value definitions.
   *
   * @param string $filename The path to the file containing definitions.
   * @return array An associative array of key-value pairs parsed from the file.
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
   * Injects a set of definitions into a specific scope.
   *
   * @param array $defs The definitions to be injected.
   * @param string $scope The scope in which the definitions will be injected.
   * @return void
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
   *
   */
  public function setUseCache($flag)
  {
    $this->use_cache = ($flag === true);
  }

  /**
   * Detects the encoding of a file and verifies if it is 'UTF-8 without BOM'.
   *
   * @param string $filename The path to the file whose encoding is to be checked.
   * @return bool Returns true if the file encoding is 'UTF-
   */
  public function detectFileEncoding($filename)
  {
    $response_encoding = 'UTF-8';
    $response_bom = ' without BOM';
    $handle = @fopen($filename, "r");

    if ((filesize($filename)) > 2) {
      $bom = fread($handle, 3);

      if ($bom == "\xEF\xBB\xBF") {
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
   *
   */
  public function getLanguageCode()
  {
    if (!is_null($this->getUrlValueLanguage())) {
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
   * Retrieves the language value from the URL depending on the configuration settings.
   *
   * The method checks if search engine friendly URLs are enabled and uses SEFU::getUrlValue()
   * to extract the language value if*/
  public function getUrlValueLanguage()
  {
    if (defined('SEARCH_ENGINE_FRIENDLY_URLS') && (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && SEFU::start())) {
      $value_language = SEFU::getUrlValue();
    } else {
      $value_language = null;
    }

    return $value_language;
  }

  /**
   * Generates a formatted string containing links to available languages with optional GET parameters.
   *
   * @param string $tag The string delimiter to be used between the language links. Default is ' - '.
   * @return string A string containing the language links separated by the
   */
  public function getLanguageText($tag = ' - ')
  {
    $get_params = [];

    if (!isset($_GET['Checkout'])) {
      $languages_string = '';

      if (is_array($_GET)) {
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

      if (is_array($_GET)) {
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

    if (is_array($languages)) {
      foreach ($languages as $value) {
        $content .= HTML::link(CLICSHOPPING::link(null, $get_params . 'language=' . $value['code']), $this->getImage($value['code'])) . '&nbsp;&nbsp;';
      }
    }

    return $content;
  }

  /**
   * Retrieves an array of available languages with their respective details.
   *
   * @return array An array where each element is an associative array containing:
   *               - 'id': The ID of the language (integer).
   *               - 'name': The name*/
  public function getLanguages()
  {

    $languages_array = [];

    $array = [
      'languages_id',
      'name',
      'code',
      'image',
      'directory'
    ];
    
    $Qlanguages = Registry::get('Db')->get('languages', $array, null, 'sort_order');

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
   * Retrieves the name of a language based on its ID.
   *
   * @param int $id The ID of the language to retrieve the name for.
   * @return string|null The name of the language if found, or null if not found.
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
   * Retrieves a list of all languages, optionally including a default "all languages" option.
   *
   * @param bool $option When set to true, includes an "all languages" option in the returned list. Defaults to false.
   *
   * @return array An array of languages where each language is represented by an associative array with 'id' and 'text' keys.
   */
  public function getAllLanguage(bool $option = false): array
  {
    $languages = $this->getLanguages();

    if ($option === true) {
      $values_languages_id[0] = [
        'id' => '0',
        'text' => CLICSHOPPING::getDef('text_all_languages')
      ];
    }

    for ($i = 0, $n = count($languages); $i < $n; $i++) {
      $values_languages_id[$i + 1] = [
        'id' => $languages[$i]['id'],
        'text' => $languages[$i]['name']
      ];
    }

    return $values_languages_id;
  }
}
