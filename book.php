<?php
session_start();
?>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Booking Appointment</title>
</head>
<body>
<?php
include 'config.php';
$conn = mysqli_connect($servername, $username, $password,  $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$start_day = intval(strtotime(htmlspecialchars($_POST["start_day"])));
$start_time = (60*60*intval(htmlspecialchars($_POST["start_hour"]))) + (60*intval(htmlspecialchars($_POST["start_minute"])));
//$end_day = intval(strtotime(htmlspecialchars($_POST["end_day"])));
$end_day = intval(strtotime(htmlspecialchars($_POST["start_day"])));
//$end_time = (60*60*intval(htmlspecialchars($_POST["end_hour"]))) + (60*intval(htmlspecialchars($_POST["end_minute"])));
$end_time = (60*60*intval(htmlspecialchars($_POST["start_hour"]))) + (60*(intval(htmlspecialchars($_POST["start_minute"])+30));
$name = htmlspecialchars($_POST["name"]);
$phone = htmlspecialchars($_POST["phone"]);
$email = htmlspecialchars($_POST["email"]);
$item = htmlspecialchars($_POST["item"]);
$start_epoch = $start_day + $start_time;
$end_epoch = $end_day + $end_time;
$sql = "SELECT * FROM $tablename WHERE item='$item' AND (start_day>=$start_day OR end_day>=$start_day) AND canceled=0";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        for ($i = $start_epoch; $i <= $end_epoch; $i=$i+600) {
            if ($i>($row["start_day"]+$row["start_time"]) && $i<($row["end_day"]+$row["end_time"])) {
                echo '<h3><font color="red">Unfortunately ' . $item . ' appointment already exists for the date & time requested.</font></h3>';
                goto end;
            }
        }
    }
}
$sql = "INSERT INTO $tablename (name, phone, email, item, start_day, start_time, end_day, end_time, canceled)
    VALUES ('$name','$phone', '$email', '$item', $start_day, $start_time, $end_day, $end_time, 0)";
if (mysqli_query($conn, $sql)) {
    mysqli_close($conn);

    $to = 'ddarizona158@gmail.com';
    $subject = 'Test Subject';
    $message = 'Test Message';
    $headers = 'From: webmaster@example.com'       . "\r\n" .
                 'Reply-To: webmaster@example.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);

    echo "<script>alert('Appointment was booked successfully with a confirmation email');window.location.href='index.php';</script>";
} else {
    echo "<script>alert('Error occurred while booking appointment. Please contact admin!');window.location.href='index.php';</script>";
}
end:
?>
<a href="index.php"><p>Back To Booking</p></a>
</body>
</html>