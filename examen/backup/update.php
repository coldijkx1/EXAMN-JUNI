<?php
// gebruik sessies
session_start();
// als de user niet is ingelogd stuur naar deze pagina:
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.html');
	exit;
}

// Include config file
require_once 'config.php';
 
// Define variables and initialize with empty values
$Voornaam = $Achternaam = $Leeftijd = $Adres = $Aantal = $Klas = "";
$Voornaam_err = $Achternaam_err = $Leeftijd_err = $Adres_err = $Aantal_err = $Klas_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate Voornaam
    $input_Voornaam = trim($_POST["Voornaam"]);
    if(empty($input_Voornaam)){
        $Voornaam_err = "Please enter a Voornaam.";
    } elseif(!filter_var(trim($_POST["Voornaam"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $Voornaam_err = 'Please enter a valid Voornaam.';
    } else{
        $Voornaam = $input_Voornaam;
    }

    // Validate Achternaam
    $input_Achternaam = trim($_POST["Achternaam"]);
    if(empty($input_Achternaam)){
        $Achternaam_err = "Please enter a Achternaam.";
    } elseif(!filter_var(trim($_POST["Achternaam"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $Achternaam_err = 'Please enter a valid Achternaam.';
    } else{
        $Achternaam = $input_Achternaam;
    }

     // Validate Leeftijd
     $input_Leeftijd = trim($_POST["Leeftijd"]);
     if(empty($input_Leeftijd)){
         $Leeftijd_err = "Please enter the Leeftijd amount.";     
     } elseif(!ctype_digit($input_Leeftijd)){
         $Leeftijd_err = 'Please enter a positive integer value.';
     } else{
         $Leeftijd = $input_Leeftijd;
     }
    
    // Validate Adres
    $input_Adres = trim($_POST["Adres"]);
    if(empty($input_Adres)){
        $Adres_err = 'Please enter an Adres.';     
    } else{
        $Adres = $input_Adres;
    }
    
    // Validate Aantal
    $input_Aantal = trim($_POST["Aantal"]);
    if(empty($input_Aantal)){
        $Aantal_err = "Please enter the Aantal amount.";     
    } elseif(!ctype_digit($input_Aantal)){
        $Aantal_err = 'Please enter a positive integer value.';
    } else{
        $Aantal = $input_Aantal;
    }

    // Validate Klas
    $input_Klas = trim($_POST["Klas"]);
    if(empty($input_Klas)){
        $Klas_err = "Please enter the Klas amount.";     
    } elseif(!ctype_digit($input_Klas)){
        $Klas_err = 'Please enter a positive integer value.';
    } else{
        $Klas = $input_Klas;
    }
    
    // Check input errors before inserting in database
    if(empty($Voornaam_err) && empty($Achternaam_err) && empty($Leeftijd_err)  && empty($Adres_err) && empty($Aantal_err) && empty($Klas_err)){
        // Prepare an insert statement
        $sql = "UPDATE oefen SET Voornaam=?, Achternaam=?, Leeftijd=?, Adres=?, Aantal=?, Klas=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_Voornaam, $param_Achternaam, $param_Leeftijd, $param_Adres, $param_Aantal, $param_Klas, $param_id);
            
            // Set parameters
            $param_Voornaam = $Voornaam;
            $param_Achternaam = $Achternaam;
            $param_Leeftijd = $Leeftijd;
            $param_Adres = $Adres;
            $param_Aantal = $Aantal;
            $param_Klas = $Klas;
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
        $sql = "SELECT * FROM oefen WHERE id = ?";
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
                    $Voornaam = $row["Voornaam"];
                    $Achternaam = $row["Achternaam"];
                    $Leeftijd = $row["Leeftijd"];
                    $Adres = $row["Adres"];
                    $Aantal = $row["Aantal"];
                    $Klas = $row["Klas"];
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
                        <div class="form-group <?php echo (!empty($Voornaam_err)) ? 'has-error' : ''; ?>">
                            <label>Voornaam</label>
                            <input type="text" name="Voornaam" class="form-control" value="<?php echo $Voornaam; ?>">
                            <span class="help-block"><?php echo $Voornaam_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Achternaam_err)) ? 'has-error' : ''; ?>">
                            <label>Achternaam</label>
                            <input type="text" name="Achternaam" class="form-control" value="<?php echo $Achternaam; ?>">
                            <span class="help-block"><?php echo $Achternaam_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Leeftijd_err)) ? 'has-error' : ''; ?>">
                            <label>Leeftijd</label>
                            <input type="text" name="Leeftijd" class="form-control" value="<?php echo $Leeftijd; ?>">
                            <span class="help-block"><?php echo $Leeftijd_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Adres_err)) ? 'has-error' : ''; ?>">
                            <label>Adres</label>
                            <textarea name="Adres" class="form-control"><?php echo $Adres; ?></textarea>
                            <span class="help-block"><?php echo $Adres_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Aantal_err)) ? 'has-error' : ''; ?>">
                            <label>Aantal</label>
                            <input type="text" name="Aantal" class="form-control" value="<?php echo $Aantal; ?>">
                            <span class="help-block"><?php echo $Aantal_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Klas_err)) ? 'has-error' : ''; ?>">
                            <label>Klas</label>
                            <textarea name="Klas" class="form-control"><?php echo $Klas; ?></textarea>
                            <span class="help-block"><?php echo $Klas_err;?></span>
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