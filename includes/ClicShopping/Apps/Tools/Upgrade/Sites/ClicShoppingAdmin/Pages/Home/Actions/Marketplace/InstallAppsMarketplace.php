<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions\Marketplace;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\ExtractFile;
  use ClicShopping\Apps\Tools\Upgrade\Upgrade as UpgradeApp;
  class InstallAppsMarketplace extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    private mixed $extractFile;

    public function __construct()
    {
      if (!Registry::exists('Upgrade')) {
        Registry::set('Upgrade', new UpgradeApp());
      }

      $this->app = Registry::get('Upgrade');

      if (!Registry::exists('ExtractFile')) {
        Registry::set('ExtractFile', new ExtractFile());
      }

      $this->extractFile = Registry::get('ExtractFile');
      $this->messageStack = Registry::get('MessageStack');
    }


    /**
     * @return bool
     */
    public function getMarketplaceFile(): bool
    {
      $id = HTML::sanitize($_POST['id']);
      $file_url_download = HTML::sanitize($_POST['file_url_download']);

      $check = $this->extractFile->checkFileFromSite($id);

      if (empty($file_url_download)) {
        $error = true;
      }

      if ($check !== false && $error === false && !empty($file_url_download)) {
        $this->extractFile->getCloseOpenStore('true');
        $filename_path = $this->extractFile->downloadFile($file_url_download);
        $this->extractFile->installFiles($filename_path);
        $this->extractFile->getCloseOpenStore('false');

        return true;
      } else {
        return false;
      }
    }

    /**
     * @return bool
     */
    private function saveFileUpload(): bool
    {
      $array_extension = ['zip'];

      $upload_file = new Upload('uploadApp', CLICSHOPPING::BASE_DIR . 'Work/Temp', null, $array_extension);

      if ($upload_file->check() && $upload_file->save()) {
        $uploadApp = $upload_file->getFilename();
        $file = HTML::removeFileAccents($uploadApp);

        $this->extractFile->getCloseOpenStore('true');
        $filename_path = $this->extractFile->downloadFile($file);
        $this->extractFile->installFiles($filename_path);
        $this->extractFile->getCloseOpenStore('false');

        return true;
      } else {
        return false;
      }
    }

    public function execute()
    {
      If (isset($_GET['InstallAppsMarketplace']) && isset($_GET['Marketplace'])) {
        $error = false;
        $check_directory = $this->extractFile->checkDirectory();

        if ($check_directory === false) {
          $error = true;
        }
/*
        if ($this->getMarketplaceFile() === true && $error === false) {
          $error = false;
        }
*/

        if ($error === false) {
          $this->messageStack->add($this->app->getDef('text_success_files_installed'), 'success', 'main');
        } else {
          $this->messageStack->add($this->app->getDef('error_file_not_installed'), 'error', 'main');
        }
      }
    }
  }