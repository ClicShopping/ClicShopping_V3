<?php

  namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Customers;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ExportCustomerInfo extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $file = null;
    protected $use_site_template = false;

    public function execute()
    {

      $CLICSHOPPING_Db = Registry::get('Db');

      $customer_id = HTML::sanitize($_POST['customers_id']);

      $Qcustomers = $CLICSHOPPING_Db->prepare('select c.*,
                                                      a.*
                                                from :table_customers c left join :table_address_book a on c.customers_default_address_id = a.address_book_id
                                                where c.customers_id = :customers_id
                                              ');
      $Qcustomers->bindInt(':customers_id', $customer_id);
      $Qcustomers->execute();

      $customers = $Qcustomers->fetch();

      $head = '"customers_id", "customers_company", "customers_siret", "customers_ape", "customers_tva_intracom", "customers_tva_intracom_code_iso", "customers_gender", "customers_firstname", "customers_lastname", "customers_dob", "customers_email_address", "customers_telephone", "customers_fax", "customers_newsletter",  "entry_company", "entry_street_address", "entry_suburb", "entry_postcode", "entry_city", "entry_state", "entry_country_id", "entry_zone_id", "customers_default_address_id";' . '",';

      $header = 'Content-type: text/plain; Content-Disposition: "attachment; filename="customer_export.csv ';

      $output = '"' . $customers['customers_id'] . '",';
      $output .= '"' . $customers['customers_company'] . '",';
      $output .= '"' . $customers['customers_siret'] . '",';
      $output .= '"' . $customers['customers_ape'] . '",';
      $output .= '"' . $customers['customers_tva_intracom'] . '",';
      $output .= '"' . $customers['customers_tva_intracom_code_iso'] . '",';
      $output .= '"' . $customers['customers_gender'] . '",';
      $output .= '"' . $customers['customers_firstname'] . '",';
      $output .= '"' . $customers['customers_lastname'] . '",';
      $output .= '"' . $customers['customers_dob'] . '",';
      $output .= '"' . $customers['customers_email_address'] . '",';
      $output .= '"' . $customers['customers_telephone'] . '",';
      $output .= '"' . $customers['customers_fax'] . '",';
      $output .= '"' . $customers['customers_newsletter'] . '",';

      $output .= '"' . $customers['entry_company'] . '",';
      $output .= '"' . $customers['entry_street_address'] . '",';
      $output .= '"' . $customers['entry_suburb'] . '",';
      $output .= '"' . $customers['entry_postcode'] . '",';
      $output .= '"' . $customers['entry_city'] . '",';
      $output .= '"' . $customers['entry_state'] . '",';
      $output .= '"' . $customers['entry_country_id'] . '",';
      $output .= '"' . $customers['entry_zone_id'] . '",';
      $output .= '"' . $customers['customers_default_address_id'] . "\n";

      $foot = '';

      $content = $head . $output . $foot;

      Header($header);

      echo $content;

      exit;
    }
  }