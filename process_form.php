<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $mob_no = $_POST["mob_no"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "enq_form";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data into the database
    $sql = "INSERT INTO cform (`name`, `mob_no`, `email`, `address`) VALUES ('$name', '$mob_no', '$email', '$address')";

    if ($conn->query($sql) === TRUE) {
        // Create a PHPMailer object
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = ''; // Your Gmail address
            $mail->Password   = ''; // Your Gmail password ( or use app passwords on google )
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SSL
            $mail->Port       = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('', 'Your Name');
            $mail->addAddress($email, $name); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Registration Confirmation';
            $mail->Body    = "Hello $name,<br><br>Thank you for your registration. You are successfully registered with the following details:<br><br>Name: $name<br>Mobile Number: $mob_no<br>Email: $email<br>Address: $address<br><br>Best regards,<br>Your Website Team";

            $mail->send();

            // Redirect user to a thank you page
            header("Location: thank_you.php");
            exit();
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
