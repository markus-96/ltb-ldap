<?php
declare(strict_types=1);

namespace Ltb\LdapOperation;

/**
 * 
 */
class LdapOperationContext {
    private LdapOperation $operation;
    
    function set_operation(LdapOperation $operation) {
        $this->operation = $operation;
    }
    
    function perform_operation() {
        if ($this->operation != null) {
            $this->operation->perform_ldap_operation();
        }
    }
}
