<?php
try {
	$to      = 'ddarizona158@gmail.com';
    $subject = 'Test Subject';
    $message = 'Test Message';
    $headers = 'From: webmaster@example.com'       . "\r\n" .
                 'Reply-To: webmaster@example.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
	echo "Mail has been sent successfully!";
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$e}";
}
?>