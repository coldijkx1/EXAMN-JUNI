<?php
// gebruik sessies
session_start();
// als de user niet is ingelogd stuur naar deze pagina:
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../../index.html');
	exit;
}

// Include config file
require_once 'config.php';
 
// Define variables and initialize with empty values
$username = $password = $email = $telefoon = $datum = $geslacht = "";
$username_err = $password_err = $email_err = $telefoon_err = $datum_err = $geslacht_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate username
    $input_username = trim($_POST["username"]);
    if(empty($input_username)){
        $username_err = "Please enter a username.";
    } elseif(!filter_var(trim($_POST["username"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $username_err = 'Please enter a valid username.';
    } else{
        $username = $input_username;
    }

    // Validate password
    $input_password = trim($_POST["password"]);
    if(empty($input_password)){
        $password_err = "Please enter a password.";
    } elseif(!filter_var(trim($_POST["password"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $password_err = 'Please enter a valid password.';
    } else{
        $password = $input_password;
    }

     // Validate email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Please enter a email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $email_err = 'Please enter a valid email.';
    } else{
        $email = $input_email;
    }
    
    // Validate telefoon
    $input_telefoon = trim($_POST["telefoon"]);
    if(empty($input_telefoon)){
        $telefoon_err = "Please enter the telefoon amount.";     
    } elseif(!ctype_digit($input_telefoon)){
        $telefoon_err = 'Please enter a positive integer value.';
    } else{
        $telefoon = $input_telefoon;
    }
    
    // Validate datum
    $input_datum = trim($_POST["datum"]);
    if(empty($input_datum)){
        $datum_err = "Please enter the datum amount.";     
    } elseif(!ctype_digit($input_datum)){
        $datum_err = 'Please enter a positive integer value.';
    } else{
        $datum = $input_datum;
    }

    // Validate geslacht
    $input_geslacht = trim($_POST["geslacht"]);
    if(empty($input_geslacht)){
        $geslacht_err = "Please enter a geslacht.";
    } elseif(!filter_var(trim($_POST["geslacht"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $geslacht_err = 'Please enter a valid geslacht.';
    } else{
        $geslacht = $input_geslacht;
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($email_err)  && empty($telefoon_err) && empty($datum_err) && empty($geslacht_err)){
        // Prepare an insert statement
        $sql = "UPDATE account SET username=?, password=?, email=?, telefoon=?, datum=?, geslacht=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_username, $param_password, $param_email, $param_telefoon, $param_datum, $param_geslacht, $param_id);
            
            // Set parameters
            $param_username = $username;
            $param_password = $password;
            $param_email = $email;
            $param_telefoon = $telefoon;
            $param_datum = $datum;
            $param_geslacht = $geslacht;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM account WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $username = $row["username"];
                    $password = $row["password"];
                    $email = $row["email"];
                    $telefoon = $row["telefoon"];
                    $datum = $row["datum"];
                    $geslacht = $row["geslacht"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link href="../../style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                            <label>username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                            <span class="help-block"><?php echo $username_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                            <label>password</label>
                            <input type="text" name="password" class="form-control" value="<?php echo $password; ?>">
                            <span class="help-block"><?php echo $password_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label>email</label>
                            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($telefoon_err)) ? 'has-error' : ''; ?>">
                            <label>telefoon</label>
                            <input type="text" name="telefoon" class="form-control" value="<?php echo $telefoon; ?>">
                            <span class="help-block"><?php echo $telefoon_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($datum_err)) ? 'has-error' : ''; ?>">
                            <label>datum</label>
                            <input type="text" name="datum" class="form-control" value="<?php echo $datum; ?>">
                            <span class="help-block"><?php echo $datum_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($geslacht_err)) ? 'has-error' : ''; ?>">
                            <label>geslacht</label>
                            <input type="text" name="geslacht" class="form-control" value="<?php echo $geslacht; ?>">
                            <span class="help-block"><?php echo $geslacht_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>