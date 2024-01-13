<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\DateTime;
use ClicShopping\OM\ErrorHandler;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Template = Registry::get('TemplateAdmin');
$CLICSHOPPING_Language = Registry::get('Language');
$CLICSHOPPING_EditLogError = Registry::get('EditLogError');

$CLICSHOPPING_Page = Registry::get('Site')->getPage();

$files = [];

foreach (glob(ErrorHandler::getDirectory() . 'phpmail_error-*.txt') as $f) {
  $key = basename($f, '.txt');

  if (preg_match('/^phpmail_error-([0-9]{4})([0-9]{2})([0-9]{2})$/', $key, $matches)) {
    $files[$key] = [
      'path' => $f,
      'key' => $key,
      'date' => DateTime::toShort($matches[1] . '-' . $matches[2] . '-' . $matches[3]),
      'size' => filesize($f)
    ];
  }
}

$log = $files[$_GET['log']];
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/log.png', $CLICSHOPPING_EditLogError->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading">
<?php
echo '&nbsp;' . $CLICSHOPPING_EditLogError->getDef('heading_title') . ' -  ';
echo HTML::outputProtected($log['date']);
?>
            </span>
          <span class="col-md-6 text-end">
<?php
echo HTML::button($CLICSHOPPING_EditLogError->getDef('button_back'), null, $CLICSHOPPING_EditLogError->link('LogErrorPhpMailer'), 'primary') . ' ';

echo HTML::form('delete', $CLICSHOPPING_EditLogError->link('LogError&DeletePhpmailer&log=' . $log['key']));
echo HTML::button($CLICSHOPPING_EditLogError->getDef('button_delete'), null, null, 'danger');
?>
            </form>
           </span>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-1"></div>
  <div>
    <?php echo HTML::textAreaField('code', file_get_contents($log['path']), '', '', 'id="code"'); ?>
  </div>
</div>