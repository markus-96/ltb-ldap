<?php
declare(strict_types=1);

namespace Ltb;

/**
 * 
 */
class LdapContext {
    protected $ldap_url;
    protected $ldap_starttls;
    protected $ldap_network_timeout;
    
    protected $ldap_base;
    protected $ldap_user_base;
    protected $ldap_group_base;
    protected $ldap_user_filter;
    protected $ldap_group_filter;
            
    function __construct(
            $ldap_url, 
            $ldap_starttls, 
            $ldap_network_timeout,
            $ldap_base,
            $ldap_user_base,
            $ldap_group_base,
            $ldap_user_filter,
            $ldap_group_filter
    ) {
        $this->ldap_url = $ldap_url;
        $this->ldap_starttls = $ldap_starttls;
        $this->ldap_network_timeout = $ldap_network_timeout;
        
        $this->ldap_base = $ldap_base;
        $this->ldap_user_base = $ldap_user_base;
        $this->ldap_group_base = $ldap_group_base;
        $this->ldap_user_filter = $ldap_user_filter;
        $this->ldap_group_filter = $ldap_group_filter;
    }
}
