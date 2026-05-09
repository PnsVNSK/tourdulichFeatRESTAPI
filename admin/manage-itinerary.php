<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {	
	header('location:index.php');
} else {
	$pid = intval($_GET['pid']);
	
	// Lay thong tin goi tour
	$sql = "SELECT PackageName FROM tbltourpackages WHERE PackageId = :pid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	
	if(!$package) {
		header('location:manage-packages.php');
		exit;
	}
	
	// Xu ly them
	if(isset($_POST['add'])) {
		$timeLabel = $_POST['timeLabel'];
		$activity = $_POST['activity'];
		
		$sql = "SELECT COALESCE(MAX(SortOrder), 0) + 1 as NextOrder FROM tblitinerary WHERE PackageId = :pid";
		$query = $dbh->prepare($sql);
		$query->bindParam(':pid', $pid, PDO::PARAM_INT);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$sortOrder = $result->NextOrder;
		
		$sql = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) VALUES (:pid, :timeLabel, :activity, :sortOrder)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':pid', $pid, PDO::PARAM_INT);
		$query->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
		$query->bindParam(':activity', $activity, PDO::PARAM_STR);
		$query->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
		$query->execute();
		
		$msg = "Đã thêm lộ trình thành công";
	}
	
	// Xu ly cap nhat
	if(isset($_POST['update'])) {
		$id = intval($_POST['id']);
		$timeLabel = $_POST['timeLabel'];
		$activity = $_POST['activity'];
		$sortOrder = intval($_POST['sortOrder']);
		
		$sql = "UPDATE tblitinerary SET TimeLabel = :timeLabel, Activity = :activity, SortOrder = :sortOrder WHERE ItineraryId = :id";
		$query = $dbh->prepare($sql);
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
		$query->bindParam(':activity', $activity, PDO::PARAM_STR);
		$query->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
		$query->execute();
		
		$msg = "Đã cập nhật lộ trình thành công";
	}
	
	// Xu ly xoa
	if(isset($_GET['del'])) {
		$id = intval($_GET['del']);
		$sql = "DELETE FROM tblitinerary WHERE ItineraryId = :id";
		$query = $dbh->prepare($sql);
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		$msg = "Đã xóa lộ trình thành công";
	}
	
	// Lay tat ca lich trinh
	$sql = "SELECT * FROM tblitinerary WHERE PackageId = :pid ORDER BY SortOrder ASC, ItineraryId ASC";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$itineraries = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Quản lý lộ trình - <?php echo htmlentities($package->PackageName); ?></title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=5.0">
	<style>
		.itinerary-container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 2rem;
		}
		.itinerary-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 2rem;
			background: white;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.itinerary-table th,
		.itinerary-table td {
			padding: 1rem;
			text-align: left;
			border-bottom: 1px solid var(--border);
		}
		.itinerary-table th {
			background: var(--brand);
			color: white;
			font-weight: 600;
		}
		.itinerary-table tr:hover {
			background: var(--brand-soft);
			cursor: pointer;
		}
		.itinerary-table tr.selected {
			background: var(--brand-soft);
			border-left: 4px solid var(--brand);
		}
		.form-section {
			background: white;
			padding: 2rem;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.form-group {
			margin-bottom: 1.5rem;
		}
		.form-group label {
			display: block;
			margin-bottom: 0.5rem;
			font-weight: 600;
			color: var(--text);
		}
		.form-group input,
		.form-group textarea {
			width: 100%;
			padding: 0.75rem;
			border: 1px solid var(--border);
			border-radius: 8px;
			font-family: inherit;
		}
		.form-group textarea {
			min-height: 120px;
			resize: vertical;
		}
		.form-actions {
			display: flex;
			gap: 1rem;
		}
		.btn-small {
			padding: 0.5rem 1rem;
			font-size: 0.875rem;
		}
		.action-buttons {
			display: flex;
			gap: 0.5rem;
			align-items: center;
		}
		.alert {
			padding: 1rem;
			border-radius: 8px;
			margin-bottom: 1rem;
		}
		.alert-success {
			background: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		.back-link {
			display: inline-block;
			margin-bottom: 1rem;
			color: var(--brand);
			text-decoration: none;
		}
		.back-link:hover {
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<div class="itinerary-container">
		<a href="<?php echo BASE_URL; ?>admin/manage-packages.php" class="back-link">← Quay lại danh sách gói tour</a>
		
		<h1 style="color: var(--brand); margin-bottom: 0.5rem;">Quản lý lộ trình</h1>
		<p style="color: var(--muted); margin-bottom: 2rem;">Gói tour: <strong><?php echo htmlentities($package->PackageName); ?></strong></p>
		
		<?php if(isset($msg)) { ?>
			<div class="alert alert-success"><?php echo $msg; ?></div>
		<?php } ?>
		
		<!-- Itinerary Table -->
		<h2 style="margin-bottom: 1rem;">Danh sách lộ trình</h2>
		<?php if(count($itineraries) > 0) { ?>
			<table class="itinerary-table">
				<thead>
					<tr>
						<th style="width: 50px;">STT</th>
						<th style="width: 200px;">Thời gian</th>
						<th>Hoạt động</th>
						<th style="width: 100px;">Thứ tự</th>
						<th style="width: 100px;">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$cnt = 1;
					foreach($itineraries as $item) { ?>
						<tr data-id="<?php echo $item->ItineraryId; ?>" 
						    data-time="<?php echo htmlspecialchars($item->TimeLabel, ENT_QUOTES); ?>" 
						    data-activity="<?php echo htmlspecialchars($item->Activity, ENT_QUOTES); ?>" 
						    data-sort="<?php echo $item->SortOrder; ?>">
							<td><?php echo $cnt++; ?></td>
							<td><?php echo htmlentities($item->TimeLabel); ?></td>
							<td><?php echo htmlentities($item->Activity); ?></td>
							<td><?php echo $item->SortOrder; ?></td>
							<td>
								<div class="action-buttons">
									<button type="button" class="btn btn-primary btn-small btn-edit" onclick="event.stopPropagation();">Sửa</button>
									<a href="?pid=<?php echo $pid; ?>&del=<?php echo $item->ItineraryId; ?>" 
									   class="btn btn-danger btn-small" 
									   onclick="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p style="text-align: center; padding: 2rem; color: var(--muted);">Chưa có lộ trình nào. Hãy thêm lộ trình bên dưới.</p>
		<?php } ?>
		
		<!-- Add/Edit Form -->
		<div class="form-section">
			<h2 style="margin-bottom: 1.5rem;" id="formTitle">Thêm lộ trình mới</h2>
			<form method="post" id="itineraryForm">
				<input type="hidden" name="id" id="itemId" value="">
				<input type="hidden" name="sortOrder" id="sortOrder" value="0">
				
				<div class="form-group">
					<label for="timeLabel">Thời gian *</label>
					<input type="text" name="timeLabel" id="timeLabel" required 
					       placeholder="VD: Ngày 1 - Sáng, 08:00 - 10:00">
				</div>
				
				<div class="form-group">
					<label for="activity">Hoạt động *</label>
					<textarea name="activity" id="activity" required 
					          placeholder="Mô tả chi tiết hoạt động trong thời gian này..."></textarea>
				</div>
				
				<div class="form-actions">
					<button type="submit" name="add" id="btnAdd" class="btn">Thêm lộ trình</button>
					<button type="submit" name="update" id="btnUpdate" class="btn" style="display: none; background: var(--accent);">Cập nhật</button>
					<button type="button" onclick="resetForm()" class="btn btn-ghost">Hủy / Làm mới</button>
				</div>
			</form>
		</div>
	</div>
	
	<script>
		// Gan su kien click cho nut sua
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.btn-edit').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const row = this.closest('tr');
					const id = row.dataset.id;
					const timeLabel = row.dataset.time;
					const activity = row.dataset.activity;
					const sortOrder = row.dataset.sort;
					
					editItem(id, timeLabel, activity, sortOrder, row);
				});
			});
		});
		
		function editItem(id, timeLabel, activity, sortOrder, row) {
			document.getElementById('formTitle').textContent = 'Chỉnh sửa lộ trình';
			document.getElementById('itemId').value = id;
			document.getElementById('timeLabel').value = timeLabel;
			document.getElementById('activity').value = activity;
			document.getElementById('sortOrder').value = sortOrder;
			document.getElementById('btnAdd').style.display = 'none';
			document.getElementById('btnUpdate').style.display = 'inline-block';
			
			// To dam dong duoc chon
			document.querySelectorAll('.itinerary-table tr').forEach(tr => tr.classList.remove('selected'));
			if (row) row.classList.add('selected');
			
			// Cuon den form
			document.getElementById('itineraryForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
		
		
		function resetForm() {
			document.getElementById('formTitle').textContent = 'Thêm lộ trình mới';
			document.getElementById('itemId').value = '';
			document.getElementById('timeLabel').value = '';
			document.getElementById('activity').value = '';
			document.getElementById('sortOrder').value = '0';
			document.getElementById('btnAdd').style.display = 'inline-block';
			document.getElementById('btnUpdate').style.display = 'none';
			
			// Bo to dam
			document.querySelectorAll('.itinerary-table tr').forEach(tr => tr.classList.remove('selected'));
		}
	</script>
</body>
</html>
<?php } ?>
