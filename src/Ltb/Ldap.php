<?php namespace Ltb;
use Ltb\LdapBindStrategy\{
    LdapBindAnonym, LdapBindDnPw, LdapBindKrb5, LdapBindStrategy
};


final class Ldap {
    
    protected LdapContext $ldap_context;
    
    protected LDAP\Connection $ldap;
    
    private LdapBindStrategy $manager_bind_strategy;
    
    private bool $bind_status = false;
    
    function set_manager_bind_strategy(LdapBindStrategy $manager_bind_strategy) {
        $this->manager_bind_strategy = $manager_bind_strategy;
    }
    
    function __construct(LdapContext $ldap_context) {
        $this->ldap_context = $ldap_context;
        $this->ldap = \ldap_connect($ldap_context->ldap_url);
        \ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        \ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
        if ( isset($ldap_context->ldap_network_timeout) ) {
            ldap_set_option($this->ldap, LDAP_OPT_NETWORK_TIMEOUT, $ldap_context->ldap_network_timeout);
        }
        
        if ( $ldap_context->ldap_starttls && !ldap_start_tls($this->ldap) ) {
            throw new Exception("LDAP - Unable to use StartTLS");
        }
    }
    
    function get_bind_status() {
        return $this->bind_status;
    }


    function bind(LdapBindStrategy $strategy) {
        $this->bind_status = $strategy::bind($this->ldap);
        
        if ( !$this->bind_status ) {
            $errno = ldap_errno($this->ldap);
            if ( $errno ) {
                throw new Exception("LDAP - Bind error $errno  (".ldap_error($this->ldap).")");
            } else {
                throw new Exception("LDAP - Bind error");
            }
        }
    }
    
    function try_user_bind(LdapBindStrategy $user_bind_strategy): bool {
        $bind = $this->change_bind($user_bind_strategy);
        $this->rebind_as_manager();
        return $bind;
    }
    
    function change_bind(LdapBindStrategy $user_bind_strategy): bool {
        $this->bind_status = $user_bind_strategy::bind($this->ldap);
        $bind = $this->bind_status;
        return $bind;
    }
    
    function check_ad_mode_manager_must_change(LdapBindStrategy $user_bind_strategy): bool {
        return $user_bind_strategy->check_ad_mode_manager_must_change($this->ldap);
    }
    
    function rebind_as_manager() {
        if ( (!isset($this->manager_bind_strategy)) || ($this->manager_bind_strategy == null) ) {
            throw new Exception("LDAP - Manager Bind Strategy is not set!");
        }
        return $this->change_bind($this->manager_bind_strategy);
    }
    
    function search_results($ldap_base, $ldap_filter) {
        $search = ldap_search($this->ldap, $ldap_base, $ldap_filter);
        $errno = ldap_errno($this->ldap);
        if ( $errno ) {
            throw new Exception("LDAP - Search error $errno  (".ldap_error($this->ldap).")");
        }
        $entry = ldap_first_entry($this->ldap, $search);
        if (!$entry) {
            $ocValues = ldap_get_values($this->ldap, $entry, 'objectClass');
            $userdn = ldap_get_dn($this->ldap, $entry);
            $entry_array = ldap_get_attributes($this->ldap, $entry);
            $entry_array['dn'] = $userdn;
            LdapSearchResult($ocValues, $userdn, $entry_array);
        }
        return new LdapSearchResult($ocValues, $userdn, $entry_array);
    }
    
    static function ldap_builder($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw, $ldap_network_timeout, $ldap_krb5ccname): Ldap {
        $manager_bind_strategy = null;
        if ( isset($ldap_binddn) && isset($ldap_bindpw) ) {
            $manager_bind_strategy = LdapBindDnPw::ldap_bind_dn_pw_builder($ldap_binddn, $ldap_bindpw);
        } elseif ( isset($ldap_krb5ccname) ) {
            $manager_bind_strategy = LdapBindKrb5::ldap_bind_krb5_builder($ldap_krb5ccname);
        } else {
            $manager_bind_strategy = LdapBindAnonym::ldap_bind_anonym_builder();
        }
        $context = new LdapContext($ldap_url, $ldap_starttls, $ldap_network_timeout);
        try {
            $ldap = new self($context);
            $ldap->set_manager_bind_strategy($manager_bind_strategy);
            $ldap->rebind_as_manager();
            return $ldap;
        } catch (Exception $ex) {
            error_log($ex);
            return null;
        }
    }

    # LDAP Functions 

    function get_list($ldap_base, $ldap_filter, $key, $value) {
        $return = array();

        if ($this->get_bind_status()) {

            # Search entry
            $search = ldap_search($this->ldap, $ldap_base, $ldap_filter, array($key, $value) );

            $errno = ldap_errno($this->ldap);

            if ( $errno ) {
                error_log("LDAP - Search error $errno  (".ldap_error($this->ldap).")");
            } else {
                $entries = ldap_get_entries($this->ldap, $search);
                for ($i=0; $i<$entries["count"]; $i++) {
                    if(isset($entries[$i][$key][0])) {
                        $return[$entries[$i][$key][0]] = isset($entries[$i][$value][0]) ? $entries[$i][$value][0] : $entries[$i][$key][0];
                    }
                }
            }
        }

        return $return;
    }

    # if key is not found in attributes, order of entries is preserved
    static function ldapSort(array &$entries, $key)
    {
        # 'count' is an additionnal attribute of ldap entries that will be preserved
        # remove it since lost by usort ( changed to integer index )
        $count=$entries['count'];
        unset($entries['count']);

        $sort_key=$key;

        usort($entries,
              fn($a, $b) =>
              ( is_array($a) and is_array($b) ) ?
                ( array_key_exists($sort_key,$a) ?
                    ( array_key_exists($sort_key,$b) ? $a[$sort_key][0] <=> $b[$sort_key][0] : 1 )
                  : ( array_key_exists($sort_key,$b) ? -1 : 0 ))
              : 0
        );


        # preserve count since sorting should not change number of elements.
        $entries['count']=$count;

        return true;

    }
    
    # not yet fully tested, please use ldapSort directly
    #
    # ldap_search + ldap_sort combined done at server side if possible
    # if not supported fallback on client sorting.
    function sorted_search($ldap_base, $ldap_filter, $attributes, $sortby, $ldap_size_limit) {

        if (isset($sortby) and $sortby)
        {
            $check_attribute='supportedControl';
            $check = ldap_read($this->ldap, '', '(objectClass=*)', [$check_attribute]);
            $entries=ldap_get_entries($this->ldap, $check);
            if (in_array(LDAP_CONTROL_SORTREQUEST, $entries[0]['supportedcontrol'],true)) {
                # server side sort
                $controls=[['oid' => LDAP_CONTROL_SORTREQUEST, 'value' => [['attr'=>$sortby]]]];
                # if $sortby is not in $attributes ? what to do ?
                $ldap_result = ldap_search($this->ldap, $ldap_base, $ldap_filter, $attributes, 0, $ldap_size_limit, -1, LDAP_DEREF_NEVER, $controls );
                $errno = ldap_errno($this->ldap);
                if ( $errno === 0 )
                {
                    $entries=ldap_get_entries($this->ldap, $ldap_result);
                }
            }
        }

        if (!isset($errno))
        {
            $ldap_result = ldap_search($this->ldap, $ldap_base, $ldap_filter, $attributes, 0, $ldap_size_limit);
            $errno = ldap_errno($this->ldap);
            if ( $errno === 0 )
            {
                $entries=ldap_get_entries($this->ldap, $ldap_result);
                Ldap::ldapSort($entries,$sortby);
            }
            else {
                var_dump($errno);
            }
        }

        return array($ldap_result,$errno,$entries);
    }


}
?>
