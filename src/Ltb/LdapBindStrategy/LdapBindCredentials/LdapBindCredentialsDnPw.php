<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy\LdapBindCredentials;

/**
 * 
 */
class LdapBindCredentialsDnPw extends LdapBindCredentials {
    private $ldap_binddn;
    private $ldap_bindpw;
    public function __construct(string $ldap_binddn, string $ldap_bindpw) {
        $this->ldap_binddn = $ldap_binddn;
        $this->ldap_bindpw = $ldap_bindpw;
    }
    
    public function get_credentials() {
        return array($this->ldap_binddn, $this->ldap_bindpw);
    }

}
