<?php
session_start();

if (isset($_POST['metamask_address'])) {
    $metamask_address = $_POST['metamask_address'];
    $_SESSION['metamask_address'] = $metamask_address;
    echo "success";
} else {
    echo "error";
}
?>
