<!--
Projet Portail Captif - LDAP
ANDRIANAIVO Minosoa Mickaëla
Page d'inscription de l'étudiant dans l'annuaire LDAP
-->
<!DOCTYPE html>
<html>
<head>
	<title>Inscription</title>
	<meta charset="utf-8">
	<?php echo "<link rel='stylesheet' type='text/css' href='".WEBROOT."views/Inscription/inscriptionStyle.css'>";?>
</head>
<body>
	<div class="loginField">
		<img src=" <?php echo WEBROOT ?>views/Inscription/logoUniv.jpeg" class="univLogo">
		<form action="<?php echo WEBROOT; ?>Inscription/inscrire" method="post">
			<legend>
				<h1>Inscrivez-vous</h1>
			</legend>
			<?php if(isset($succes)):?>
				<div class="alert succes"><?=$succes?> </div>
			<?php endif;?>

			<?php if(isset($error)):?>
				<div class="alert error"><?=$error??"";?></div>
			<?php endif;?>
			<div >
				<label for="pseudo">
					<p>Identifiant:</p> 
				</label>
				<input type="text" placeholder="Entrez votre identifiant" name="pseudo" required="required">
			</div>
			<div >
				<label for="mdp">
					<p>Mot de passe:</p>
				</label>
			    <input type="password" placeholder="Entrez votre mot de passe" name="mdp" required="required" id="mdp" onchange="enableButton()">
			</div>	
			<p class="mdpError" id="error"></p>
			<div >
			    <input type="password" placeholder="Confirmez votre mot de passe" name="mdpConfirm" required="required" id="mdpConfirm" onchange="enableButton()">
			</div>
			<button type="submit" name="inscrire" id="myBtn" disabled>S'inscrire</button>
		</form>
	</div>
</body>
</html>

<script>
function enableButton(){
	var mdp = document.getElementById("mdp").value;
	var mdpConfirm = document.getElementById("mdpConfirm").value;
	
	var mdpError = document.getElementById("error");
	var myBtn = document.getElementById("myBtn");
	
	if(mdp == mdpConfirm){
		document.getElementById("error").innerHTML = "";
		myBtn.disabled = false;
	}
	else if(mdp!="" && mdpConfirm==""){
		document.getElementById("error").innerHTML = "";
		myBtn.disabled = true;
	}
	else if(mdp != mdpConfirm && mdp!=""){
		document.getElementById("error").innerHTML = "Mot de passe incorrect"; 
		myBtn.disabled = true;
	}
}
</script>