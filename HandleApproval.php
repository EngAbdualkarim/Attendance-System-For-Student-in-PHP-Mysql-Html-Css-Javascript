<?php
session_start();
include('connect.php');

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor-login.php");
    exit;
}

$instructorId = $_SESSION['instructor_id'];

if (isset($_GET['excuseId']) && isset($_GET['decision'])) {
    $excuseId = $_GET['excuseId'];
    $decision = $_GET['decision'];

    $updateQuery = "UPDATE UploadedExcuses SET decision = '$decision' WHERE id = $excuseId";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}
?>
