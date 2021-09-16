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

  namespace ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\ClicShoppingAdmin\ModuleDownload;

  use ClicShopping\Apps\Tools\Upgrade\Upgrade as UpgradeApp;

  class Github
  {
    protected mixed $app;
    protected $github;
    protected string $githubRepo;
    protected $context;
    protected string $coreName;
    protected string $githubRepoClicShoppingCore;
    protected string $githubRepoName;
    protected string $saveFileFromGithub;
    protected string $cacheGithub;
    public string $cacheGithubTemp;
    protected string $ModuleInfosJson;

    public function __construct()
    {
      if (!Registry::exists('Upgrade')) {
        Registry::set('Upgrade', new UpgradeApp());
      }

      $this->app = Registry::get('Upgrade');

      $this->githubUrl = 'https://github.com';
      $this->githubApi = 'https://api.github.com';
      $this->githubRepo = 'repos';
      $this->context = stream_context_create(array('http' => array('header' => 'User-Agent: ClicShopping')));

      $this->coreName = 'ClicShopping';
      $this->githubRepoClicShoppingCore = 'ClicShopping_V3';

      if (isset($_POST['addons_apps'])) {
        if ($_POST['addons_apps'] == 'official') {
          $this->githubRepoName = 'ClicShoppingOfficialModulesV3';
        } else { // community
          $this->githubRepoName = 'ClicShoppingV3Community';
        }
      }

      $this->saveFileFromGithub = CLICSHOPPING::BASE_DIR . 'Work/Temp';
      $this->cacheGithub = CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/';
      $this->cacheGithubTemp = CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/Temp/';
      $this->ModuleInfosJson = 'ModuleInfosJson';
    }

    /*
    * getGithubCoreRepo : ClicShopping Core
    * @param
    * @return  $url url about the ClicShopping Core
    * @access
    */
    private function getGithubCoreRepo()
    {
      $url = $this->githubApi . '/' . $this->githubRepo . '/' . $this->coreName . '/' . $this->githubRepoClicShoppingCore;
      return $url;
    }

    /*
    * user agent in Header
    * @param
    * @return  user agent
    */
    private function setContext()
    {
      return $this->context;
    }

    /**
     * get ClicShopping Core Version from Github
     * @param
     * @return $version version of new clicshopping core
     *
     */
    public function getJsonCoreInformation()
    {
      $VersionCache = new Cache('clicshopping_core_information');

      if ($VersionCache->exists(30) !== false) {
        $result = $VersionCache->get();
      } else {
        if (is_file($this->getGithubCoreRepo() . '/contents/shop/includes/ClicShopping/version.json?ref=master')) {
          $json = @file_get_contents($this->getGithubCoreRepo() . '/contents/shop/includes/ClicShopping/version.json?ref=master', true, $this->context);

          $url = json_decode($json);

          $url_download = @file_get_contents($url->download_url, true, $this->setContext()); //content of readme.
          $data = json_decode($url_download);

          $result = $VersionCache->save($data);
        } else {
          $result = false;
        }
      }

      return $result;
    }

    /**
     * Extract Zip file
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @uses ZipArchive
     */
    private function getExtractZip(string $source, string $destination) :bool
    {
      $zip = new \ZipArchive;

      if ($zip->open($source) === true) {
        $zip->extractTo($destination);
        $zip->close();
        return true;
      } else {
        return false;
      }
    }

    /*
    * user open or Close the store during the upgrade process
    * @param $value,true / false
    * @return
    */
    private function getCloseOpenStore($value)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (\defined('STORE_OFFLINE')) {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_configuration
                                              set configuration_value = :configuration_value,
                                              last_modified = now()
                                              where configuration_key = :configuration_key
                                             ');
        $Qupdate->bindValue(':configuration_value', $value);
        $Qupdate->bindValue(':configuration_key', 'STORE_OFFLINE');
        $Qupdate->execute();
      }
    }

    /**
     * Check directory if exist or not
     * @param
     * @return Boolean true / false
     */
    private function checkDirectoryOnlineUpdate() :bool
    {
      if (!is_dir($this->saveFileFromGithub)) {
        if (!mkdir($concurrentDirectory = $this->saveFileFromGithub, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->saveFileFromGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithub)) {
        if (!mkdir($concurrentDirectory = $this->cacheGithub, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithub)) {
        return true;
      } else {
        return false;
      }

      if (!is_dir($this->cacheGithubTemp)) {
        if (!mkdir($concurrentDirectory = $this->cacheGithubTemp, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->cacheGithubTemp)) {
        return true;
      } else {
        return false;
      }
    }


    /**
     * get all modules directories for template
     * @return $module,values of array
     */
    public function getModuleTemplateDirectory()
    {
      $default_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/Default/modules';
      $module[] = [
        'id' => '0',
        'text' => $this->app->getDef('text_select_module_template')
      ];

      $exclude = [
        '..',
        '.',
        'customers_address',
        'download',
        'index.php',
        '_htaccess',
        '.htaccess'];

      $module_dir = array_diff(scandir($default_directory), $exclude);

      foreach ($module_dir as $filename) {
        $module[] = [
          'id' => $filename,
          'text' => str_replace('_ ', '', $filename)
        ];
      }

      return $module;
    }

    /**
     * get all modules directories (fix modules)
     * @param
     * @return $module,values of array
     *
     */
    public function getModuleDirectory() :array
    {
      $module = array(array(
        'id' => '0', 'text' => $this->app->getDef('text_select_module_fix')),
        array('id' => 'export', 'text' => 'Modules Export'),
        array('id' => 'header_tags', 'text' => 'Modules Header Tags'),
        array('id' => 'hooks', 'text' => 'Modules Hooks'),
        array('id' => 'catalog', 'text' => 'Modules Apps Catalog'),
        array('id' => 'communication', 'text' => 'Modules Apps Communication'),
        array('id' => 'configuration', 'text' => 'Modules Apps Configuration'),
        array('id' => 'customer', 'text' => 'Modules Apps Customer'),
        array('id' => 'marketing', 'text' => 'Modules Apps Marketing'),
        array('id' => 'order', 'text' => 'Modules Apps Orders'),
        array('id' => 'total', 'text' => 'Modules Apps Orders Total'),
        array('id' => 'payment', 'text' => 'Modules Apps Payment'),
        array('id' => 'listing', 'text' => 'Modules Apps Products listing'),
        array('id' => 'report', 'text' => 'Modules Apps Report'),
        array('id' => 'shipping', 'text' => 'Modules Apps Shipping'),
        array('id' => 'social', 'text' => 'Modules Apps Social network'),
        array('id' => 'tools', 'text' => 'Modules Apps Tools'),
        array('id' => 'service', 'text' => 'Modules Apps Web service'),
        array('id' => 'other', 'text' => 'Modules Apps Others'),
        array('id' => 'template', 'text' => 'Template Design'),
        array('id' => 'language', 'text' => 'Language'),
      );

      return $module;
    }

//*************************************************************
// Cache
//*************************************************************

    /*
     * Unlik the cache if the delay is passed
     * @param $module_name name of the module
     * @return  $file, file information
     * @access private
     */
    private function getCheckDateCache(string $module_name)
    {
      $life = 864000; // 1 month

      if (is_file($this->cacheGithub . $module_name)) {
        $cache_file = $this->cacheGithub . $module_name;
        $count = filemtime($cache_file) > (time() - $life);

        if ($count < 1 && filesize($cache_file)) {
          unlink($this->cacheGithub . $module_name);
        }
      } elseif (is_file($this->cacheGithubTemp . $module_name)) {
        $cache_file = $this->cacheGithubTemp . $module_name;
        $count = filemtime($cache_file) > (time() - $life);

        if ($count < 1 && filesize($cache_file)) {
          unlink($this->cacheGithubTemp . $module_name);
        }
      }
    }

    /**
     * Check if cache exit else create
     * @param string $module_name
     * @return bool|string
     */
    public function getCheckCacheFile(string $module_name)
    {
      if (!empty($module_name)) {
        $this->getCheckDateCache($module_name);

        if (is_file($this->cacheGithub . $module_name)) {
          $result = 'cacheGithub';
        } elseif (is_file($this->cacheGithubTemp . $module_name)) {
          $result = 'cacheGithubTemp';
        } else {
          $file_name = $module_name;
          $file_array = explode('.', $file_name);
          $extension = \count($file_array) - 1;
          $filename = substr($file_name, 0, \strlen($file_name) - \strlen($file_array[$extension]) - 1);

          $content_json_file = @file_get_contents($this->getGithubRepo() . $filename . '/contents/' . $this->ModuleInfosJson . '/' . $module_name . '?ref=master', true, $this->context);
          $content_download_file = json_decode($content_json_file);

          if (!empty($content_download_file->download_url)) {
            $file_link = $content_download_file->download_url;

            $file_json_content = @file_get_contents($file_link, true, $this->context);

            if (!empty($file_link)) {
              $headers = get_headers($file_link, true);

              if (isset($headers['Content-Length'])) {
                @file_put_contents($this->cacheGithubTemp . '/' . $module_name, $file_json_content);
              }

              $result = false;
            } else {
              $result = true;
            }
          } else {
            $result = false;
          }
        }

        return $result;
      }
    }

    /**
     * get the json file infromation about temporary directory
     * @param string $module_name
     * @return false|mixed
     */
    public function getCacheFileTemp(string $module_name)
    {
      $check = $this->getCheckCacheFile($module_name);

      if ($check == 'cacheGithubTemp') {
        $file = @file_get_contents($this->cacheGithubTemp . $module_name, true, $this->context);
        $file = json_decode($file);

        return $file;
      } else {
        return false;
      }
    }

    /**
     * @param string $module_name
     * @return mixed|null
     */
    public function getCacheFile(string $module_name)
    {
      $check = $this->getCheckCacheFile($module_name);

      if ($check == 'cacheGithub') {
        $file = @file_get_contents($this->cacheGithub . $module_name, true, $this->context);
        $file = json_decode($file);

        return $file;
      } else {
        return null;
      }
    }

//*************************************************************
// Limit
//*************************************************************

    /**
     * @return string
     */
    public function getSearchModule() :string
    {
      if (isset($_POST['install_module_template_directory']) && $_POST['install_module_template_directory'] != '0 '&& !empty($_POST['install_module_template_directory'])) {
        $result = HTML::sanitize($_POST['install_module_template_directory']);
      } elseif (isset($_POST['install_module_directory']) && $_POST['install_module_directory'] != '0 '&& !empty($_POST['install_module_directory'])) {
        $result = HTML::sanitize($_POST['install_module_directory']);
      } else {
        $result = HTML::sanitize($_POST['module_search']);
      }

      return $result;
    }

    /**
     * search inside Github repo
     * @param null $name
     * @return mixed
     */
    public function getSearchInsideRepo($name = null)
    {
      if (\is_null($name)) {
        $search = $this->githubApi . '/search/repositories?q=org%3A' . $this->githubRepoName . '+' . $this->getSearchModule();
        $search_url = @file_get_contents($search, true, $this->setContext()); //content of readme.
      } else {
        $search_url = @file_get_contents($name, true, $this->setContext()); //content of readme.
      }

      $result = json_decode($search_url);

      return $result;
    }

    /**
     * Count the total research
     * @return int
     */
    public function getSearchTotalCount() :int
    {
      $result = $this->getSearchInsideRepo();

      if(\is_object($result)) {
        $count = $result->total_count;
      } else {
        $count = 0;
      }

      return $count;
    }

    /**
     * getGithubRepo : Gisthub repo
     * @return string
     */
    private function getGithubRepo() :string
    {
      $url = $this->githubApi . '/' . $this->githubRepo . '/' . $this->githubRepoName . '/';

      return $url;
    }

    /**
     * get the version if exist on local
     * @param string $module_name
     * @return int
     */
    public function getJsonModuleLocalVersion(string $module_name)
    {
      if (is_file($this->cacheGithub . $module_name . '.json')) {
        $json = file_get_contents($this->cacheGithub . $module_name . '.json', true, $this->context);
        $result = json_decode($json);
        $version = $result->version;
      } elseif (is_file($this->cacheGithubTemp . $module_name . '.json')) {
        $json = file_get_contents($this->cacheGithubTemp . $module_name . '.json', true, $this->context);
        $result = json_decode($json);
        $version = $result->version;
      } else {
        $version = -1;
      }

      return $version;
    }

//******************************************
// Module
//******************************************

    /**
     * get the json of module content repository
     * @param string $module_name
     * @return mixed
     */
    public function getJsonRepoContentInformationModule(string $module_name)
    {
      $json = @file_get_contents($this->getGithubRepo() . $module_name . '/contents/' . $this->ModuleInfosJson . '?ref=master', true, $this->context);
      $result = json_decode($json);

//include cache in Github Tmp
      $this->getCacheFile($module_name);

      return $result;
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function getJsonModuleInformaton(string $url)
    {
      $json = @file_get_contents($url, true, $this->setContext());
      $result_content_module = json_decode($json);

      return $result_content_module;
    }

    /**
     * Github module download
     * @param string $module_name
     */
    public function getModuleMasterArchive(string $module_name)
    {
      if (!empty($module_name) || !\is_null($module_name)) {
        $url = HTML::sanitize($_POST['githubLink']);

        if (!empty($url)) {
          $authentication = @file_get_contents($url);

          file_put_contents($this->saveFileFromGithub . '/' . $module_name . '.zip', $authentication);
        } else {
          $this->app->rediret('Upgrade');
        }
      }
    }

    /**
     * Extract ClicShopping Core Zip to install inside ClicShopping
     * @param string $file
     */
    public function getInstallModuleTemplate(string $file)
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->checkDirectoryOnlineUpdate() === true) {
        $this->getCloseOpenStore('true');

        $this->getExtractZip($this->saveFileFromGithub . '/' . $file . '.zip', $this->saveFileFromGithub);

        if (is_dir($this->saveFileFromGithub . '/' . $file . '-master')) {
// copy json in cache
          $json_source = $this->saveFileFromGithub . '/' . $file . '-master/' . $this->ModuleInfosJson . '/';
          $json_destination = $this->cacheGithub;
          @ModuleDownload::smartCopy($json_source, $json_destination);

// copy files their directories
          $source = $this->saveFileFromGithub . '/' . $file . '-master';
          $dest_default_template = CLICSHOPPING::getConfig('dir_root', 'Shop');
          @ModuleDownload::smartCopy($source, $dest_default_template);

// copy in the current theme used
//          $dest_template = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'template/' . SITE_THEMA;
//          ModuleUpload::smartCopy($source, $dest_template);


          if (isset($source)) {
            @ModuleDownload::removeDirectory($source);
          }

          $CLICSHOPPING_MessageStack->add($this->app->getDef('success_files_installed'), 'success', 'header');
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'header');
        }

        $this->getCloseOpenStore('false');
      }

      $this->app->redirect('CoreUpgrade');
    }

    /**
     * Extract ClicShopping Core Zip to install inside ClicShopping
     * @param string $file
     */
    public function getInstallModuleFixe(string $file)
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->checkDirectoryOnlineUpdate() === true) {
        $this->getCloseOpenStore('true');

        $this->getExtractZip($this->saveFileFromGithub . '/' . $file . '.zip', $this->saveFileFromGithub);

        if (is_dir($this->saveFileFromGithub . '/' . $file . '-master')) {
// copy json in cache
          $json_source = $this->saveFileFromGithub . '/' . $file . '-master/' . $this->ModuleInfosJson . '/';
          $json_destination = $this->cacheGithub;

          @ModuleDownload::smartCopy($json_source, $json_destination);

// copy files their directories
          $source = $this->saveFileFromGithub . '/' . $file . '-master';
          $dest = CLICSHOPPING::getConfig('dir_root', 'Shop');

          @ModuleDownload::smartCopy($source, $dest);

          if (isset($source)) {
            @ModuleDownload::removeDirectory($source);
          }

          $CLICSHOPPING_MessageStack->add($this->app->getDef('success_file_installed'), 'success', 'header');
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'header');
        }

        $this->getCloseOpenStore('false');
      }

      $this->app->redirect('ModuleInstallResult&file=' . $file);
    }

    /**
     * @return string
     */
    public function getDropDownMenuSearchOption()
    {
      if (isset($_POST['addons_apps'])) {
        $addons_apps = HTML::sanitize($_POST['addons_apps']);
      } else {
        $addons_apps = '';
      }

      $array = [array('id' => 'official', 'text' => $this->app->getDef('text_official')),
                array('id' => 'community', 'text' => $this->app->getDef('text_community'))
      ];

      return HTML::selectMenu('addons_apps', $array, $addons_apps);
    }
  }