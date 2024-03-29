<?php

namespace Ltb;

/**
 * 
 */
class LdapSearchResult {
    public $ocValues;
    public $userdn;
    public $entry_array;
    
    function __construct($ocValues, $userdn, $entry_array) {
        $this->ocValues = $ocValues;
        $this->userdn = $userdn;
        $this->userdn = $entry_array;
    }
    
    function check_samba_shadow_mode($samba_mode, $shadow_update_shadowLastChange, $shadow_update_shadowExpire) {
        if ( !in_array( 'sambaSamAccount', $this->ocValues ) and !in_array( 'sambaSAMAccount', $this->ocValues ) ) {
            $samba_mode = false;
        }
        if ( !in_array( 'shadowAccount', $this->ocValues ) ) {
            $shadow_update_shadowLastChange = false;
            $shadow_update_shadowExpire = false;
        }
    }
}
