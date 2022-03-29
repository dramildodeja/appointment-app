<?php
session_start();
?>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Cancel Booking</title>
</head>
<body>
<?php
include 'config.php';
$conn = mysqli_connect($servername, $username, $password,  $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$id = intval(htmlspecialchars($_POST["id"]));
$sql = "UPDATE $tablename SET canceled=1 WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    echo "<h3>Booking cancelled.</h3>";
}
else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>
<a href="../index.php"><p>Back to the booking calendar</p></a>
</body>
</html>