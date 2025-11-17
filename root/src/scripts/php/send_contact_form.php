<?php
require_once('../../../config/constants.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $to = "khan661@uwindsor.ca";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $fullMessage =
        "New message from ClubHub Contact Form\n\n" .
        "Name: $name\n" .
        "Email: $email\n" .
        "Subject: $subject\n\n" .
        "Message:\n$message\n";

    if (mail($to, $subject, $fullMessage, $headers)) {
        header("Location: /COMP-4150-Group-Project/root/public/contact.php?success=1");
        exit;
    } else {
        header("Location: /COMP-4150-Group-Project/root/public/contact.php?error=1");
        exit;
    }
}
?>
