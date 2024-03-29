<?php
declare(strict_types=1);

namespace Ltb\LdapBindStrategy;

/**
 * 
 */
abstract class LdapBindStrategy {
    protected LdapBindCredentials\LdapBindCredentials $ldap_bind_credentials;
    
    function __construct(LdapBindCredentials $ldap_bind_credentials) {
        $this->ldap_bind_credentials = $ldap_bind_credentials;
    }

    abstract function bind(LDAP\Connection $ldap);
    
    function check_ad_mode_manager_must_change(LDAP\Connection $ldap) {
        if ( ldap_get_option($ldap, 0x0032, $extended_error) ) {
            error_log("LDAP - Bind user extended_error $extended_error  (".ldap_error($ldap).")");
            $extended_error = explode(', ', $extended_error);
            if ( strpos($extended_error[2], '773') or strpos($extended_error[0], 'NT_STATUS_PASSWORD_MUST_CHANGE') ) {
                error_log("LDAP - Bind user password needs to be changed");
                $who_change_password = "manager";
                $result = "";
            }
            if ( ( strpos($extended_error[2], '532') or strpos($extended_error[0], 'NT_STATUS_ACCOUNT_EXPIRED') ) and $ad_options['change_expired_password'] ) {
                error_log("LDAP - Bind user password is expired");
                $who_change_password = "manager";
                $result = "";
            }
            unset($extended_error);
        }
    }
}
