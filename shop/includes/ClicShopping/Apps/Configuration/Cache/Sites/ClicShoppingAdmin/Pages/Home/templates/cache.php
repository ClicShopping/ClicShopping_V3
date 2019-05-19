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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  $CLICSHOPPING_Cache = Registry::get('Cache');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  // check if the cache directory exists
  if (is_dir(Cache::getPath())) {
    if (!FileSystem::isWritable(Cache::getPath())) $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Cache->getDef('error_cache_directory_not_writeable'), 'error');
  } else {
    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Cache->getDef('error_cache_directory_does_not_exist'), 'error');
  }

  $cache_files = [];

  foreach (glob(Cache::getPath() . '*.cache') as $c) {
    $key = basename($c, '.cache');

    if (($pos = strpos($key, '-')) !== false) {
      $cache_files[substr($key, 0, $pos)][] = $key;
    } else {
      $cache_files[$key][] = $key;
    }
  }

?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/cache.gif', $CLICSHOPPING_Cache->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Cache->getDef('heading_title'); ?></span>
          <span
            class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_Cache->getDef('button_reset'), null, $CLICSHOPPING_Cache->link('Cache&ResetAll'), 'danger'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table class="table table-sm table-hover table-striped">
    <thead>
    <tr class="dataTableHeadingRow">
      <th><?php echo $CLICSHOPPING_Cache->getDef('table_heading_cache'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_Cache->getDef('table_heading_cache_number_of_files'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_Cache->getDef('table_heading_action'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
      foreach (array_keys($cache_files) as $key) {
        ?>
        <tr>
          <th scope="row"><?php echo $key; ?></th>
          <td class="text-md-right"><?php echo count($cache_files[$key]); ?></td>
          <td
            class="text-md-right"><?php echo '<a href="' . $CLICSHOPPING_Cache->link('Cache&Reset&block=' . $key) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/icon_reset.gif', $CLICSHOPPING_Cache->getDef('button_reset'), 16, 16) . '</a>'; ?></td>
        </tr>
        <?php
      }
    ?>
    </tbody>
  </table>
  <div
    class="smalltext"><?php echo $CLICSHOPPING_Cache->getDef('text_cache_directory') . ' ' . FileSystem::displayPath(Cache::getPath()); ?></div>
</div>

