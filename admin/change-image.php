<?php
session_start();
error_reporting(0);
include('includes/config.php');
require_once dirname(__DIR__) . '/core/Helper.php';
Helper::requireAdminLogin();

$imgid=intval($_GET['imgid']);
if(isset($_POST['submit']))
{
$pimage = '';
$error = '';

	// Kiem tra file upload bang lop ho tro
	$validation = Helper::validateImage($_FILES["packageimage"]);
	if (!$validation['valid']) {
		$error = $validation['error'];
	} else {
		// Lam sach ten file
		$pimage = Helper::sanitizeFilename($_FILES["packageimage"]["name"]);
		$uploadPath = "packageimages/" . $pimage;
		
		if (move_uploaded_file($_FILES["packageimage"]["tmp_name"], $uploadPath)) {
			$sql="update tbltourpackages set PackageImage=:pimage where PackageId=:imgid";
			$query = $dbh->prepare($sql);
			$query->bindParam(':imgid',$imgid,PDO::PARAM_INT);
			$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
			$query->execute();
			$msg="Cáº­p nháº­t hÃ¬nh áº£nh gÃ³i tour thÃ nh cÃ´ng";
		} else {
			$error = "KhÃ´ng thá»ƒ táº£i lÃªn file. Vui lÃ²ng thá»­ láº¡i";
		}
	}
}

	$pageTitle = "GoTravel Admin | Cáº­p nháº­t hÃ¬nh áº£nh";
	$currentPage = 'manage-packages';
	$sql = "SELECT PackageImage from tbltourpackages where PackageId=:imgid";
	$query = $dbh -> prepare($sql);
	$query -> bindParam(':imgid', $imgid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1>Cáº­p nháº­t hÃ¬nh áº£nh</h1>
				<p>Thay Ä‘á»•i hÃ¬nh áº£nh Ä‘áº¡i diá»‡n cho gÃ³i tour.</p>
			</div>
		</section>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
		<section class="card">
			<?php if($package): ?>
			<form method="post" enctype="multipart/form-data" class="form-stack">
				<div class="form-group">
					<label>HÃ¬nh áº£nh hiá»‡n táº¡i</label>
					<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage);?>" alt="áº¢nh gÃ³i tour" style="width:200px;border-radius:0.75rem;">
				</div>
				<div class="form-group">
					<label for="packageimage">HÃ¬nh áº£nh má»›i</label>
					<input type="file" name="packageimage" id="packageimage" accept="image/*" required>
				</div>
				<button type="submit" name="submit" class="btn btn-primary">Cáº­p nháº­t</button>
			</form>
			<?php else: ?>
			<div class="empty-state">KhÃ´ng tÃ¬m tháº¥y gÃ³i tour.</div>
			<?php endif; ?>
		</section>
	<?php include('includes/layout-end.php'); ?>
