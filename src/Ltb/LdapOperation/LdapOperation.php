<?php
declare(strict_types=1);

namespace Ltb\LdapOperation;

/**
 * 
 */
abstract class LdapOperation {
    abstract function perform_ldap_operation();
}
