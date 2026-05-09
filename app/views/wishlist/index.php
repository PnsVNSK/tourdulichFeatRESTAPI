<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Tour yêu thích</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/enhanced-forms.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/theme-colors.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/wishlist-button.css?v=1.0">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Tour yêu thích của tôi</h1>
			<p>Quản lý các tour mà bạn yêu thích và sẵn sàng đặt.</p>
		</section>

		<?php if ($data["error"]): ?>
		<div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($data["error"]); ?></div>
		<?php elseif ($data["msg"]): ?>
		<div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($data["msg"]); ?></div>
		<?php endif; ?>

		<?php if (count($data["wishlistItems"])): ?>
		<div class="tour-grid">
			<?php foreach ($data["wishlistItems"] as $package): ?>
				<div class="tour-card">
					<!-- Tour Type Badge -->
					<div class="badge"><?php echo htmlentities($package->PackageType); ?></div>
					
					<!-- Image -->
					<div class="tilt">
						<div class="img">
							<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage); ?>" 
							     alt="<?php echo htmlentities($package->PackageName); ?>">
						</div>
					</div>
					
					<!-- Wishlist Heart Button -->
					<button class="wishlist-heart" data-package-id="<?php echo htmlentities($package->PackageId); ?>" title="Xóa khỏi danh sách yêu thích">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
							<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
						</svg>
					</button>
					
					<!-- Info Section -->
					<div class="info">
						<!-- Location -->
						<div class="meta-top">
							<div class="cat">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
									<circle cx="12" cy="10" r="3"></circle>
								</svg>
								<?php echo htmlentities($package->PackageLocation); ?>
							</div>
						</div>
					
						<!-- Tour Name -->
						<h2 class="title"><?php echo htmlentities($package->PackageName); ?></h2>
						
						<!-- Description -->
						<p class="desc"><?php echo htmlentities($package->PackageFetures); ?></p>

						<!-- Duration -->
						<div class="duration-row">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="12" cy="12" r="10"></circle>
								<polyline points="12 6 12 12 16 14"></polyline>
							</svg>
							<span><?php echo htmlentities($package->TourDuration); ?></span>
						</div>

						<!-- Price -->
						<div class="price-row">
							<span class="price-label">Chỉ từ</span>
							<span class="price-value"><?php echo Controller::formatVND($package->PackagePrice); ?></span>
						</div>

						<!-- Button -->
						<a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($package->PackageId); ?>" class="btn-detail">
							<span>Xem chi tiết</span>
							<svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M5 12h14"></path>
								<path d="M12 5l7 7-7 7"></path>
							</svg>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php else: ?>
		<div class="empty-state">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; margin: 0 auto 1rem; color: var(--muted);">
				<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
			</svg>
			<h2>Chưa có tour yêu thích</h2>
			<p>Hãy khám phá những tour tuyệt vời và thêm chúng vào danh sách yêu thích của bạn.</p>
			<a href="<?php echo BASE_URL; ?>package" class="btn">Khám phá tour</a>
		</div>
		<?php endif; ?>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<script>
    // Truyen BASE_URL tu php sang javascript
    window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>public/js/wishlist.js?v=1.1"></script>
</body>
</html>
