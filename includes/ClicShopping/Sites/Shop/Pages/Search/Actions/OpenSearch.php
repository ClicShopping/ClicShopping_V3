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


  namespace ClicShopping\Sites\Shop\Pages\Search\Actions;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  header('Content-Type: text/xml');

  class OpenSearch extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      if (!\defined('MODULE_HEADER_TAGS_OPENSEARCH_STATUS') || (MODULE_HEADER_TAGS_OPENSEARCH_STATUS != 'True')) {
        exit;
      }

      $output = '<?xml version="1.0"?>' . "\n";
      $output .= '

      <OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
      <ShortName>' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_SHORT_NAME) . '</ShortName>
      <Description>' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_DESCRIPTION) . '</Description>
      ';
      if (!\is_null(MODULE_HEADER_TAGS_OPENSEARCH_SITE_CONTACT)) {
        $output .= '<Contact>' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_CONTACT) . '</Contact>' . "\n";
      }

      if (!\is_null(MODULE_HEADER_TAGS_OPENSEARCH_SITE_TAGS)) {
        $output .= ' <Tags>' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_TAGS) . '</Tags>' . "\n";
      }

      if (!\is_null(MODULE_HEADER_TAGS_OPENSEARCH_SITE_ATTRIBUTION)) {
        $output .= ' <Attribution>' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_ATTRIBUTION) . '</Attribution>' . "\n";
      }

      if (MODULE_HEADER_TAGS_OPENSEARCH_SITE_ADULT_CONTENT == 'True') {
        $output .= ' <AdultContent>True</AdultContent>' . "\n";
      }

      if (!\is_null(MODULE_HEADER_TAGS_OPENSEARCH_SITE_ICON)) {
        $output .= '<Image height="16" width="16" type="image/x-icon">' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_ICON) . '</Image>' . "\n";
      }

      if (!\is_null(MODULE_HEADER_TAGS_OPENSEARCH_SITE_IMAGE)) {
        $output .= '<Image height="64" width="64" type="image/png">' . HTML::output(MODULE_HEADER_TAGS_OPENSEARCH_SITE_IMAGE) . '</Image>' . "\n";
      }

      $output .= '
      <InputEncoding>UTF-8</InputEncoding>
      <Url type="text/html" method="get" template="' . CLICSHOPPING::link(null, 'Search&amp;Q&amp;keywords={searchTerms}', false, false) . '" />
      </OpenSearchDescription>
     ';

// templates
      return $output;
    }
  }