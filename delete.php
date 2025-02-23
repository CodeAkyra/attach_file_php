<?php
include 'conn.php';

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT file FROM files WHERE id = '$id'");
$row = mysqli_fetch_assoc($result);
$filename = "files/" . $row['file'];

if (file_exists($filename)) {
    unlink($filename);
}

mysqli_query($conn, "DELETE FROM files WHERE id = '$id'");
header("location: index.php");
