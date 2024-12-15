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

/**
 * Encrypts a plaintext string using the specified algorithm.
 * Supported algorithms are 'default', 'bcrypt', 'argon2id', 'phpass', and 'salt'.
 * If no algorithm is specified, 'PASSWORD_DEFAULT' is used by default.
 * Triggers a warning if an unknown algorithm is specified and returns false.
 *
 * @param string $plain The plaintext string to encrypt.
 * @param null|string $algo The encryption algorithm to use.
 * @return bool|string The encrypted string or false on failure.
 * @throws Exception If the 'phpass' class cannot load, or in case of failure during random number generation for 'salt'.
 */
class Hash
{
  private static $key = '1c5f37542a2056c76dc2cfe98fecb514'; // 32 caractères pour AES-256
  private static $cipher = 'aes-256-cbc'; // Algorithme de chiffremen

  /**
   * Encrypts a plain text string using the specified algorithm.
   *
   * Supported algorithms:
   * - 'default' or null: Uses the default PHP password hashing algorithm.
   * - 'bcrypt': Uses the Bcrypt hashing algorithm.
   * - 'argon2id': Uses the Argon2id hashing algorithm.
   * - 'phpass': Uses the Phpass library for password hashing.
   * - 'salt': Applies a custom MD5-based hashing with a salt.
   *
   * @param string $plain The plain text string to be encrypted
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
   * Verifies if the given plain text matches the provided hashed value using various hashing mechanisms.
   *
   * @param string $plain The plain text string to verify.
   * @param string $hash The hashed value to compare against.
   * @return bool Returns true if the plain text matches the hashed value, otherwise false.
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
   * Determines if a hashed password needs to be rehashed with a different algorithm or cost.
   *
   * @param string $hash The hashed password to verify.
   * @param string|null $algo The algorithm to use for verifying the hash. Can be 'default', 'bcrypt', 'argon2id', or null for the default algorithm. Defaults to null.
   * @return bool Returns true if the hash needs to be rehashed, false otherwise.
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
   * Determines the type of a given hash based on its characteristics.
   *
   * @param string $hash The hash string to analyze and determine its type.
   * @return string|null Returns the hash type as a string if recognized, or null if not found.
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
   * Generates a random integer within the specified range.
   * If a secure random number cannot be generated, a less secure fallback is used if allowed.
   *
   * @param int|null $min The minimum value of the range. Defaults to 0 if not specified.
   * @param int|null $max The maximum value of the range. Defaults to PHP_INT_MAX if not specified.
   * @param bool $secure Whether to strictly use cryptographically secure random numbers. Defaults to true.
   * @return int A random integer
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
   * Generates a random string based on the specified type and length.
   *
   * @param int $length The length of the random string to be generated.
   * @param string $type The type of characters to include in the random string.
   *                      Accepted values are:
   *                      - 'mixed': Includes both characters and digits.
   *                      - 'chars': Includes only alphabetical characters.
   *                      - 'digits': Includes only numerical digits.
   *                      Defaults to 'mixed'.
   * @return string The generated random string of the specified length and type.
   * @throws InvalidArgumentException If
   */
  public static function getRandomString(int $length, string $type = 'mixed'): string
  {
    if (!in_array($type, [
      'mixed',
      'chars',
      'digits'
    ])) {
      throw new InvalidArgumentException('Hash::getRandomString() $type not recognized: ' . $type);

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
   * Generates a string of random bytes with a specified length.
   * If the `secure` parameter is true and a cryptographically secure source of randomness
   * is unavailable, an exception will be thrown.
   *
   * @param int $length The number of random bytes to generate.
   * @param bool $secure Whether to enforce the use of a cryptographically secure method. Defaults to true.
   * @return string A string containing the generated random bytes.
   * @throws Exception If a secure random byte generation method is required but unavailable.
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
   * Encrypts the provided data using a specified cipher and key.
   *
   * @param string $data The plaintext data to be encrypted.
   * @return string The encrypted data in Base64-encoded format, including the encryption initialization vector.
   * @throws Exception If the encryption process fails.
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
   * Decrypts the provided encrypted data string.
   *
   * @param string $encryptedData The encrypted data in base64 format, containing the encrypted text and IV concatenated with '::'.
   * @return string Returns the decrypted data as a plain text string.
   * @throws Exception If the data format is invalid or decryption fails.
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
   * Checks if the given data is an encrypted text.
   *
   * @param string $data The input string to verify if it represents encrypted text.
   * @return bool Returns true if the data appears to be encrypted (valid base64 encoding
   *              containing a separator "::" for the encrypted text and IV), false otherwise.
   */
  private static function isEncryptedDatatext(string $data): bool
  {
    // Vérifie si la donnée est encodée en base64
    if (base64_decode($data, true) === false) {
      return false;
    }

    return count(explode('::', base64_decode($data), 2)) === 2;
  }

  /**
   * Displays the decrypted data text if the input is encrypted,
   * or returns the original string if it is not encrypted.
   *
   * @param string|null $dataString The input string to be handled, which may be encrypted or null.
   * @return string The decrypted string if the input was encrypted, otherwise the original string or an empty string if null was provided.
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
