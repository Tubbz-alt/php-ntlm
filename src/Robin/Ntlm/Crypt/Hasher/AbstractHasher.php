<?php
/**
 * Robin NTLM
 *
 * @copyright 2019 Robin Powered, Inc.
 * @link https://robinpowered.com/
 */

declare(strict_types=1);

namespace Robin\Ntlm\Crypt\Hasher;

use HashContext;
use InvalidArgumentException;

/**
 * A cryptographic hasher implemented using PHP's built-in hashing mechanisms as
 * part of the "hash" extension (`ext-hash`).
 *
 * @link http://php.net/manual/en/ref.hash.php
 */
abstract class AbstractHasher implements HasherInterface
{

    /**
     * Constants
     */

    /**
     * The "resource" type of a PHP hash context.
     *
     * @type string
     */

    const HASH_CONTEXT_RESOURCE_TYPE = 'Hash Context';


    /**
     * Properties
     */

    /**
     * The incremental hashing context.
     *
     * @link http://php.net/manual/en/hash.resources.php
     * @type resource|HashContext
     */
    private $context;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param resource|HashContext $context The incremental hashing context.
     */
    protected function __construct($context)
    {
        $this->context = $this->validateHashContext($context);
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $data): HasherInterface
    {
        hash_update($this->context, $data);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function digest(): string
    {
        // Copy the context so we can keep using the hasher
        $context_copy = hash_copy($this->context);

        // Calculate the digest
        $digest = hash_final($this->context, true);

        // Set our context to the copied one, since the old one is now finalized
        $this->context = $context_copy;

        return $digest;
    }

    /**
     * Validates a given incremental hashing context.
     *
     * @link http://php.net/manual/en/hash.resources.php
     * @param mixed $context The context to validate.
     * @return resource|HashContext The incremental hashing context.
     * @throws InvalidArgumentException If the hash context isn't valid.
     */
    protected function validateHashContext($context)
    {
        if (!($context instanceof HashContext) && (false === $context
            || !is_resource($context)
            || (is_resource($context) && static::HASH_CONTEXT_RESOURCE_TYPE !== get_resource_type($context)))) {
            throw new InvalidArgumentException(
                'Unable to initialize hashing context. Your system might not support the supplied algorithm.'
            );
        }

        return $context;
    }
}
