<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

// Lay booking id
$bid = intval($_GET['bid'] ?? 0);

if($bid <= 0) {
	header('location:manage-bookings.php');
	exit;
}

// Lay chi tiet booking
$sql = "SELECT tblbooking.*,
	tblusers.FullName as fname,
	tblusers.MobileNumber as mnumber,
	tblusers.EmailId as email,
	tbltourpackages.PackageId as pkgid,
	tbltourpackages.PackageName as pckname,
	tbltourpackages.TourDuration as tourduration,
	tbltourpackages.PackagePrice as pkgprice
FROM tblbooking 
JOIN tblusers ON tblbooking.UserEmail=tblusers.EmailId 
JOIN tbltourpackages ON tbltourpackages.PackageId=tblbooking.PackageId
WHERE tblbooking.BookingId=:bid";

$query = $dbh->prepare($sql);
$query->bindParam(':bid', $bid, PDO::PARAM_INT);
$query->execute();
$booking = $query->fetch(PDO::FETCH_OBJ);

if(!$booking) {
	header('location:manage-bookings.php');
	exit;
}

// Xu ly cap nhat trang thai
$msg = '';
$error = '';
if(isset($_POST['update_status'])) {
	$newStatus = intval($_POST['status']);
	$cancelReason = trim($_POST['cancel_reason'] ?? '');
	
	if($newStatus >= 0 && $newStatus <= 2) {
		// Neu huy (status = 2) thi cap nhat them CancelledBy va CancelReason
		if($newStatus == 2) {
			$sql_update = "UPDATE tblbooking SET status=:status, CancelledBy=:cancelby, CancelReason=:cancelreason WHERE BookingId=:bid";
			$query_update = $dbh->prepare($sql_update);
			$query_update->bindParam(':status', $newStatus, PDO::PARAM_INT);
			$query_update->bindParam(':cancelby', $cancelby = 'a', PDO::PARAM_STR);
			$query_update->bindParam(':cancelreason', $cancelReason, PDO::PARAM_STR);
		} else {
			$sql_update = "UPDATE tblbooking SET status=:status WHERE BookingId=:bid";
			$query_update = $dbh->prepare($sql_update);
			$query_update->bindParam(':status', $newStatus, PDO::PARAM_INT);
		}
		$query_update->bindParam(':bid', $bid, PDO::PARAM_INT);
		
		if($query_update->execute()) {
			$msg = "Cập nhật trạng thái thành công";
			// Tai lai du lieu booking
			$query_refresh = $dbh->prepare($sql);
			$query_refresh->bindParam(':bid', $bid, PDO::PARAM_INT);
			$query_refresh->execute();
			$booking = $query_refresh->fetch(PDO::FETCH_OBJ);
		} else {
			$error = "Có lỗi xảy ra khi cập nhật";
		}
	}
}

// Cap nhat tin nhan khach hang (CustomerMessage)
if(isset($_POST['update_message'])) {
    $customerMessage = trim($_POST['customer_message'] ?? '');
    $sql_update_msg = "UPDATE tblbooking SET CustomerMessage=:message WHERE BookingId=:bid";
    $query_update_msg = $dbh->prepare($sql_update_msg);
    $query_update_msg->bindParam(':message', $customerMessage, PDO::PARAM_STR);
	$query_update_msg->bindParam(':bid', $bid, PDO::PARAM_INT);
	if($query_update_msg->execute()) {
		$msg = "Đã cập nhật lời nhắn gửi khách";
		// Tai lai du lieu booking
		$query_refresh = $dbh->prepare($sql);
		$query_refresh->bindParam(':bid', $bid, PDO::PARAM_INT);
		$query_refresh->execute();
		$booking = $query_refresh->fetch(PDO::FETCH_OBJ);
	} else {
		$error = "Có lỗi xảy ra khi lưu lời nhắn";
	}
}

// Cap nhat ghi chu noi bo admin (AdminNotes)
if(isset($_POST['update_admin_notes'])) {
	$adminNotes = trim($_POST['admin_notes'] ?? '');
	$sql_update_notes = "UPDATE tblbooking SET AdminNotes=:notes WHERE BookingId=:bid";
	$query_update_notes = $dbh->prepare($sql_update_notes);
	$query_update_notes->bindParam(':notes', $adminNotes, PDO::PARAM_STR);
	$query_update_notes->bindParam(':bid', $bid, PDO::PARAM_INT);
	if($query_update_notes->execute()) {
		$msg = "Đã lưu ghi chú nội bộ";
		$query_refresh = $dbh->prepare($sql);
		$query_refresh->bindParam(':bid', $bid, PDO::PARAM_INT);
		$query_refresh->execute();
		$booking = $query_refresh->fetch(PDO::FETCH_OBJ);
	} else {
		$error = "Có lỗi xảy ra khi lưu ghi chú";
	}
}

$pageTitle = "GoTravel Admin | Chi tiết đặt tour";
$currentPage = 'manage-bookings';

include('includes/layout-start.php');
?>

<section class="admin-page-head">
	<div>
		<h1>Chi tiết đặt tour #<?php echo htmlentities($booking->BookingId); ?></h1>
		<p>Quản lý thông tin và cập nhật trạng thái đặt tour</p>
	</div>
	<a href="manage-bookings.php" class="btn btn-ghost">← Quay lại</a>
</section>

<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>

<div class="grid-two">
	<!-- Left Column -->
	<div>
		<!-- Customer Info -->
		<section class="card">
			<h3>Thông tin Khách hàng</h3>
			<div class="info-group">
				<div class="info-row">
					<span class="label">Họ và tên:</span>
					<strong><?php echo htmlentities($booking->fname); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Số điện thoại:</span>
					<strong><?php echo htmlentities($booking->mnumber); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Email:</span>
					<strong><?php echo htmlentities($booking->email); ?></strong>
				</div>
			</div>
		</section>

		<!-- Order Info -->
		<section class="card">
			<h3>Thông tin Đơn hàng</h3>
			<div class="info-group">
				<div class="info-row">
					<span class="label">Tên Tour:</span>
					<strong><?php echo htmlentities($booking->pckname); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Mã Tour:</span>
					<strong>#PKG-<?php echo htmlentities($booking->pkgid); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Thời gian tour:</span>
					<strong><?php echo htmlentities($booking->tourduration); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Ngày khởi hành:</span>
					<strong><?php echo date('d/m/Y', strtotime($booking->FromDate)); ?></strong>
				</div>
				<div class="info-row">
					<span class="label">Số lượng khách:</span>
					<strong><?php echo htmlentities($booking->NumberOfPeople); ?> người</strong>
				</div>
				<div class="info-row">
					<span class="label">Tổng tiền:</span>
					<strong>
						<?php 
						$computedTotal = (int)$booking->pkgprice * (int)$booking->NumberOfPeople; 
						echo number_format($computedTotal, 0, ',', '.') . ' VND';
						?>
					</strong>
				</div>
			</div>
		</section>

		<!-- Notes -->
		<section class="card">
			<h3>Ghi chú & Yêu cầu</h3>
			<div class="info-group">
				<div class="info-item">
					<h4>Ghi chú của khách:</h4>
					<p><?php echo nl2br(htmlentities($booking->Comment)); ?></p>
				</div>
				<div class="info-item">
					<h4>Lời nhắn gửi khách</h4>
					<form method="post" class="form-stack">
						<div class="form-group">
							<label for="customer_message">Nội dung lời nhắn:</label>
							<textarea id="customer_message" name="customer_message" rows="4"><?php echo htmlentities($booking->CustomerMessage ?? ''); ?></textarea>
						</div>
						<button type="submit" name="update_message" class="btn">Lưu lời nhắn</button>
					</form>
				</div>
			</div>
		</section>
	</div>

	<!-- Right Column -->
	<div>
		<!-- Ghi chú nội bộ (Admin) -->
		<section class="card">
			<h3>Ghi chú nội bộ (Admin)</h3>
			<form method="post" class="form-stack">
				<div class="form-group">
					<label for="admin_notes_internal">Ghi chú Admin:</label>
					<textarea id="admin_notes_internal" name="admin_notes" rows="5"><?php echo htmlentities($booking->AdminNotes ?? ''); ?></textarea>
				</div>
				<button type="submit" name="update_admin_notes" class="btn">Lưu ghi chú</button>
			</form>
		</section>

		<!-- Thao tác -->
		<section class="card">
			<h3>Thao tác</h3>
			<div class="action-stack">
				<?php if($booking->status != 1) { ?>
					<form method="post" class="action-row">
						<input type="hidden" name="status" value="1">
						<input type="hidden" name="admin_notes" value="<?php echo htmlentities($booking->AdminNotes ?? ''); ?>">
						<button type="submit" name="update_status" class="btn btn-primary btn-full">
							Xác nhận đơn
						</button>
					</form>
				<?php } else { ?>
					<div class="action-row">
						<button class="btn btn-muted btn-full" disabled>Đã xác nhận</button>
					</div>
				<?php } ?>

				<?php if($booking->status != 2) { ?>
					<div class="action-row">
						<button class="btn btn-danger btn-full" type="button" onclick="document.getElementById('cancelModal').style.display='block';">
							Hủy đơn
						</button>
					</div>
				<?php } else { ?>
					<div class="action-row">
						<button class="btn btn-muted btn-full" disabled>Đã hủy</button>
					</div>
				<?php } ?>
			</div>
		</section>
	</div>
</div>

<!-- Modal Xác nhận đã được loại bỏ theo yêu cầu -->

<!-- Modal Hủy đơn -->
<div id="cancelModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
	<div style="background: white; padding: 2rem; border-radius: 8px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
		<h3>Hủy đơn hàng</h3>
		<form method="post" style="margin-top: 1rem;">
			<div class="form-group">
				<label for="cancel_reason">Lý do hủy đơn:</label>
				<textarea id="cancel_reason" name="cancel_reason" rows="4" placeholder="Nhập lý do hủy..." required></textarea>
			</div>
			<div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
				<button type="button" onclick="document.getElementById('cancelModal').style.display='none';" class="btn btn-ghost" style="flex: 1;">Đóng</button>
				<input type="hidden" name="status" value="2">
				<input type="hidden" name="admin_notes" value="<?php echo htmlentities($booking->AdminNotes ?? ''); ?>">
				<button type="submit" name="update_status" class="btn btn-danger" style="flex: 1;">Hủy đơn</button>
			</div>
		</form>
	</div>
</div>

<style>
.grid-two {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 1.5rem;
	margin-top: 1.5rem;
}

@media (max-width: 768px) {
	.grid-two {
		grid-template-columns: 1fr;
	}
}

.info-group {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.info-row {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	padding: 0.75rem;
	background: #f5f5f5;
	border-radius: 4px;
	gap: 1rem;
}

.info-row .label {
	font-weight: 600;
	color: #666;
	min-width: 150px;
}

.info-item {
	padding: 0.75rem;
	background: #f5f5f5;
	border-radius: 4px;
}

.action-stack {
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
}

.action-row {
	display: flex;
}

.btn-full {
	width: 100%;
}

.btn-muted {
	background: #f0f0f0;
	color: #666;
	cursor: not-allowed;
}

.info-item h4 {
	margin: 0 0 0.5rem 0;
	font-size: 0.9rem;
	color: #666;
}

.info-item p {
	margin: 0;
	color: #333;
}

.admin-page-head {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	margin-bottom: 1.5rem;
}

.admin-page-head div h1 {
	margin: 0 0 0.25rem 0;
}

.form-control {
	width: 100%;
	padding: 0.75rem;
	border: 1px solid #ddd;
	border-radius: 4px;
	font-size: 1rem;
	font-family: inherit;
}

.form-control:focus {
	outline: none;
	border-color: #007bff;
	box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}
</style>

<?php include('includes/layout-end.php');?>
