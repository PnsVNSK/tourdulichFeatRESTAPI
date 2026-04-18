<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Chi tiết gói tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/itinerary-carousel.css?v=14.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/wishlist-button.css?v=1.0">
</head>

<body>
	<?php include ROOT . "/includes/header.php"; ?>
	<main class="page">
		<div class="container">
			<section class="page-head">
				<h1>Chi tiết gói tour</h1>
				<p>Thông tin rõ ràng giúp bạn quyết định dễ dàng.</p>
			</section>
			<?php if (
				$data["error"]
			) { ?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities(
															$data["error"],
														); ?></div><?php } elseif (
				$data["msg"]
			) { ?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities(
																	$data["msg"],
																); ?></div><?php } ?>

			<?php if ($data["package"]): ?>
				<div class="grid-two">
					<section class="card">
						<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities(
																					$data["package"]->PackageImage,
																				); ?>"
							onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>admin/packageimages/tour01.jpg';"
							alt="<?php echo htmlentities($data["package"]->PackageName); ?>">
						<h2><?php echo htmlentities($data["package"]->PackageName); ?></h2>
						<p class="badge">#PKG-<?php echo htmlentities(
													$data["package"]->PackageId,
												); ?></p>

						<!-- Wishlist Button - Đẹp mắt và rõ ràng -->
						<button class="wishlist-btn" data-package-id="<?php echo htmlentities($data["package"]->PackageId); ?>" id="wishlistBtn">
							<i class="fas fa-heart"></i>
							<span class="wishlist-text">Lưu tour yêu thích</span>
						</button>
						<ul class="summary-list">
							<li><span>Loại gói</span><strong><?php echo htmlentities(
																	Helper::vi($data["package"]->PackageType),
																); ?></strong></li>
							<li><span>Địa điểm</span><strong><?php echo htmlentities(
																	Helper::vi($data["package"]->PackageLocation),
																); ?></strong></li>
							<li><span>Thời gian tour</span><strong><?php echo htmlentities(
																		Helper::vi($data["package"]->TourDuration),
																	); ?></strong></li>
							<li><span>Giá</span><strong><?php echo Controller::formatVND($data["package"]->PackagePrice); ?></strong></li>
						</ul>
						<p><?php echo htmlentities($data["package"]->PackageFetures); ?></p>
					</section>
					<section class="card">
						<h3>Đặt tour</h3>
						<form name="book" method="post" class="form-stack" action="<?php echo BASE_URL; ?>package/book/<?php echo htmlentities(
																															$data["package"]->PackageId,
																														); ?>">
							<div class="form-group">
								<label for="departuredate">Ngày khởi hành</label>
								<input type="date" id="departuredate" name="departuredate" required>
							</div>
							<div class="form-group">
								<label for="numberofpeople">Số người</label>
								<input type="number" id="numberofpeople" name="numberofpeople" min="1" max="100" value="1" required>
							</div>
							<div class="form-group">
								<label for="comment">Ghi chú</label>
								<textarea id="comment" name="comment" placeholder="Nêu thêm yêu cầu cụ thể"></textarea>
							</div>
							<?php if (!empty($_SESSION["login"])): ?>
								<button type="submit" name="submit2" class="btn">Đặt tour</button>
							<?php else: ?>
								<a class="btn btn-ghost" href="#" data-modal-target="signin-modal">Đăng nhập để đặt tour</a>
							<?php endif; ?>
						</form>
					</section>
				</div>

				<!-- Itinerary Section - Simple Layout -->
				<?php if (isset($data["itineraries"]) && count($data["itineraries"]) > 0): ?>
					<section class="card">
						<h3>Lộ trình chi tiết</h3>
						<div class="itinerary-list">
							<?php foreach ($data["itineraries"] as $item): ?>
								<div class="itinerary-item">
									<strong><?php echo htmlentities($item->TimeLabel); ?>:</strong>
									<span><?php echo htmlentities($item->Activity); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<section class="card">
					<h3>Thông tin chi tiết</h3>
					<div class="package-details"><?php echo nl2br(htmlspecialchars($data["package"]->PackageDetails, ENT_QUOTES, 'UTF-8')); ?></div>
				</section>
			<?php else: ?>
				<div class="empty-state">Không tìm thấy gói tour.</div>
			<?php endif; ?>
		</div>
	</main>
	<?php include ROOT . "/includes/footer.php"; ?>
	<?php include ROOT . "/includes/signup.php"; ?>
	<?php include ROOT . "/includes/signin.php"; ?>
	<?php include ROOT . "/includes/write-us.php"; ?>
	<script>
		// Pass BASE_URL from PHP to JavaScript
		window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
	</script>
	<script src="<?php echo BASE_URL; ?>public/js/wishlist-button.js?v=1.1"></script>
</body>

</html>