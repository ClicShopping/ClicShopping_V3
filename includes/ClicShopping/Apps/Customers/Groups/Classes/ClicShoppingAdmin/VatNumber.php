<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin;

use SoapClient;
/**
 * Get the prefix for Intracommunity VAT numbers for various countries.
 *
 * This method returns an associative array where the keys represent ISO
 * code of countries and values represent the corresponding VAT prefix.
 *
 * @return array Returns an array of ISO country codes and their VAT prefixes.
 */
class VatNumber
{
  /**
   * Retrieves an associative array of intracommunity VAT prefixes for European Union countries.
   *
   * @return array An associative array where the keys represent country codes and the values are the corresponding VAT prefixes.
   */
  public static function getPrefixIntracomVAT(): array
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
   * Checks if the provided ISO country code is valid and present in the list of prefixes for Intracom VAT.
   *
   * @param string $country_iso The ISO country code to be checked.
   * @return bool Returns true if the country ISO is invalid or not found in the list; otherwise, false.
   */
  public static function checkIsoCountry(string $country_iso)
  {
    if (strlen($country_iso) != 2) {
      return true;
    }

    foreach (static::getPrefixIntracomVAT() as $value) {
      if (mb_strtoupper($value) == mb_strtoupper($country_iso)) {
        return false;
      }
    }
  }

  /**
   * Checks the availability of a web service by attempting to create a SOAP client.
   *
   * @return mixed Returns the SOAP client if the web service is available, or true if it is unavailable.
   */
  public static function checkWebService()
  {
    $client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");

    if (!array($client)) {
//        $error_message = "web service at ec.europa.eu unavailable";
      return true;
    } else {
      return $client;
    }
  }

  /**
   * Checks the validity of a VAT number against a web service.
   *
   * @param string|null $country_iso The ISO country code. If null, it will be determined based on the VAT number.
   * @param string $tva_intracom The VAT number to validate.
   * @return bool Returns true if the VAT check fails or the web service is unavailable, false if the VAT check succeeds.
   */
  public static function serviceCheckVat(?string $country_iso, string $tva_intracom): bool
  {
    if (ACCOUNT_TVA_INTRACOM_PRO_VERIFICATION == 'false') {
      return false;
    }

    $error = false;

    if (!empty($country_iso)) {
      $result = static::checkIsoCountry($country_iso);
    } else {
      $country_iso = substr($tva_intracom, 0, 2);
      $country_iso = substr(str_replace(' ', '', $country_iso), 2);

      $result = static::checkIsoCountry($country_iso);
    }

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
      } catch (SoapFault $e) {
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
   * Processes the response to extract company information and formats it as a JSON-like string.
   *
   * @param array $response The response data containing company details.
   * @return string A formatted string representing the company's information in a JSON-like structure.
   */
  public static function getInfoCompany($response): string
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