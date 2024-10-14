<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use Exception;
use PasswordHash;
use function count;
use function in_array;
use function strlen;

class Hash
{
  private static $key = '1c5f37542a2056c76dc2cfe98fecb514'; // 32 caractères pour AES-256
  private static $cipher = 'aes-256-cbc'; // Algorithme de chiffremen

  /**
   * @param string $plain
   * @param null|string $algo
   * @return bool|string
   * @throws Exception
   */
  public static function encrypt(string $plain, string|null $algo = null)
  {
    if (!isset($algo) || $algo == 'default' || $algo == 'bcrypt' || $algo == 'argon2id') {
      if (!isset($algo) || ($algo == 'default')) {
        $algo = PASSWORD_DEFAULT;
      } elseif ($algo == 'bcrypt') {
        $algo = PASSWORD_BCRYPT;
      } elseif ($algo == 'argon2id') {
        $algo = PASSWORD_ARGON2ID;
      }

      return password_hash($plain, $algo);
    }

    if ($algo == 'phpass') {
      if (!class_exists('PasswordHash', false)) {
        include_once(CLICSHOPPING::BASE_DIR . 'External/PasswordHash.php');
      }

      $hasher = new PasswordHash(10, true);

      return $hasher->HashPassword($plain);
    }

    if ($algo == 'salt') {
      $password = '';

      for ($i = 0; $i < 10; $i++) {
        $password .= static::getRandomInt();
      }

      $salt = substr(md5($password), 0, 2);

      $password = md5($salt . $plain) . ':' . $salt;

      return $password;
    }

    trigger_error('ClicShopping\\OM\\Hash::encrypt() Algorithm "' . $algo . '" unknown.');

    return false;
  }

  /**
   * @param string $plain
   * @param string $hash
   * @return bool
   */
  public static function verify(string $plain, string $hash): bool
  {
    $result = false;

    if ((strlen($plain) > 0) && (strlen($hash) > 0)) {
      switch (static::getType($hash)) {
        case 'phpass':
          if (!class_exists('PasswordHash', false)) {
            include_once(BASE_DIR . 'external/PasswordHash.php');
          }

          $hasher = new PasswordHash(10, true);

          $result = $hasher->checkPassword($plain, $hash);

          break;

        case 'salt':
          // split apart the hash / salt
          $stack = explode(':', $hash, 2);

          if (count($stack) === 2) {
            $result = (md5($stack[1] . $plain) === $stack[0]);
          } else {
            $result = false;
          }

          break;

        default:
          $result = password_verify($plain, $hash);

          break;
      }
    }

    return $result;
  }

  /**
   * @param string $hash
   * @param string|null $algo
   * @return bool
   */
  public static function needsRehash(string $hash, ?string $algo = null)
  {
    if (!isset($algo) || $algo == 'default') {
      $algo = PASSWORD_DEFAULT;
    } elseif ($algo == 'bcrypt') {
      $algo = PASSWORD_BCRYPT;
    } elseif ($algo == 'argon2id') {
      $algo = PASSWORD_ARGON2ID;
    }

    if (!is_int($algo)) {
      trigger_error('ClicShopping\OM\Hash::needsRehash() Algorithm "' . $algo . '" not supported.');
    }

    return password_needs_rehash($hash, $algo);
  }

  /**
   * @param string $hash
   * @return string|null
   */
  public static function getType(string $hash): ?string
  {
    $info = password_get_info($hash);

    if ($info['algo'] > 0) {
      return $info['algoName'];
    }

    if (substr($hash, 0, 3) == '$P$') {
      return 'phpass';
    }

    if (preg_match('/^[A-Z0-9]{32}\:[A-Z0-9]{2}$/i', $hash) === 1) {
      return 'salt';
    }

    trigger_error('ClicShopping\OM\Hash::getType() hash type not found for "' . substr($hash, 0, 5) . '"');

    return '';
  }

  /**
   * @param null $min
   * @param null $max
   * @param bool $secure
   * @return int
   * @throws Exception
   */
  public static function getRandomInt($min = null, $max = null, bool $secure = true)
  {
    if (!isset($min)) {
      $min = 0;
    }

    if (!isset($max)) {
      $max = PHP_INT_MAX;
    }

    try {
      $result = random_int($min, $max);
    } catch (Exception $e) {
      if ($secure === true) {
        throw $e;
      }

      $result = mt_rand($min, $max);
    }

    return $result;
  }

  /**
   * @param int $length
   * @param string $type
   * @return string
   * @throws Exception
   */
  public static function getRandomString(int $length, string $type = 'mixed'): string
  {
    if (!in_array($type, [
      'mixed',
      'chars',
      'digits'
    ])) {
      trigger_error('Hash::getRandomString() $type not recognized: ' . $type, E_USER_ERROR);

      return false;
    }

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';

    $base = '';

    if (($type == 'mixed') || ($type == 'chars')) {
      $base .= $chars;
    }

    if (($type == 'mixed') || ($type == 'digits')) {
      $base .= $digits;
    }

    $rand_value = '';

    do {
      $random = base64_encode(static::getRandomBytes($length));

      for ($i = 0, $n = strlen($random); $i < $n; $i++) {
        $char = substr($random, $i, 1);

        if (str_contains($base, $char)) {
          $rand_value .= $char;
        }
      }
    } while (strlen($rand_value) < $length);

    if (strlen($rand_value) > $length) {
      $rand_value = substr($rand_value, 0, $length);
    }

    return $rand_value;
  }

  /**
   * @param $length
   * @param bool $secure
   * @return bool|string|void
   * @throws Exception
   */
  public static function getRandomBytes(int $length, bool $secure = true)
  {
    try {
      $result = random_bytes($length);
    } catch (Exception $e) {
      if ($secure === true) {
        throw $e;
      }

      $result = '';
      $random_state = 0;

      for ($i = 0; $i < $length; $i += 16) {
        $random_state = md5(microtime() . $random_state);

        $result .= pack('H*', md5($random_state));
      }

      $result = substr($result, 0, $length);
    }

    return $result;
  }

  /**
   * Chiffrement de données - - Uniquement pour les données type text
   * @param string $data
   * @return string
   * @throws Exception
   */
  public static function encryptDatatext(string $data): string
  {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher));

    $encrypted = openssl_encrypt($data, self::$cipher, self::$key, 0, $iv);
    if ($encrypted === false) {
      throw new Exception('Erreur lors du chiffrement des données');
    }

    return base64_encode($encrypted . '::' . $iv);
  }

  /**
   * Déchiffrement de données - Uniquement pour les données type text
   * @param string $encryptedData
   * @return string
   * @throws Exception
   */
  private static function decryptDatatext(string $encryptedData): string
  {
    $decodedData = base64_decode($encryptedData);
    $parts = explode('::', $decodedData, 2);

    if (count($parts) !== 2) {
      throw new Exception('Données de chiffrement invalides');
    }

    [$encrypted, $iv] = $parts;

    $decrypted = openssl_decrypt($encrypted, self::$cipher, self::$key, 0, $iv);
    if ($decrypted === false) {
      throw new Exception('Erreur lors du déchiffrement des données');
    }

    return $decrypted;
  }

  /**
   * Vérifie si les données sont chiffrées (données en base64 avec "::" pour séparer le contenu et l'IV)
   * @param string $data
   * @return bool
   */
  private static function isEncryptedDatatext(string $data): bool
  {
    // Vérifie si la donnée est encodée en base64
    if (base64_decode($data, true) === false) {
      return false;
    }

    // Vérifie si la donnée décodée contient "::", séparateur entre le texte chiffré et l'IV
    return count(explode('::', base64_decode($data), 2)) === 2;
  }

  /**
   * Display the data decripted on the front Office
   * @param string|null $dataString
   * @return string
   * @throws Exception
   */
  public static function displayDecryptedDataText(string|null $dataString): string
  {
    if (!is_null($dataString)) {
      if (self::isEncryptedDatatext($dataString)) {
        $data = self::decryptDatatext($dataString);
      } else {
        $data = $dataString;
      }
    } else {
      $data = '';
    }

    return $data;
  }
}
