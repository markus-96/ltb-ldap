<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy\LdapBindCredentials;

/**
 * 
 */
class LdapBindKrb5 extends LdapBindCredentials {
    private $ldap_krb5ccname;
    public function __construct(string $ldap_krb5ccname) {
        $this->ldap_krb5ccname = $ldap_krb5ccname;
    }

    public function get_credentials() {
        return $this->ldap_krb5ccname;
    }
    
    

}
