<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy;

/**
 * 
 */
class LdapBindKrb5 extends LdapBindStrategy {
    protected LdapBindCredentials\LdapBindKrb5 $ldap_bind_credentials;

    function bind($ldap) {
        putenv("KRB5CCNAME=". $this->ldap_bind_credentials::get_credentials());
        return ldap_sasl_bind($ldap, NULL, NULL, 'GSSAPI');
    }
    
    static function ldap_bind_krb5_builder($ldap_krb5ccname) {
        $credentials = new LdapBindCredentials\LdapBindKrb5($ldap_krb5ccname);
        return new self($credentials);
    }

}
