<?php
/**
 * Robin NTLM
 *
 * @copyright 2019 Robin Powered, Inc.
 * @link https://robinpowered.com/
 */

namespace Robin\Ntlm\Crypt\Des;

use InvalidArgumentException;
use Robin\Ntlm\Crypt\CipherMode;
use Robin\Ntlm\Crypt\Exception\CryptographicFailureException;

/**
 * @deprecated Mcrypt has been deprecated from the core PHP library. Use
 * {@see OpenSslDesEncrypter} as an alternative.
 * @link https://php.net/manual/en/migration72.other-changes.php#migration72.other-changes.mcrypt
 *
 * An engine used to encrypt data using the DES standard algorithm and
 * implemented using the PHP "mcrypt" extension.
 *
 * @link http://php.net/mcrypt
 */
class McryptDesEncrypter extends AbstractDesEncrypter implements DesEncrypterInterface
{

    /**
     * Properties
     */

    /**
     * A map of {@link CipherMode}s to the "mcrypt" extension equivalents.
     */
    private static $cipher_mode_map = [
        CipherMode::CBC => MCRYPT_MODE_CBC,
        CipherMode::CFB => MCRYPT_MODE_CFB,
        CipherMode::ECB => MCRYPT_MODE_ECB,
        CipherMode::OFB => MCRYPT_MODE_OFB,
    ];


    /**
     * Methods
     */

    /**
     * {@inheritDoc}
     */
    public function encrypt($key, $data, $mode, $initialization_vector)
    {
        if (isset(self::$cipher_mode_map[$mode])) {
            $mode = self::$cipher_mode_map[$mode];
        } else {
            throw new InvalidArgumentException('Unknown cipher mode "'. $mode .'"');
        }

        $key = $this->processKey($key);

        $encrypted = mcrypt_encrypt(MCRYPT_DES, $key, $data, $mode, $initialization_vector);

        if (false === $encrypted) {
            throw CryptographicFailureException::forReasonCode(
                CryptographicFailureException::CODE_FOR_ENCRYPTION_FAILURE
            );
        }

        return $encrypted;
    }
}
