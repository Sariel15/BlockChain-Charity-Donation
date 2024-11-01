<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php 
            include("php/config.php");
            if(isset($_POST['submit'])){
                $username = $_POST['username'];
                $email = $_POST['email'];
                $passkey = $_POST['passkey'];
                $password = $_POST['password'];

            //verifying the email

            $verify_query = mysqli_query($con,"SELECT Email FROM admins WHERE Email = '$email'");
            $valid_passkey = 'harthart';
            if(mysqli_num_rows($verify_query)!=0){
                echo "<div class='message'>
                            <p>This email is used, Try another One Please!</p>
                        </div><br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            }else if ($passkey !== $valid_passkey){
                echo "<div class='message'>
                            <p>The registered Passkey is invalid. Please input a valid Passkey.</p>
                        </div><br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            }else{
                mysqli_query($con,"INSERT INTO Admins(Username,Email,Passkey,Password) VALUES('$username','$email','$passkey','$password')") or die("Error Occured");
                echo "<div class='message'>
                            <p>Registration Successfully!</p>
                        </div><br>";
                echo "<a href='admin-login.php'><button class='btn'>Login Now</button>";
            }
            }else{

            ?>
            <header>Sign Up</header>
            <form action="" method="POST">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="passkey">PassKey (provided by the company)</label>
                    <input type="password" name="passkey" id="passkey" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field input">
                    <input type="submit" name="submit" class="btn" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="admin-login.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>