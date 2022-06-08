<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.html');
	exit;
}
// Include config file
require_once 'config.php';

// krijg resultaat uit database.
$stmt = $con->prepare('SELECT password, email, telefoon, datum, geslacht FROM account WHERE id = ?');
// zoek naar account id om informatie te krijgen.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $telefoon, $datum, $geslacht);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="../style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="home.php"><i class="fas fa-bed"></i>Home</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Naam:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Wachtwoord:</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>TelefoonNummer:</td>
						<td><?=$telefoon?></td>
					</tr>
					<tr>
						<td>GeboorteDatum:</td>
						<td><?=$datum?></td>
					</tr>
					<tr>
						<td>Geslacht:</td>
						<td><?=$geslacht?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>