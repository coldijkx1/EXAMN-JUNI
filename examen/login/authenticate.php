<<?php
session_start();
// Include config file
require_once 'config.php';

/// Time to check if the login form data was submitted. The isset() function checks if the form data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
    // Could not fetch  any data from form subbmission
    exit('Please make sure you filled both the username and password form fields!');
    }
// We need to Prepare our SQL. This SQL preparation helps prevent SQL injections
if ($stmt = $con->prepare('SELECT id, password FROM account WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc). Since a string is the username in our case, we use "s"
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Store or preserve the results. It helps counter-check if the  user account exists within our database.
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // At this point, the account exists. The only thing left is to verify the password.
        // The use of password_hash in the registration file is encouraged for the storage of hashed passwords.
        if ($_POST['password'] === $password) {
        // Verification was a success! Use log in took place!
        // Sessions creation takes place because a user is logged in. Sessions functionality resemble cookies because they can remember the server's data.
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $_POST['username'];
        $_SESSION['id'] = $id;
        header('Location: home.php');
        } else {
        // Incorrect password
        echo 'Incorrect username and/or password!';
        }
        } else {
        // Incorrect username
        echo 'Incorrect username and/or password!';
        }
    $stmt->close();
    }

?>