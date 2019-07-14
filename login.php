<?php
// Initialize the session
session_start();
 
// if logged in go to game page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    
    $_SESSION['username']=$username;
    header("location: cloud.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// variables set as empty
$username = $password = "";
$username_wr = $password_wr = "";
 
// Check form
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username and password is empty
    if(empty(trim($_POST["username"]))){
        $username_wr = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_wr = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validation
    if(empty($username_wr) && empty($password_wr)){
        // Select from database
        $sql = "SELECT id, username, password,Points,counter FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // link as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    //Result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password,$Points,$counter);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            $_SESSION["Points"] = $Points; 
                            $_SESSION["counter"] = $counter;
                           $_SESSION['z']=0;
                            // Redirect user to game page
                            header("location: cloud.php");
                        } else{
                            // error message
                            $password_wr = "Λάθος κωδικός.";
                        }
                    }
                } else{
                    // no username 
                    $username_wr = "Δεν βρεθηκε το όνομα χρήστη.";
                }
            } else{
                echo "Ωχ, κατι πήγε στραβά.";
            }
          }
        
        mysqli_stmt_close($stmt);
           }
    
    
    mysqli_close($link);
}
?>


<html>
<head>
<title>Login</title>
</head>
<body style="background-color:#a0bab0; font-family:sans-serif">

<p><b>Σύνδεση Ιατρικού Προσωπικού:</b></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	<div class="container" <?php echo (!empty($username_wr)) ? 'has-error' : ''; ?>>

<label for="username"><b>Όνομα Χρήστη</b></label>
    <input type="text" placeholder="Εισάγετε όνομα χρήστη" value="<?php echo $username; ?>" name="username" required >
    <span class="help-block"><?php echo $username_wr; ?></span>
    <label for="password"><b>Κωδικός</b></label>
    <input type="password" placeholder="Εισάγετε Κωδικό" name="password" required>
     <span class="help-block"><?php echo $password_wr; ?></span>
    <button type="submit">Σύνδεση</button>
 <label>
 	</div>
 	 <div class="container <?php echo (!empty($password_wr)) ? 'has-error' : ''; ?>" style="background-color:#666ca6" >
   
  </div>
  
  
</form>

</form>
<center>
<h1 style="background-color:#8b8c96">ΠΡΟΒΟΛΗ ΚΑΙ ΚΑΤΑΧΩΡΗΣΗ ΙΑΤΡΙΚΟΥ ΦΑΚΕΛΟΥ</h1>
<img src="hospital.png" style="width:100%;height:80%;"> 
</center>


</body>
</html>

