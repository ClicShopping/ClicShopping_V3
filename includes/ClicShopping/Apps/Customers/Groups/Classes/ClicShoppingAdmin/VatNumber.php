<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin;

  class VatNumber
  {
    /**
     * @return array
     */
    public static function getPrefixIntracomVAT() :array
    {
      $intracomArray = [
        'AT' => 'AT',
        //Austria
        'BE' => 'BE',
        //Belgium
        'DK' => 'DK',
        //Denmark
        'FI' => 'FI',
        //Finland
        'FR' => 'FR',
        //France
        'FX' => 'FR',
        //France métropolitaine
        'DE' => 'DE',
        //Germany
        'GR' => 'EL',
        //Greece
        'IE' => 'IE',
        //Irland
        'IT' => 'IT',
        //Italy
        'LU' => 'LU',
        //Luxembourg
        'NL' => 'NL',
        //Netherlands
        'PT' => 'PT',
        //Portugal
        'ES' => 'ES',
        //Spain
        'SE' => 'SE',
        //Sweden
        'CY' => 'CY',
        //Cyprus
        'EE' => 'EE',
        //Estonia
        'HU' => 'HU',
        //Hungary
        'LV' => 'LV',
        //Latvia
        'LT' => 'LT',
        //Lithuania
        'MT' => 'MT',
        //Malta
        'PL' => 'PL',
        //Poland
        'SK' => 'SK',
        //Slovakia
        'CZ' => 'CZ',
        //Czech Republic
        'SI' => 'SI',
        //Slovenia
        'RO' => 'RO',
        //Romania
        'BG' => 'BG',
        //Bulgaria
        'HR' => 'HR',
        //Croatia
        'XI' => 'XI'
        // Norhen Ireland
      ];

      return $intracomArray;
    }

    /**
     * @param string $country_iso
     * @return bool|void
     */
    public static function checkIsoCountry(string $country_iso)
    {
      if (strlen($country_iso) != 2) {
        return true;
      }

      foreach (static::getPrefixIntracomVAT() as $value) {
        if (strtoupper($value) == strtoupper($country_iso)) {
          return false;
        }
      }
    }

    /**
     * @return bool|\soapclient
     */
    public static function checkWebService()
    {
      $client = new \SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");

      if(!array($client)) {
//        $error_message = "web service at ec.europa.eu unavailable";
        return true;
      } else {
        return $client;
      }
    }

    /**
     * @param string $tva_intracom
     * @return string
     */
    public static function serviceCheckVat(string $country_iso, string $tva_intracom)
    {
      $error = false;

      $result = static::checkIsoCountry($country_iso);

      if ($result === true) {
        $error = true;
      }

      if (static::checkWebService() && $error === false) {
        $client = static::checkWebService();

        try {
          $array = [
            'countryCode' => $country_iso,
            'vatNumber' => $tva_intracom
          ];

          $response = $client->checkVat($array);
        }

        catch (SoapFault $e) {
          $faults = [
            'INVALID_INPUT' => 'The provided CountryCode is invalid or the VAT number is empty',
            'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
            'MS_UNAVAILABLE' => 'The Member State service is unavailable, try again later or with another Member State',
            'TIMEOUT' => 'The Member State service could not be reached in time, try again later or with another Member State',
            'SERVER_BUSY' => 'The service cannot process your request. Try again later.'
          ];

          $error_message = $faults[$e->faultstring];

          if (!is_array(is_set($error_message))) {
//            $error_message = $e->faultstring;
            $error = true;
          }
        }

        if (!array($response->valid)) {
//          $error_message = "Not a valid VAT number";
          $error = true;
        }

        if ($error === true) {
//          $error_message = '{ "success": 0, "error": "' . $error . '" }';
          return true;
        } else {
//          $error_message = '{ "success": 0, "error": "' . $error . '" }';
          return false;
        }
      } else {
        return true;
      }
    }

    /**
     * @param $response
     * @return string
     */
    public static function getInfoCompany($response) :string
    {
      $result = '';

      foreach ($response as $key => $prop) {
        $result .= ",\n  \"" . $key . "\": \"" . str_replace('"', '\"', $prop) . "\"";

        if ($key == 'name') {
          $name = $prop;
        } elseif ($key == 'address') {
          $address = $prop;
        }
      }

      $result .= "\n}";

      return $result;
    }
  }