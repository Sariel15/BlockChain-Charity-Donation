<?php
session_start();

if (isset($_SESSION['metamask_address'])) {
    echo $_SESSION['metamask_address'];
} else {
    echo '0x0B53Db0122c2430590240e5CE54cA1E86542f1df'; // Default address
}
?>
