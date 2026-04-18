<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Trang chủ</title>
	<meta name="description" content="Đặt tour du lịch nhanh chóng, quản lý lịch trình và nhận hỗ trợ 24/7 cùng GoTravel.">

	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/enhanced-forms.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/custom-dropdown.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/theme-colors.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pagination.css?v=2.0">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main>
	<section class="hero" style="background-image: url('<?php echo BASE_URL; ?>admin/images/nentour.jpg');">
		<div class="container hero__grid">
			<div class="hero__content">
				<p style="color: #e5e7eb;">GoTravel</p>
				<h1 style="color: #ea2cc7ff;">Đặt tour du lịch dễ dàng chỉ trong vài phút</h1>
				<p style="color: #333; font-size: 18px;">Hệ thống gọn nhẹ giúp bạn khám phá tour phù hợp, quản lý lịch sử đặt và nhận hỗ trợ tức thời. Thiết kế hướng tới trải nghiệm rõ ràng, tối giản.</p>
				<div class="hero__cta">
					<a class="btn" href="<?php echo BASE_URL; ?>package">Khám phá gói tour</a>
					<?php if (empty($_SESSION["login"])): ?>
						<a class="btn btn-ghost" href="#" data-modal-target="signup-modal">Đăng ký ngay</a>
					<?php else: ?>
						<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>tour/history">Lịch sử tour</a>
					<?php endif; ?>
				</div>
			</div>
			<form class="hero__card" action="<?php echo BASE_URL; ?>package" method="get">
				<p style="letter-spacing: .3rem; color:black; ">Tìm tour</p>
				<h3 style="margin-top:0; color:black; font-size:25px;">Bắt đầu với nhu cầu của bạn</h3>
				<div class="form-group">
					<label for="keyword" style="color:black;">Từ khóa</label>
					<input type="text" id="keyword" name="keyword" placeholder="Nhập từ khóa: Tên tour, địa điểm (VD: Đà Nẵng, Sa Pa, Hạ Long)">
					<small class="helper-text" style="display: block; margin-top: 0.5rem; color: var(--muted); font-size: 0.85rem;">Ví dụ: Tên tour, địa điểm bạn muốn khám phá</small>
				</div>
				<div class="form-group">
					<label for="location">Địa điểm</label>
					<select name="location" id="location">
						<option value="">-- Chọn địa điểm bạn muốn khám phá --</option>
						<?php foreach ($data["locations"] as $loc): ?>
							<option value="<?php echo htmlentities(
           $loc->PackageLocation,
       ); ?>"><?php echo htmlentities(Helper::vi($loc->PackageLocation)); ?></option>
						<?php endforeach; ?>
					</select>
					<small class="helper-text" style="display: block; margin-top: 0.5rem; color: var(--muted); font-size: 0.85rem;">Chọn địa điểm cụ thể hoặc để trống để xem tất cả</small>
				</div>
				<div class="form-group">
					<label for="price">Ngân sách</label>
					<select name="price" id="price">
						<option value="">-- Chọn mức ngân sách phù hợp --</option>
						<option value="under-200">Dưới 4.800.000 đ</option>
						<option value="200-500">4.800.000 đ - 12.000.000 đ</option>
						<option value="over-500">Trên 12.000.000 đ</option>
					</select>
					<small class="helper-text" style="display: block; margin-top: 0.5rem; color: var(--muted); font-size: 0.85rem;">Chọn khoảng giá phù hợp với ngân sách của bạn</small>
				</div>
				<button class="btn w-100" type="submit">Xem tour phù hợp</button>
			</form>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section__header">
				<h2>GoTravel có gì khác biệt?</h2>
				<p class="link-muted">Tập trung vào trải nghiệm tối giản và thông tin rõ ràng.</p>
			</div>
			<div class="features-grid">
				<article class="feature-card">
					<h4>Quy trình 3 bước</h4>
					<p>Chọn tour, chọn ngày, xác nhận. Mọi biểu mẫu đều ngắn gọn và dễ đọc.</p>
				</article>
				<article class="feature-card">
					<h4>Quản lý lịch sử</h4>
					<p>Theo dõi mọi đặt chỗ, trạng thái và yêu cầu hỗ trợ tại một nơi duy nhất.</p>
				</article>
				<article class="feature-card">
					<h4>Hỗ trợ 24/7</h4>
					<p>Đội ngũ phản hồi nhanh qua biểu mẫu hỗ trợ tích hợp ngay trong giao diện.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section__header">
				<h2>Tour nổi bật</h2>
				<a href="<?php echo BASE_URL; ?>package">Xem tất cả</a>
			</div>
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
				
				<!-- Pagination -->
				<?php if ($data['totalPages'] > 1): ?>
				<div class="pagination">
					<?php if ($data['currentPage'] > 1): ?>
						<a href="?page=<?php echo $data['currentPage'] - 1; ?>" class="pagination-btn">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M15 18l-6-6 6-6"></path>
							</svg>
						</a>
					<?php else: ?>
						<span class="pagination-btn disabled">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M15 18l-6-6 6-6"></path>
							</svg>
						</span>
					<?php endif; ?>
					
					<span class="pagination-info">
						Trang <?php echo $data['currentPage']; ?> / <?php echo $data['totalPages']; ?>
					</span>
					
					<?php if ($data['currentPage'] < $data['totalPages']): ?>
						<a href="?page=<?php echo $data['currentPage'] + 1; ?>" class="pagination-btn">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M9 18l6-6-6-6"></path>
							</svg>
						</a>
					<?php else: ?>
						<span class="pagination-btn disabled">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M9 18l6-6-6-6"></path>
							</svg>
						</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="empty-state">Chưa có tour nào. Hãy quay lại sau nhé!</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section">
		<div class="container stats-grid">
			<div class="stat-card">
				<h3>1.200+</h3>
				<p>Khách hàng đặt tour mỗi tháng</p>
			</div>
			<div class="stat-card">
				<h3>98%</h3>
				<p>Tỷ lệ phản hồi yêu cầu trong 2 giờ</p>
			</div>
			<div class="stat-card">
				<h3>4.8/5</h3>
				<p>Mức độ hài lòng trên toàn hệ thống</p>
			</div>
		</div>
	</section>

	<section class="section cta">
		<div class="container cta__content">
			<div>
				<h2>Sẵn sàng lên đường?</h2>
				<p>Hãy gửi yêu cầu hoặc liên hệ đội hỗ trợ để được tư vấn hành trình phù hợp nhất.</p>
			</div>
			<?php if (empty($_SESSION["login"])): ?>
				<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>enquiry">Gửi hỏi đáp</a>
			<?php else: ?>
				<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>issue">Gửi yêu cầu</a>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<script src="<?php echo BASE_URL; ?>public/js/custom-dropdown.js?v=1.0"></script>
</body>
</html>
