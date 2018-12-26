<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="SitemapSummaryTable">
    <div class="row col-md-12 col-md-12">
      <div class="col-md-4 col-md-4">
        <div class="SitemapSummary">
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(), CLICSHOPPING::getDef('sitemap_summary_text_home_page')); ?></li>
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Info&Contact'),  CLICSHOPPING::getDef('sitemap_summary_text_contact')); ?></li>
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Search&AdvancedSearch'),  CLICSHOPPING::getDef('sitemap_summary_text_search_advanced_search')); ?></li>
        </div>
      </div>
      <div class="col-md-4 col-md-4">
        <div class="SitemapSummary">
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Main'),  CLICSHOPPING::getDef('sitemap_summary_text_my_account')); ?></li>
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Create'),  CLICSHOPPING::getDef('sitemap_summary_text_create_account')); ?></li>
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&PasswordForgotten'), CLICSHOPPING::getDef('sitemap_summary_text_password')); ?></li>
        </div>
      </div>
      <div class="col-md-4 col-md-4">
        <div class="SitemapSummary">
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Products&Specials'),  CLICSHOPPING::getDef('sitemap_summary_text_products_specials')); ?></li>
          <li class="SitemapSummary"><?php echo HTML::link(CLICSHOPPING::link(null, 'Products&ProductsNew'),  CLICSHOPPING::getDef('sitemap_summary_text_products_whats_new')); ?></li>
        </div>
      </div>
    </div>
  </div>
</div>