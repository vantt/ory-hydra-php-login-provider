<?php

namespace App\Security;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class Drupal7Encoder implements PasswordEncoderInterface {

    public function encodePassword($raw, $salt) {
        return hash('sha512', $salt . $raw); // Custom function for password encrypt
    }

    public function isPasswordValid($encoded, $raw, $salt) {
        if (strpos($encoded, 'U$') === 0) {
            // This may be an updated password from user_update_7000(). Such hashes
            // have 'U' added as the first character and need an extra md5().
            $stored_hash = substr($encoded, 1);
            $raw         = md5($raw);
        }
        else {
            $stored_hash = $encoded;
        }

        $type = substr($stored_hash, 0, 3);
        switch ($type) {
            case '$S$':
                // A normal Drupal 7 password using sha512.
                $hash = _password_crypt('sha512', $raw, $stored_hash);
                break;
            case '$H$':
                // phpBB3 uses "$H$" for the same thing as "$P$".
            case '$P$':
                // A phpass password generated using md5.  This is an
                // imported password or from an earlier Drupal version.
                $hash = _password_crypt('md5', $raw, $stored_hash);
                break;
            default:
                return false;
        }

        return ($hash && $stored_hash == $hash);
    }

    public function needsRehash(string $encoded): bool {
        // TODO: Implement needsRehash() method.
    }

}




    /**
     * The standard log2 number of iterations for password stretching. This should
     * increase by 1 every Drupal version in order to counteract increases in the
     * speed and power of computers available to crack the hashes.
     */
    const DRUPAL_HASH_COUNT=15;

    /**
     * The minimum allowed log2 number of iterations for password stretching.
     */
    const DRUPAL_MIN_HASH_COUNT=7;

    /**
     * The maximum allowed log2 number of iterations for password stretching.
     */
    const DRUPAL_MAX_HASH_COUNT=30;

    /**
     * The expected (and maximum) number of characters in a hashed password.
     */
    const DRUPAL_HASH_LENGTH=55;


    /**
     * Returns a string for mapping an int to the corresponding base 64 character.
     */
    function _password_itoa64() {
        return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    /**
     * Encodes bytes into printable base 64 using the *nix standard from crypt().
     *
     * @param $input
     *   The string containing bytes to encode.
     * @param $count
     *   The number of characters (bytes) to encode.
     *
     * @return string
     *   Encoded string
     */
    function _password_base64_encode($input, $count) {
        $output = '';
        $i      = 0;
        $itoa64 = _password_itoa64();
        do {
            $value  = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }

    /**
     * Hash a password using a secure stretched hash.
     *
     * By using a salt and repeated hashing the password is "stretched". Its
     * security is increased because it becomes much more computationally costly
     * for an attacker to try to break the hash by brute-force computation of the
     * hashes of a large number of plain-text words or strings to find a match.
     *
     * @param $algo
     *   The string name of a hashing algorithm usable by hash(), like 'sha256'.
     * @param $password
     *   Plain-text password up to 512 bytes (128 to 512 UTF-8 characters) to hash.
     * @param $setting
     *   An existing hash or the output of _password_generate_salt().  Must be
     *   at least 12 characters (the settings and salt).
     *
     * @return string
     *   A string containing the hashed password (and salt) or FALSE on failure.
     *   The return string will be truncated at DRUPAL_HASH_LENGTH characters max.
     */
    function _password_crypt($algo, $password, $setting) {
        // Prevent DoS attacks by refusing to hash large passwords.
        if (strlen($password) > 512) {
            return false;
        }
        // The first 12 characters of an existing hash are its setting string.
        $setting = substr($setting, 0, 12);

        if ($setting[0] != '$' || $setting[2] != '$') {
            return false;
        }
        $count_log2 = _password_get_count_log2($setting);
        // Hashes may be imported from elsewhere, so we allow != DRUPAL_HASH_COUNT
        if ($count_log2 < DRUPAL_MIN_HASH_COUNT || $count_log2 > DRUPAL_MAX_HASH_COUNT) {
            return false;
        }
        $salt = substr($setting, 4, 8);
        // Hashes must have an 8 character salt.
        if (strlen($salt) != 8) {
            return false;
        }

        // Convert the base 2 logarithm into an integer.
        $count = 1 << $count_log2;

        // We rely on the hash() function being available in PHP 5.2+.
        $hash = hash($algo, $salt . $password, true);
        do {
            $hash = hash($algo, $hash . $password, true);
        } while (--$count);

        $len    = strlen($hash);
        $output = $setting . _password_base64_encode($hash, $len);
        // _password_base64_encode() of a 16 byte MD5 will always be 22 characters.
        // _password_base64_encode() of a 64 byte sha512 will always be 86 characters.
        $expected = 12 + ceil((8 * $len) / 6);

        return (strlen($output) == $expected) ? substr($output, 0, DRUPAL_HASH_LENGTH) : false;
    }

    /**
     * Parse the log2 iteration count from a stored hash or setting string.
     *
     * @param $setting
     *
     * @return bool|int
     */
    function _password_get_count_log2($setting) {
        $itoa64 = _password_itoa64();

        return strpos($itoa64, $setting[3]);
    }

    /**
     * Hash a password using a secure hash.
     *
     * @param $password
     *   A plain-text password.
     * @param $count_log2
     *   Optional integer to specify the iteration count. Generally used only during
     *   mass operations where a value less than the default is needed for speed.
     *
     * @return string
     *   A string containing the hashed password (and a salt), or FALSE on failure.
     */
    function user_hash_password($password, $count_log2 = 0) {
        if (empty($count_log2)) {
            // Use the standard iteration count.
            $count_log2 = variable_get('password_count_log2', DRUPAL_HASH_COUNT);
        }

        return _password_crypt('sha512', $password, _password_generate_salt($count_log2));
    }

