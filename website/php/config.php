<?php

    $con = new mysqli("localhost", "root", "", "charitydb");
    
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

?>