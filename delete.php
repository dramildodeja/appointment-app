<?php
session_start();
?>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Delete Appointment Booking</title>
</head>
<body>
<?php
include 'config.php';
$conn = mysqli_connect($servername, $username, $password,  $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$id = intval(htmlspecialchars($_GET["id"]));
$sql = "DELETE FROM $tablename WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    mysqli_close($conn);
    echo "<script>alert('Appointment was deleted successfully');window.location.href='index.php?viewall=true';</script>";
} else {
    echo "<script>alert('Error occurred while deleting appointment. Please contact admin!');window.location.href='index.php?viewall=true';</script>";
}
?>
</body>
</html>