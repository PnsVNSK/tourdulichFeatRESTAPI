<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_REQUEST['bkid']) && isset($_POST['cancel_reason']))
{
	$bid=intval($_GET['bkid']);
	$status=2;
	$cancelby='a';
	$cancelReason = $_POST['cancel_reason'];
	// todo: gui ly do huy cho nguoi dung (se bo sung sau)
	$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby,CancelReason=:cancelReason WHERE BookingId=:bid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
	$query -> bindParam(':cancelReason',$cancelReason , PDO::PARAM_STR);
	$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Hủy đặt tour thành công. Lý do: " . htmlentities($cancelReason);
}

if(isset($_REQUEST['bckid']))
{
	$bcid=intval($_GET['bckid']);
	$status=1;
	$sql = "UPDATE tblbooking SET status=:status WHERE BookingId=:bcid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query->bindParam(':bcid',$bcid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Xác nhận đặt tour thành công";
}

if(isset($_GET['del']))
{
	$delid=intval($_GET['del']);
	$sql = "DELETE FROM tblbooking WHERE BookingId=:delid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã xóa bản ghi đặt tour";
}

$pageTitle = "GoTravel Admin | Quản lý đặt tour";
$currentPage = 'manage-bookings';
$sql = "SELECT tblbooking.BookingId as bookid,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.EmailId as email,tbltourpackages.PackageName as pckname,tbltourpackages.TourDuration as tourduration,tblbooking.PackageId as pid,tblbooking.FromDate as fdate,tblbooking.ToDate as tdate,tblbooking.Comment as comment,tblbooking.NumberOfPeople as numppl,tblbooking.TotalPrice as totalprice,tblbooking.status as status,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate,tblbooking.RegDate as regdate from tblusers join  tblbooking on  tblbooking.UserEmail=tblusers.EmailId join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId ORDER BY tblbooking.RegDate DESC";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Quản lý đặt tour</h1>
			<p>Cập nhật trạng thái và theo dõi các yêu cầu đặt tour.</p>
		</div>
	</section>
	<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>
	<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
	<section class="card">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Mã (#ID)</th>
						<th>Khách hàng</th>
						<th>Thông tin Tour</th>
						<th>Thời gian tour</th>
						<th>Ngày khởi hành</th>
						<th>Số lượng khách</th>
						<th>Tổng tiền</th>
						<th>Trạng thái</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody>
				<?php if($query->rowCount() > 0) {
					foreach($results as $result) {
						$statusClass = 'is-pending';
						$statusText = 'Chờ xử lý';
						if($result->status==1){ $statusClass='is-approved'; $statusText='Đã xác nhận'; }
						if($result->status==2 && $result->cancelby=='a'){ $statusClass='is-cancelled'; $statusText='Quản trị viên hủy'; }
						if($result->status==2 && $result->cancelby=='u'){ $statusClass='is-cancelled'; $statusText='Người dùng hủy'; }
				?>
				<tr>
					<td><strong>#<?php echo htmlentities($result->bookid);?></strong></td>
					<td><?php echo htmlentities($result->fname);?></td>
					<td><?php echo htmlentities($result->pckname);?></td>
					<td><?php echo htmlentities($result->tourduration);?></td>
					<td><?php echo date('d/m/Y', strtotime($result->fdate));?></td>
					<td><?php echo htmlentities($result->numppl);?> người</td>
					<td><?php echo number_format($result->totalprice, 0, ',', '.') . ' VND';?></td>
					<td><span class="status-chip <?php echo $statusClass;?>"><?php echo htmlentities($statusText);?></span></td>
					<td>
						<div style="display:flex; gap:.5rem;">
							<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/view-booking.php?bid=<?php echo htmlentities($result->bookid);?>">Chi tiết</a>
							<a class="btn btn-danger" href="<?php echo BASE_URL; ?>admin/manage-bookings.php?del=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đặt tour #<?php echo htmlentities($result->bookid);?>?');">Xóa</a>
						</div>
					</td>
				</tr>
				<?php }
				} else { ?>
				<tr><td colspan="9"><div class="empty-state">Hiện chưa có đặt tour nào.</div></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
	<script>
	function promptCancelReason(form) {
		var reason = prompt('Vui lòng nhập lý do hủy tour:');
		if (reason === null || reason.trim() === '') {
			alert('Bạn phải nhập lý do hủy tour.');
			return false;
		}
		form.querySelector('input[name="cancel_reason"]').value = reason;
		return true;
	}
	</script>
<?php include('includes/layout-end.php');?>
