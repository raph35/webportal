<?php
/*
die(); 

        // Préparation des données
     $ldap_dn = "cn=admin,dc=authentification,dc=com";
        $ldap_password = "123456";
        $ldap_con = ldap_connect("localhost");

        ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
        if(ldap_bind($ldap_con, $ldap_dn, $ldap_password))
        {
    // Authentification
   
        // Préparation des données
        $dn = "cn=etudiant1,cn=UNUM-group,ou=autentification_G,cn=admin,dc=authentification,dc=com";
        $value = "123456";
        $value=trim(base64_encode(pack("H*",md5("123456"))));
        $attr = "userPassword";
        echo $value;
        $value="{MD5}".$value;
        // Comparaison des valeurs
        $r=ldap_compare($ldap_con, $dn, $attr, $value);

        if ($r === -1) {
            echo "Error: " . ldap_error($ds);
        } elseif ($r === true) {
            echo "Password correct.";
        } elseif ($r === false) {
            echo "Mal choisi ! Mot de passe incorrect !";
        }

    } else {
        echo "Impossible de se connecter au serveur LDAP.";
    }

    ldap_close($ldap_con);


     echo strftime('%H:%M:%S');
     function microtime_float() 
     {
       list($usec, $sec) = explode(" ", microtime());
       return ((float)$usec + (float)$sec);
     }
     
     $time_start = microtime_float();
     
     // Attends pendant un moment
     usleep(100);
     
     $time_end = microtime_float();
     $time = $time_end - $time_start;
     
     echo  $time." secondes\n";
     */
     function getMac(){
        $mac = shell_exec('/usr/sbin/arp -na ' .$_SERVER['REMOTE_ADDR']);
        preg_match('/..:..:..:..:..:../', $mac, $matches);
        @$mac = $matches[0];
        return $mac;
    }
    echo getMac();
?>	
