<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Danh sách tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=3.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
</head>

<body>
	<?php include ROOT . "/includes/header.php"; ?>
	<main class="page">
		<div class="container">
			<section class="page-head">
				<h1>Gói du lịch</h1>
				<p>Chọn tour phù hợp với ngân sách và lịch trình của bạn.</p>
			</section>

			<section class="card">
				<form class="form-grid" method="get">
					<div class="form-group">
						<label for="keyword">Từ khóa</label>
						<input type="text" id="keyword" name="keyword" value="<?php echo htmlentities(
																					$data["keyword"],
																				); ?>" placeholder="Tên tour, địa điểm">
					</div>
					<div class="form-group">
						<label for="location">Địa điểm</label>
						<select id="location" name="location">
							<option value="">Tất cả</option>
							<?php foreach ($data["locations"] as $loc): ?>
								<option value="<?php echo htmlentities($loc->PackageLocation); ?>" <?php if (
																										$data["locationFilter"] === $loc->PackageLocation
																									) {
																										echo "selected";
																									} ?>><?php echo htmlentities(Helper::vi($loc->PackageLocation)); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="price">Ngân sách</label>
						<select id="price" name="price">
							<option value="" <?php if ($data["priceFilter"] === "") {
													echo "selected";
												} ?>>Bất kỳ</option>
							<option value="under-200" <?php if ($data["priceFilter"] === "under-200") {
															echo "selected";
														} ?>>Dưới 4.800.000 đ</option>
							<option value="200-500" <?php if ($data["priceFilter"] === "200-500") {
														echo "selected";
													} ?>>4.800.000 đ - 12.000.000 đ</option>
							<option value="over-500" <?php if ($data["priceFilter"] === "over-500") {
															echo "selected";
														} ?>>Trên 12.000.000 đ</option>
						</select>
					</div>
					<div class="form-group" style="align-self:flex-end;">
						<button class="btn" type="submit">Áp dụng</button>
					</div>
				</form>
			</section>

			<section class="card" style="margin-top:1.5rem;">
				<?php if (count($data["packages"])): ?>
					<div class="tour-grid">
						<?php foreach ($data["packages"] as $package): ?>
							<div class="tour-card">
								<!-- Tour Type Badge -->
								<div class="badge"><?php echo htmlentities(Helper::vi($package->PackageType)); ?></div>
								<!-- Image -->
								<div class="tilt">
									<div class="img">
										<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage); ?>"
											onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>admin/packageimages/tour01.jpg';"
											alt="<?php echo htmlentities($package->PackageName); ?>">
									</div>
								</div>

								<!-- Info Section -->
								<div class="info">
									<!-- Location -->
									<div class="meta-top">
										<div class="cat">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
												<circle cx="12" cy="10" r="3"></circle>
											</svg>
											<?php echo htmlentities(Helper::vi($package->PackageLocation)); ?>
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
										<span><?php echo htmlentities(Helper::vi($package->TourDuration)); ?></span>
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
					<div class="empty-state">Không tìm thấy tour phù hợp. Hãy thử thay đổi bộ lọc.</div>
				<?php endif; ?>
			</section>
		</div>
	</main>
	<?php include ROOT . "/includes/footer.php"; ?>
	<?php include ROOT . "/includes/signup.php"; ?>
	<?php include ROOT . "/includes/signin.php"; ?>
	<?php include ROOT . "/includes/write-us.php"; ?>

</body>

</html>