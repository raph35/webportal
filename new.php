<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Authentification</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/macss.css">

</head>

<body>


    <div class="wrap-login100">
        <form class="login100-form" method="post">
            <span class="login100-form-title p-b-43">
						Authentifiez vous!!!
					</span>


            <div class="wrap-input100 validate-input">
                <input class="input100" type="text" name="pseudo" placeholder="Pseudo">
            </div>


            <div class="wrap-input100 validate-input" data-validate="Password is required">
                <input class="input100" type="password" name="pass" placeholder="Mot de pass">
            </div>

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" type="submit">
							S'authentifier
						</button>
            </div>

        </form>

        <div class="login100-more" style="background-image: url('images/bg-01.jpg');">
        </div>
    </div>

</body>

</html>

<?php
$url=$_GET['url'];
$ipPortal='192.168.10.245';
if(!empty($_POST['pseudo']) && !empty($_POST['pass']))
{ 
        $pseudo=$_POST['pseudo'];
        $pass=$_POST['pass'];

        $value=trim(base64_encode(pack("H*",md5($pass))));
        $pass="{MD5}".$value;
        $ldap_dn = "cn=admin,dc=authentification,dc=com";
        $ldap_password = "123456";
        $ldap_con = ldap_connect("localhost");

        ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
        if(ldap_bind($ldap_con, $ldap_dn, $ldap_password))
        {

            //echo "Bind successful!";
            $filter = "(uid=$pseudo)";
            $result = ldap_search($ldap_con, "dc=authentification,dc=com", $filter) or exit("Unable to search");
            $entries = ldap_get_entries($ldap_con, $result);
            


           
            if (!empty($entries[0]['givenname'][0]) && $entries[0]['givenname'][0]==$pseudo) {
                if (!empty( $entries[0]['userpassword'][0]) &&  $entries[0]['userpassword'][0]==$pass) {
                    //$mac=takeMac();
                    //saveUser();
                    header("Location: http://$ipPortal/intercept.php?&s=accepted&url=$url");                
                }
                else
                {
                     echo "<script>alert('Mot de passe incorecte')</script>";
                    header("Location: index.php?&url=$url");
                     header();   
                }
            }
            else
            {
                echo "<script>alert('Identifiant incorecte')</script>";
                header("Location: index.php?&url=$url");
            }
        }
        else{
            echo "Invalid user/pass or other errors!";
            header("Location: index.php?&url=$url");
        }
}
?>