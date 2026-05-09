<?php
// Script test nhanh de them tin nhan khach hang vao booking 11
include 'admin/includes/config.php';

$bid = 11;
$message = "Xin chào, chúng tôi đã xác nhận đơn đặt tour của bạn. Vui lòng chuẩn bị các giấy tờ cần thiết trước ngày khởi hành. Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc điện thoại.";

$sql = "UPDATE tblbooking SET CustomerMessage = :msg WHERE BookingId = :bid";
$query = $dbh->prepare($sql);
$query->bindParam(':msg', $message, PDO::PARAM_STR);
$query->bindParam(':bid', $bid, PDO::PARAM_INT);

if($query->execute()) {
    echo "✓ Đã thêm lời nhắn test cho booking #" . $bid;
} else {
    echo "✗ Lỗi khi thêm lời nhắn";
}
?>
