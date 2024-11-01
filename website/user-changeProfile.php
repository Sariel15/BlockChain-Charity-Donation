<?php
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: user-login.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <a href="user-home.php">
                <img src="giftchain-withoutname.png" alt="GiftChain Logo" class="logo">
            </a>        
        </div>

        <div class="right-links">
            <a href="php/logout.php"><button class="btn">Logout</button></a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <?php
                if(isset($_POST['submit'])){
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    $id = $_SESSION['id'];

                    $edit_query = mysqli_query($con,"UPDATE users SET Username='$username',Email='$email',Password='$password' WHERE Id=$id") or die("error occured");
                    if($edit_query){
                        echo "<div class='message'>
                                    <p>Profile Updated!</p>
                                </div><br>";
                        echo "<a href='user-home.php'><button class='btn'>Go Home</button>";
                    }
                }else{
                    $id = $_SESSION['id'];
                    $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id");

                    while($result = mysqli_fetch_assoc($query)){
                        $res_Uname = $result['Username'];
                        $res_Email= $result['Email'];
                        $res_Password = $result['PASSWORD'];
        
                    }
            ?>
            <header>Change Profile</header>
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
                    <label for="password">Password</label>
                    <input type="text" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field input">
                    <input type="submit" name="submit" class="btn" value="Update" required>
                </div>
                <div class="field input">
                        <a href="user-home.php"><button type="button" class="btn">Go Home</button></a>
                    </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>