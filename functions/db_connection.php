<?php

function getDbConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "ngochiep2k5";
    $dbname = "quanlyhoctap";
    $port = 3307;

    // In ra thông tin kết nối để debug
    error_log("Đang kết nối đến: $servername:$port, DB: $dbname, User: $username");

    // Tạo kết nối
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    
    // Kiểm tra cơ sở dữ liệu thực tế
    $db_result = mysqli_query($conn, "SELECT DATABASE() as db_name");
    $db_row = mysqli_fetch_assoc($db_result);
    error_log("Cơ sở dữ liệu thực tế sau kết nối: " . $db_row['db_name']);
    
    // Thiết lập charset cho kết nối (quan trọng để hiển thị tiếng Việt đúng)
    mysqli_set_charset($conn, "utf8");
    return $conn;
}
?>