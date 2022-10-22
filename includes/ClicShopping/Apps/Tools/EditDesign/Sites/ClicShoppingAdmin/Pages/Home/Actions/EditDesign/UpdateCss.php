<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Tools\EditDesign\Sites\ClicShoppingAdmin\Pages\Home\Actions\EditDesign;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\HTML;

  class UpdateCss extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_EditDesign = Registry::get('EditDesign');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
      $CLICSHOPPING_Language = Registry::get('Language');

      $directory_selected = HTML::sanitize($_POST['directory_css']);
      $filename_selected = HTML::sanitize($_POST['filename']);
      $code = $_POST['code'];

      $file = CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDynamicTemplateDirectory() . '/css/' . $CLICSHOPPING_Language->get('directory') . '/' . $directory_selected . '/' . $filename_selected;

      if (is_file($file)) {
        $filename = CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDynamicTemplateDirectory() . '/css/' . $CLICSHOPPING_Language->get('directory') . '/' . $directory_selected . '/' . $filename_selected;
      } else {
        $file = CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDynamicTemplateDirectory() . '/css/english/' . $directory_selected . '/' . $filename_selected;

        if (is_file($file)) {
          $filename = CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDynamicTemplateDirectory() . '/css/english/' . $directory_selected . '/' . $filename_selected;
        } else {
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditDesign->getDef('error_file_does_not_exist'), 'error');
        }
      }

      if (FileSystem::isWritable($filename)) {
        $file = new \SplFileObject($filename, "w");
        $written = $file->fwrite($code);
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditDesign->getDef('success_file_saved_sucessfully'), 'success');
      } else {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditDesign->getDef('error_file_not_writeable'), 'error');
      }

      $CLICSHOPPING_EditDesign->redirect('EditCss&action=directory&directory_css=' . $directory_selected . '&filename=' . $filename_selected);


    }
  }
