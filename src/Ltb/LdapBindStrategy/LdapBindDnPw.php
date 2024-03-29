<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy;

/**
 * 
 */
class LdapBindDnPw extends LdapBindStrategy {
    protected LdapBindCredentials\LdapBindDnPw $ldap_bind_credentials;
    
    function bind($ldap) {
        $credentials = $this->ldap_bind_credentials::get_credentials();
        ldap_bind($ldap, $credentials[0], $credentials[1]);
    }
    
    static function ldap_bind_dn_pw_builder($ldap_binddn, $ldap_bindpw): LdapBindDnPw {
        $credentials = new LdapBindCredentials\LdapBindCredentialsDnPw($ldap_binddn, $ldap_bindpw);
        return new self($credentials);
    }
}
