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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

  class FooterOutputBootstrapLinkTab
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!-- Bootstrap Link tab Script start-->' . "\n";
      $output .= '
<!-- if the page request contains a link to a tab, open that tab on page load -->
<script>
    $(function () {
        var url = document.location.toString();

        if (url.match(\'#\')) {
            if ($(\'.nav-tabs a[data-target="#\' + url.split(\'#\')[1] + \'"]\').length === 1) {
                $(\'.nav-tabs a[data-target="#\' + url.split(\'#\')[1] + \'"]\').tab(\'show\');
            }
        }
    });
</script>
        ' . "\n";
      $output .= '<!--Bootstrap Link tab end -->' . "\n";

      return $output;
    }
  }