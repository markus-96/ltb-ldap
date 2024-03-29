<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy\LdapBindCredentials;

/**
 * 
 */
abstract class LdapBindCredentials {
    abstract function get_credentials();
}
