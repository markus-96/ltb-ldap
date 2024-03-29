<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy;

use Ltb\LdapBindStrategy\LdapBindCredentials\LdapBindCredentialsAnonym;

/**
 * 
 */
class LdapBindAnonym extends LdapBindStrategy {
    protected LdapBindCredentialsAnonym $ldap_bind_credentials;
    
    public function bind($ldap) {
        return ldap_bind($ldap);
    }
    
    public static function ldap_bind_anonym_builder(): LdapBindAnonym {
        $credentials = new LdapBindCredentialsAnonym();
        return new self($credentials);
    }
}
