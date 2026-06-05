<?php
session_start();
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if PromotionID and status are set in the URL
if (isset($_GET['PromotionID']) && isset($_GET['status'])) {
    $promotionID = $_GET['PromotionID'];
    $status = $_GET['status'];

    // Update promotion status in the database
    $sql = "UPDATE Promotions SET Status = ? WHERE PromotionID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $promotionID);
    mysqli_stmt_execute($stmt);

    // Check if the update was successful
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // If status is changed to active, send promotion to users via PHP mailer
        if ($status === "active") {
            // Fetch all user emails from the database
            $sql_users = "SELECT email FROM Users";
            $result_users = mysqli_query($conn, $sql_users);
            $user_emails = mysqli_fetch_all($result_users, MYSQLI_ASSOC);

             // Server settings
             $mail = new PHPMailer(true);
             $mail->isSMTP();
             $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
             $mail->SMTPAuth = true;
             $mail->Username = 'mehrooz.jamal10@gmail.com'; // Replace with your SMTP username
             $mail->Password = "YOUR_OWN_API_KEY"; // Replace with your SMTP password
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port = 587; // Replace with your SMTP port (usually 587)
 
             // Recipients
             $mail->setFrom('mehrooz.jamal10@gmail.com', 'E-Carpool');  // Replace with your email and name

            // Example mail content
            $subject = "New Promotion Alert";
            $message = "Dear user,\n\nWe are excited to announce a new promotion! Check it out on our website.\n\nBest regards,\n E-Carpool Team";

            // Send email to each user
            foreach ($user_emails as $user_email) {
                $recipient_email = $user_email['email'];
                $mail->addAddress($recipient_email);
                $mail->Subject = $subject;
                $mail->Body = $message;
                if (!$mail->send()) {
                    echo "Error sending email to $recipient_email: " . $mail->ErrorInfo . "<br>";
                } else {
                    echo "Email sent to $recipient_email successfully.<br>";
                }
                $mail->clearAddresses();
            }
        }

        // Redirect back to view_promotions.php with success message
        header("Location: view_promotions.php");
        exit;
    } else {
        // Redirect back to view_promotions.php with error message
        header("Location: view_promotions.php?error=update_failed");
        exit;
    }
} else {
    // Redirect back to view_promotions.php with error message if PromotionID or status is not set
    header("Location: view_promotions.php?error=missing_params");
    exit;
}
?>
