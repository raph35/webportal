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
		<img src="/views/Inscription/logoUniv.jpeg" class="univLogo">
		<form action="/Inscription/inscrire" method="post">
			<legend>
				<h1>Inscrivez-vous</h1>
			</legend>
			<div >
				<label for="pseudo">
					<p>Identifiant:</p> 
				</label>
				<input type="text" placeholder="Entrez votre identifiant" name="pseudo">
			</div>
			<div >
				<label for="mdp">
					<p>Mot de passe:</p>
				</label>
			    <input type="password" placeholder="Entrez votre mot de passe" name="mdp" required="required">
			</div>
			<button type="submit" name="inscrire">S'inscrire</button>
		</form>
	</div>
</body>
</html>