<?php
session_start();
error_reporting(0);
include('includes/config.php');
require_once dirname(__DIR__) . '/core/Helper.php';
Helper::requireAdminLogin();

$packageCreated = false;
$newPackageId = null;

// Xu ly them lich trinh (only if package exists)
if(isset($_POST['addItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
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
	
	$itineraryMsg = "ÄÃ£ thÃªm lá»™ trÃ¬nh thÃ nh cÃ´ng";
}

// Xu ly sua lich trinh
if(isset($_POST['updateItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$id = intval($_POST['itineraryId']);
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
	
	$itineraryMsg = "ÄÃ£ cáº­p nháº­t lá»™ trÃ¬nh thÃ nh cÃ´ng";
}

// Xu ly xoa lich trinh
if(isset($_GET['delItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$id = intval($_GET['delItinerary']);
	$sql = "DELETE FROM tblitinerary WHERE ItineraryId = :id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':id', $id, PDO::PARAM_INT);
	$query->execute();
	
	$itineraryMsg = "ÄÃ£ xÃ³a lá»™ trÃ¬nh thÃ nh cÃ´ng";
	header('Location: ' . BASE_URL . 'admin/create-package.php?pid=' . $pid);
	exit;
}

// Xu ly tao goi tour
if(isset($_POST['submit']))
{
$pname = trim($_POST['packagename'] ?? '');
$ptype = trim($_POST['packagetype'] ?? '');	
$plocation = trim($_POST['packagelocation'] ?? '');
$tourduration = trim($_POST['tourduration'] ?? '');
$pprice = intval($_POST['packageprice'] ?? 0);	
$pfeatures = trim($_POST['packagefeatures'] ?? '');
$pdetails = trim($_POST['packagedetails'] ?? '');	
$pimage = '';

// Lay du lieu lich trinh tu truong an
$itineraryData = isset($_POST['itineraryData']) ? $_POST['itineraryData'] : '';

// Kiem tra du lieu dau vao
if (empty($pname) || empty($ptype) || empty($plocation) || empty($tourduration) || $pprice <= 0 || empty($pfeatures) || empty($pdetails)) {
	$error = "Vui lÃ²ng Ä‘iá»n Ä‘áº§y Ä‘á»§ thÃ´ng tin";
} elseif (!isset($_FILES["packageimage"]) || $_FILES["packageimage"]["error"] !== UPLOAD_ERR_OK) {
	$error = "Vui lÃ²ng chá»n hÃ¬nh áº£nh";
} else {
	// Kiem tra file upload bang lop ho tro
	$validation = Helper::validateImage($_FILES["packageimage"]);
	if (!$validation['valid']) {
		$error = $validation['error'];
	} else {
		// Lam sach ten file
		$pimage = Helper::sanitizeFilename($_FILES["packageimage"]["name"]);
		$uploadPath = "packageimages/" . $pimage;
		
		if (move_uploaded_file($_FILES["packageimage"]["tmp_name"], $uploadPath)) {
			// Upload file thanh cong, tiep tuc luu vao co so du lieu
		} else {
			$error = "KhÃ´ng thá»ƒ táº£i lÃªn file. Vui lÃ²ng thá»­ láº¡i";
		}
	}
}

if (!isset($error)) {
	try {
		// Bat dau transaction
		$dbh->beginTransaction();
		
		// Them goi tour
		$sql="INSERT INTO tbltourpackages(PackageName,PackageType,PackageLocation,TourDuration,PackagePrice,PackageFetures,PackageDetails,PackageImage) VALUES(:pname,:ptype,:plocation,:tourduration,:pprice,:pfeatures,:pdetails,:pimage)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':pname',$pname,PDO::PARAM_STR);
		$query->bindParam(':ptype',$ptype,PDO::PARAM_STR);
		$query->bindParam(':plocation',$plocation,PDO::PARAM_STR);
		$query->bindParam(':tourduration',$tourduration,PDO::PARAM_STR);
		$query->bindParam(':pprice',$pprice,PDO::PARAM_INT);
		$query->bindParam(':pfeatures',$pfeatures,PDO::PARAM_STR);
		$query->bindParam(':pdetails',$pdetails,PDO::PARAM_STR);
		$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId();
		
		// Them lich trinh neu co
		if($lastInsertId && !empty($itineraryData)) {
			$itineraries = json_decode($itineraryData, true);
			if(is_array($itineraries) && count($itineraries) > 0) {
				$sqlItinerary = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) VALUES (:pid, :timeLabel, :activity, :sortOrder)";
				$queryItinerary = $dbh->prepare($sqlItinerary);
				
				foreach($itineraries as $item) {
					$queryItinerary->bindParam(':pid', $lastInsertId, PDO::PARAM_INT);
					$queryItinerary->bindParam(':timeLabel', $item['timeLabel'], PDO::PARAM_STR);
					$queryItinerary->bindParam(':activity', $item['activity'], PDO::PARAM_STR);
					$queryItinerary->bindParam(':sortOrder', $item['sortOrder'], PDO::PARAM_INT);
					$queryItinerary->execute();
				}
			}
		}
		
		// Xac nhan transaction
		$dbh->commit();
		
		if($lastInsertId) {
			$packageCreated = true;
			$newPackageId = $lastInsertId;
			$itineraryCount = is_array(json_decode($itineraryData, true)) ? count(json_decode($itineraryData, true)) : 0;
			$msg = "Táº¡o gÃ³i tour thÃ nh cÃ´ng! " . ($itineraryCount > 0 ? "ÄÃ£ thÃªm $itineraryCount lá»™ trÃ¬nh." : "Báº¡n cÃ³ thá»ƒ thÃªm lá»™ trÃ¬nh bÃªn dÆ°á»›i.");
			// Chuyen ve cung trang kem package id de quan ly lich trinh
			header('Location: ' . BASE_URL . 'admin/create-package.php?pid=' . $lastInsertId . '&created=1');
			exit;
		} else {
			$error="CÃ³ lá»—i xáº£y ra. Vui lÃ²ng thá»­ láº¡i";
		}
	} catch(Exception $e) {
		// Hoan tac transaction khi co loi
		$dbh->rollBack();
		$error = "CÃ³ lá»—i xáº£y ra: " . $e->getMessage();
	}
}
}

// Kiem tra co dang xem goi tour da tao hay khong
if(isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$packageCreated = true;
	$newPackageId = $pid;
	
	// Lay thong tin goi tour
	$sql = "SELECT * FROM tbltourpackages WHERE PackageId = :pid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	
	// Lay danh sach lich trinh
	$sql = "SELECT * FROM tblitinerary WHERE PackageId = :pid ORDER BY SortOrder ASC, ItineraryId ASC";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$itineraries = $query->fetchAll(PDO::FETCH_OBJ);
	
	if(isset($_GET['created'])) {
		$msg = "Táº¡o gÃ³i tour thÃ nh cÃ´ng! BÃ¢y giá» báº¡n cÃ³ thá»ƒ thÃªm lá»™ trÃ¬nh chi tiáº¿t bÃªn dÆ°á»›i.";
	}
}

	$pageTitle = "GoTravel Admin | Táº¡o gÃ³i tour";
	$currentPage = 'create-package';
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1><?php echo $packageCreated ? 'HoÃ n thiá»‡n gÃ³i tour' : 'Táº¡o gÃ³i tour'; ?></h1>
				<p><?php echo $packageCreated ? 'GÃ³i tour Ä‘Ã£ Ä‘Æ°á»£c táº¡o. ThÃªm lá»™ trÃ¬nh chi tiáº¿t Ä‘á»ƒ hoÃ n thiá»‡n.' : 'ThÃªm nhanh gÃ³i tour má»›i vá»›i Ä‘áº§y Ä‘á»§ thÃ´ng tin vÃ  hÃ¬nh áº£nh.'; ?></p>
			</div>
			<?php if($packageCreated) { ?>
				<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/manage-packages.php">â† Quay láº¡i danh sÃ¡ch</a>
			<?php } ?>
		</section>
		<?php if($error){?><div class="alert error"><?php echo htmlentities($error); ?> </div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg); ?> </div><?php } ?>
		<?php if(isset($itineraryMsg)){?><div class="alert success"><?php echo htmlentities($itineraryMsg); ?></div><?php } ?>
		
		<?php if(!$packageCreated) { ?>
		<!-- Package Creation Form -->
		<section class="card">
			<h3>ThÃ´ng tin gÃ³i tour</h3>
			<form name="package" method="post" enctype="multipart/form-data" class="form-stack" id="packageForm">
				<input type="hidden" name="itineraryData" id="itineraryDataInput" value="">
				<div class="form-grid">
					<div class="form-group">
						<label for="packagename">TÃªn gÃ³i</label>
						<input type="text" name="packagename" id="packagename" required>
					</div>
					<div class="form-group">
						<label for="packagetype">Loáº¡i gÃ³i</label>
						<input type="text" name="packagetype" id="packagetype" placeholder="Gia Ä‘Ã¬nh / Cáº·p Ä‘Ã´i / ..." required>
					</div>
					<div class="form-group">
						<label for="packagelocation">Äá»‹a Ä‘iá»ƒm</label>
						<input type="text" name="packagelocation" id="packagelocation" required>
					</div>
					<div class="form-group">					<label for="tourduration">Thá»i gian tour</label>
					<input type="text" name="tourduration" id="tourduration" placeholder="VD: 2 NgÃ y 1 ÄÃªm / 5 NgÃ y 4 ÄÃªm / Trong ngÃ y" required>
				</div>
				<div class="form-group">						<label for="packageprice">GiÃ¡ gÃ³i (VNÄ)</label>
						<input type="number" min="0" step="1000" name="packageprice" id="packageprice" required>
						<small style="color: var(--muted); font-size: 0.85rem;">Nháº­p giÃ¡ báº±ng VNÄ. VÃ­ dá»¥: 4.800.000</small>
					</div>
				</div>
				<div class="form-group">
					<label for="packagefeatures">Äiá»ƒm ná»•i báº­t</label>
					<input type="text" name="packagefeatures" id="packagefeatures" placeholder="VÃ­ dá»¥: ÄÆ°a Ä‘Ã³n sÃ¢n bay miá»…n phÃ­" required>
				</div>
				<div class="form-group">
					<label for="packagedetails">Chi tiáº¿t gÃ³i</label>
					<textarea name="packagedetails" id="packagedetails" placeholder="Nháº­p mÃ´ táº£ chi tiáº¿t" required></textarea>
				</div>
				<div class="form-group">
					<label for="packageimage">HÃ¬nh áº£nh gÃ³i</label>
					<input type="file" name="packageimage" id="packageimage" accept="image/*" required>
				</div>
				<div>
					<button type="submit" name="submit" class="btn btn-primary">Táº¡o gÃ³i tour</button>
					<button type="reset" class="btn btn-ghost">LÃ m má»›i</button>
				</div>
			</form>
		</section>
		
		<!-- Itinerary Management Section (Pre-Creation) -->
		<section class="card" style="margin-top: 2rem;">
			<h3>Lá»™ trÃ¬nh chi tiáº¿t (TÃ¹y chá»n)</h3>
			<p style="color: var(--muted); margin-bottom: 1.5rem;">ThÃªm cÃ¡c Ä‘iá»ƒm trong lá»™ trÃ¬nh tour. Báº¡n cÃ³ thá»ƒ thÃªm sau khi táº¡o gÃ³i tour.</p>
			
			<div id="itineraryPreviewTable" style="display: none; overflow-x: auto; margin-bottom: 2rem;">
				<table class="table">
					<thead>
						<tr>
							<th style="width: 50px;">STT</th>
							<th style="width: 200px;">Thá»i gian</th>
							<th>Hoáº¡t Ä‘á»™ng</th>
							<th style="width: 80px;">Thá»© tá»±</th>
							<th style="width: 150px;">Thao tÃ¡c</th>
						</tr>
					</thead>
					<tbody id="itineraryPreviewBody">
					</tbody>
				</table>
			</div>
			
			<p id="emptyItineraryMsg" style="text-align: center; padding: 2rem; color: var(--muted);">ChÆ°a cÃ³ lá»™ trÃ¬nh nÃ o. HÃ£y thÃªm lá»™ trÃ¬nh bÃªn dÆ°á»›i.</p>
			
			<!-- Add Itinerary Form -->
			<div style="background: var(--bg); padding: 1.5rem; border-radius: 8px;">
				<h4 style="margin-bottom: 1rem;">ThÃªm lá»™ trÃ¬nh má»›i</h4>
				<div class="form-stack">
					<div class="form-grid">
						<div class="form-group">
							<label for="newTimeLabel">Thá»i gian *</label>
							<input type="text" id="newTimeLabel" placeholder="VD: NgÃ y 1 - SÃ¡ng, 08:00 - 10:00">
						</div>
					</div>
					
					<div class="form-group">
						<label for="newActivity">Hoáº¡t Ä‘á»™ng *</label>
						<textarea id="newActivity" placeholder="MÃ´ táº£ chi tiáº¿t hoáº¡t Ä‘á»™ng trong thá»i gian nÃ y..."></textarea>
					</div>
					
					<div style="display: flex; gap: 1rem;">
						<button type="button" onclick="addItineraryItem()" class="btn">ThÃªm lá»™ trÃ¬nh</button>
						<button type="button" onclick="clearItineraryForm()" class="btn btn-ghost">LÃ m má»›i</button>
					</div>
				</div>
			</div>
		</section>
		<?php } else { ?>
		
		<!-- Package Created - Show Summary -->
		<section class="card">
			<h3>âœ… GÃ³i tour Ä‘Ã£ táº¡o</h3>
			<div class="form-grid">
				<div><strong>TÃªn gÃ³i:</strong> <?php echo htmlentities($package->PackageName); ?></div>
				<div><strong>Loáº¡i:</strong> <?php echo htmlentities($package->PackageType); ?></div>
				<div><strong>Äá»‹a Ä‘iá»ƒm:</strong> <?php echo htmlentities($package->PackageLocation); ?></div>
				<div><strong>Thá»i gian:</strong> <?php echo htmlentities($package->TourDuration); ?></div>
				<div><strong>GiÃ¡:</strong> <?php echo number_format($package->PackagePrice, 0, ',', '.') . ' Ä‘'; ?></div>
			</div>
			<div style="margin-top: 1rem;">
				<a href="<?php echo BASE_URL; ?>admin/update-package.php?pid=<?php echo $newPackageId; ?>" class="btn btn-ghost">Chá»‰nh sá»­a thÃ´ng tin gÃ³i</a>
			</div>
		</section>
		
		<!-- Itinerary Management Section -->
		<section class="card" style="margin-top: 2rem;">
			<h3>Quáº£n lÃ½ lá»™ trÃ¬nh chi tiáº¿t</h3>
			<p style="color: var(--muted); margin-bottom: 1.5rem;">ThÃªm cÃ¡c Ä‘iá»ƒm trong lá»™ trÃ¬nh tour cá»§a báº¡n.</p>
			
			<?php if(count($itineraries) > 0) { ?>
				<div style="overflow-x: auto; margin-bottom: 2rem;">
					<table class="table">
						<thead>
							<tr>
								<th style="width: 50px;">STT</th>
								<th style="width: 200px;">Thá»i gian</th>
								<th>Hoáº¡t Ä‘á»™ng</th>
								<th style="width: 80px;">Thá»© tá»±</th>
								<th style="width: 150px;">Thao tÃ¡c</th>
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
										<div style="display: flex; gap: 0.5rem;">
											<button type="button" class="btn btn-primary btn-small btn-edit-itinerary">Sá»­a</button>
											<a href="?pid=<?php echo $newPackageId; ?>&delItinerary=<?php echo $item->ItineraryId; ?>" 
											   class="btn btn-danger btn-small" 
											   onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a?');">XÃ³a</a>
										</div>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } else { ?>
				<p style="text-align: center; padding: 2rem; color: var(--muted);">ChÆ°a cÃ³ lá»™ trÃ¬nh nÃ o. HÃ£y thÃªm lá»™ trÃ¬nh bÃªn dÆ°á»›i.</p>
			<?php } ?>
			
			<!-- Add/Edit Itinerary Form -->
			<div style="background: var(--bg); padding: 1.5rem; border-radius: 8px;">
				<h4 style="margin-bottom: 1rem;" id="itineraryFormTitle">ThÃªm lá»™ trÃ¬nh má»›i</h4>
				<form method="post" id="itineraryForm" class="form-stack">
					<input type="hidden" name="itineraryId" id="itineraryId" value="">
					<input type="hidden" name="sortOrder" id="sortOrder" value="0">
					
					<div class="form-grid">
						<div class="form-group">
							<label for="timeLabel">Thá»i gian *</label>
							<input type="text" name="timeLabel" id="timeLabel" required 
							       placeholder="VD: NgÃ y 1 - SÃ¡ng, 08:00 - 10:00">
						</div>
					</div>
					
					<div class="form-group">
						<label for="activity">Hoáº¡t Ä‘á»™ng *</label>
						<textarea name="activity" id="activity" required 
						          placeholder="MÃ´ táº£ chi tiáº¿t hoáº¡t Ä‘á»™ng trong thá»i gian nÃ y..."></textarea>
					</div>
					
					<div style="display: flex; gap: 1rem;">
						<button type="submit" name="addItinerary" id="btnAddItinerary" class="btn">ThÃªm lá»™ trÃ¬nh</button>
						<button type="submit" name="updateItinerary" id="btnUpdateItinerary" class="btn" style="display: none; background: var(--accent);">Cáº­p nháº­t</button>
						<button type="button" onclick="resetItineraryForm()" class="btn btn-ghost">Há»§y / LÃ m má»›i</button>
						<a href="<?php echo BASE_URL; ?>admin/manage-packages.php" class="btn btn-ghost">HoÃ n táº¥t & Quay láº¡i</a>
					</div>
				</form>
			</div>
		</section>
		
		<script>
		// Quan ly lich trinh
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.btn-edit-itinerary').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const row = this.closest('tr');
					const id = row.dataset.id;
					const timeLabel = row.dataset.time;
					const activity = row.dataset.activity;
					const sortOrder = row.dataset.sort;
					
					editItinerary(id, timeLabel, activity, sortOrder);
				});
			});
		});
		
		function editItinerary(id, timeLabel, activity, sortOrder) {
			document.getElementById('itineraryFormTitle').textContent = 'Chá»‰nh sá»­a lá»™ trÃ¬nh';
			document.getElementById('itineraryId').value = id;
			document.getElementById('timeLabel').value = timeLabel;
			document.getElementById('activity').value = activity;
			document.getElementById('sortOrder').value = sortOrder;
			document.getElementById('btnAddItinerary').style.display = 'none';
			document.getElementById('btnUpdateItinerary').style.display = 'inline-block';
			
			// Cuon den form
			document.getElementById('itineraryForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
		
		function resetItineraryForm() {
			document.getElementById('itineraryFormTitle').textContent = 'ThÃªm lá»™ trÃ¬nh má»›i';
			document.getElementById('itineraryId').value = '';
			document.getElementById('timeLabel').value = '';
			document.getElementById('activity').value = '';
			document.getElementById('sortOrder').value = '0';
			document.getElementById('btnAddItinerary').style.display = 'inline-block';
			document.getElementById('btnUpdateItinerary').style.display = 'none';
		}
		
		// Quan ly lich trinh truoc khi tao goi
		let tempItineraries = [];
		
		function addItineraryItem() {
			const timeLabel = document.getElementById('newTimeLabel').value.trim();
			const activity = document.getElementById('newActivity').value.trim();
			
			if(!timeLabel || !activity) {
				alert('Vui lÃ²ng Ä‘iá»n Ä‘áº§y Ä‘á»§ thÃ´ng tin lá»™ trÃ¬nh');
				return;
			}
			
			const newItem = {
				timeLabel: timeLabel,
				activity: activity,
				sortOrder: tempItineraries.length + 1
			};
			
			tempItineraries.push(newItem);
			updateItineraryPreview();
			clearItineraryForm();
			
			// Cap nhat truong an
			document.getElementById('itineraryDataInput').value = JSON.stringify(tempItineraries);
		}
		
		function removeItineraryItem(index) {
			if(confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a lá»™ trÃ¬nh nÃ y?')) {
				tempItineraries.splice(index, 1);
				// Cap nhat thu tu sap xep
				tempItineraries.forEach((item, idx) => {
					item.sortOrder = idx + 1;
				});
				updateItineraryPreview();
				document.getElementById('itineraryDataInput').value = JSON.stringify(tempItineraries);
			}
		}
		
		function updateItineraryPreview() {
			const tbody = document.getElementById('itineraryPreviewBody');
			const table = document.getElementById('itineraryPreviewTable');
			const emptyMsg = document.getElementById('emptyItineraryMsg');
			
			if(tempItineraries.length === 0) {
				table.style.display = 'none';
				emptyMsg.style.display = 'block';
				return;
			}
			
			table.style.display = 'block';
			emptyMsg.style.display = 'none';
			
			tbody.innerHTML = '';
			tempItineraries.forEach((item, index) => {
				const row = tbody.insertRow();
				row.innerHTML = `
					<td>${index + 1}</td>
					<td>${escapeHtml(item.timeLabel)}</td>
					<td>${escapeHtml(item.activity)}</td>
					<td>${item.sortOrder}</td>
					<td>
						<button type="button" class="btn btn-danger btn-small" onclick="removeItineraryItem(${index})">XÃ³a</button>
					</td>
				`;
			});
		}
		
		function clearItineraryForm() {
			document.getElementById('newTimeLabel').value = '';
			document.getElementById('newActivity').value = '';
		}
		
		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, m => map[m]);
		}
		
		// Xu ly dat lai form
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('packageForm');
			if(form) {
				form.addEventListener('reset', function() {
					tempItineraries = [];
					updateItineraryPreview();
					document.getElementById('itineraryDataInput').value = '';
				});
			}
		});
		</script>
		<?php } ?>
	<?php include('includes/layout-end.php'); ?>
