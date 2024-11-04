<?php
session_start();

include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: admin-login.php");
    exit();
}

$query = mysqli_query($con, "SELECT * FROM admins");
while ($result = mysqli_fetch_assoc($query)) {
    $res_Uname = $result['Username'];
    $res_Email = $result['Email'];
    $res_id = $result['Id'];
}

if (!isset($_SESSION['metamask_address'])) {
    $_SESSION['metamask_address'] = '0x0B53Db0122c2430590240e5CE54cA1E86542f1df';  // Default value
}

$metamask_address = $_SESSION['metamask_address'];

$totalDonationResult = mysqli_query($con, "SELECT SUM(donation_amount) AS total_donations FROM donations");
$totalDonations = mysqli_fetch_assoc($totalDonationResult)['total_donations'];

$donationResult = mysqli_query($con, "SELECT id, username, donor_address, donation_amount, created_at FROM donations ORDER BY created_at ASC");

if (isset($_POST['update_metamask'])) {
    $metamask_address = $_POST['metamask_address'];

    if (!empty($metamask_address)) {
        $_SESSION['metamask_address'] = $metamask_address;
        $success_message = "MetaMask address updated successfully!";
    } else {
        $error_message = "Please enter a MetaMask address.";
    }
}

if (isset($_POST['delete_donations'])) {
    if (!empty($_POST['selected_donations'])) {
        foreach ($_POST['selected_donations'] as $donationId) {
            $donationId = mysqli_real_escape_string($con, $donationId);
            mysqli_query($con, "DELETE FROM donations WHERE id = '$donationId'");
        }
        $_SESSION['message'] = "Selected donations have been deleted successfully.";
    } else {
        $_SESSION['error'] = "No donations selected for deletion.";
    }

    header("Location: admin-home.php");
    exit();
}

$metamask_address = $_SESSION['metamask_address'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Admin Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <a href="admin-home.php">
                <img src="giftchain-withoutname.png" alt="GiftChain Logo" class="logo">
            </a>
        
        </div>
        

        <div class="right-links">
            <?php echo "<a href='admin-changeProfile.php?Id=$res_id'>Change Profile</a>"; ?>
            <a href="php/logout.php"><button class="btn">Logout</button></a>
        </div>
    </div>
    <main>
        <div class="main-box top">
            <div class="top">
                <div class="box">
                    <p>Hello Admin <b><?php echo htmlspecialchars($res_Uname); ?></b>, Welcome!</p>
                </div>
                <div class="box">
                    <p>Metamask Account: <b id="metamask-account"><?php echo $metamask_address; ?></b></p>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <h1>Charity Donation Tracker</h1>
                    
                    <form method="post" action="admin-home.php">
                        <label for="metamask_address">Enter MetaMask Address for Donations:</label>
                        <input type="text" name="metamask_address" id="metamask_address" placeholder="Enter MetaMask Address" required>
                        <button type="submit" name="update_metamask" class="btn">Update Address</button>
                    </form>

                    <?php
                        if (isset($success_message)) {
                            echo "<p class='success'>$success_message</p>";
                        }
                        if (isset($error_message)) {
                            echo "<p class='error'>$error_message</p>";
                        }
                    ?>

                    <div class="summary">
                        <h3>Total Donations: <span id="total-donations">
                            <?php echo $totalDonations ? $totalDonations : '0'; ?>
                        </span> ETH</h3>                    </div>

                    <div class="donor-list">
                        <?php if (isset($_SESSION['message'])): ?>
                            <p class="message" id="flash-message"><?php echo $_SESSION['message']; ?></p>
                            <?php unset($_SESSION['message']); // Clear the message after it's displayed ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <p class="error" id="flash-error"><?php echo $_SESSION['error']; ?></p>
                            <?php unset($_SESSION['error']); // Clear the error after it's displayed ?>
                        <?php endif; ?>

                        <?php if (mysqli_num_rows($donationResult) > 0) { ?>
                            <!-- Display the table if there are donation records -->
                            <table id="donation-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select-all"></th>
                                            <th>Username</th>
                                            <th>Donor Address</th>
                                            <th>Amount (ETH)</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($donationResult)) {
                                            echo "<tr>";
                                            echo "<td><input type='checkbox' class='donation-row' name='selected_donations[]' value='" . $row['id'] . "'></td>"; // Checkbox for each row
                                            echo "<td>" . $row['username'] . "</td>";
                                            echo "<td>" . $row['donor_address'] . "</td>";
                                            echo "<td>" . $row['donation_amount'] . " ETH</td>";
                                            echo "<td>" . date("F j, Y, g:i a", strtotime($row['created_at']));
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <!-- Buttons to process or delete the selected donations -->
                                <button type="submit" name="delete_donations" class="btn">Delete Selected Donations</button>
                            </form>
                        <?php } else { ?>
                            <!-- Display message if no donations are found -->
                            <p>No donations yet.</p>
                        <?php } ?>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
                <script src="admin-app.js"></script>
            </div>
        </div>
    </main>
</body>
</html>
