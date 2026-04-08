<?php
require 'PHPMailer/PHPMailerAutoload.php';

// Function to get client IP
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Collect POST data correctly (case-sensitive keys!)
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['number']) ? trim($_POST['number']) : '';

    // Basic backend validation
    if ($name === '' || $email === '' || $phone === '') {
        echo json_encode(['status' => 'error', 'message' => 'Name, Email, and Phone are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        exit;
    }
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid 10 digit phone number.']);
        exit;
    }
    
    $ip = get_client_ip(); // Keeps your IP function
    
    // Create the email body content (NO COUNTRY CODE ANYMORE)
    $bodyContent = "<h1>Enquiry Form - Dosti Greenscapes</h1>";
    $bodyContent .= "
        Name: <strong>$name</strong><br>
        Email: $email<br>
        Phone Number: $phone<br>
        IP Address: $ip<br>
    ";

    
    // Set up PHPMailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@punyacentralpune.com';
    $mail->Password = 'Infopune@1234';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('info@punyacentralpune.com', 'Dosti Greenscapes');
    $mail->addAddress('balajirealestateagency9@gmail.com');
    $mail->addAddress('abhijitsarvade39@gmail.com');


    // Set email format to HTML
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';  // Ensure proper encoding

    $mail->Subject = "$name ($email) Sent Email From Enquiry Form For - Dosti Greenscapes";
    $mail->Body = $bodyContent;

    // Send email and handle result
    if (!$mail->send()) {
        // Send a valid JSON response with error message
        echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        exit;
    } else {
        // Send a valid JSON response with success message
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
