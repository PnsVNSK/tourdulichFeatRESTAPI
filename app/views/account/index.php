<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Tài khoản của tôi</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/account.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Tài khoản của tôi</h1>
			<p>Quản lý thông tin cá nhân, lịch sử đặt tour và danh sách yêu thích.</p>
		</section>

		<?php if ($data["error"]) { ?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($data["error"]); ?></div><?php } elseif ($data["msg"]) { ?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($data["msg"]); ?></div><?php } ?>

		<div class="account-container">
			<!-- Tab Navigation -->
			<nav class="account-tabs">
				<button class="account-tab active" data-tab="profile">
					<i class="fas fa-user"></i>
					<span>Thông tin cá nhân</span>
				</button>
				<button class="account-tab" data-tab="bookings">
					<i class="fas fa-history"></i>
					<span>Lịch sử đặt tour</span>
				</button>
				<button class="account-tab" data-tab="security">
					<i class="fas fa-lock"></i>
					<span>Đổi mật khẩu</span>
				</button>
				<button class="account-tab" data-tab="wishlist">
					<i class="fas fa-heart"></i>
					<span>Tour yêu thích</span>
				</button>
			</nav>

			<!-- Tab Content -->
			<div class="account-content">
				<!-- Profile Tab -->
				<div class="tab-pane active" id="profile-tab">
					<div class="card">
						<h3>Thông tin cá nhân</h3>
						<p class="helper-text">Thông tin này sẽ được tự động điền khi bạn đặt tour.</p>
						
						<?php if ($data["user"]): ?>
						<form name="profileForm" method="post" class="form-stack" action="<?php echo BASE_URL; ?>user/updateProfileExtended" enctype="multipart/form-data">
							<div class="form-grid">
								<div class="form-group">
									<label for="name">Họ và tên <span class="required">*</span></label>
									<input type="text" name="name" id="name" value="<?php echo htmlentities($data["user"]->FullName); ?>" required>
								</div>
								<div class="form-group">
									<label for="mobileno">Số điện thoại <span class="required">*</span></label>
									<input type="text" name="mobileno" id="mobileno" maxlength="10" value="<?php echo htmlentities($data["user"]->MobileNumber); ?>" required>
								</div>
							</div>

							<div class="form-group">
								<label>Email</label>
								<input type="email" value="<?php echo htmlentities($data["user"]->EmailId); ?>" disabled>
								<p class="helper-text">Email không thể thay đổi vì đây là tài khoản đăng nhập của bạn.</p>
							</div>

							<div class="form-group">
								<label for="address">Địa chỉ</label>
								<input type="text" name="address" id="address" value="<?php echo htmlentities($data["user"]->Address ?? ''); ?>" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố">
								<p class="helper-text">Địa chỉ để xe đưa đón (nếu có).</p>
							</div>

							<div class="form-grid">
								<div class="form-group">
									<label for="dateofbirth">Ngày sinh</label>
									<input type="date" name="dateofbirth" id="dateofbirth" value="<?php echo htmlentities($data["user"]->DateOfBirth ?? ''); ?>">
								</div>
								<div class="form-group">
									<label for="gender">Giới tính</label>
									<select name="gender" id="gender">
										<option value="">Chọn giới tính</option>
										<option value="Nam" <?php if (($data["user"]->Gender ?? '') === 'Nam') echo 'selected'; ?>>Nam</option>
										<option value="Nữ" <?php if (($data["user"]->Gender ?? '') === 'Nữ') echo 'selected'; ?>>Nữ</option>
										<option value="Khác" <?php if (($data["user"]->Gender ?? '') === 'Khác') echo 'selected'; ?>>Khác</option>
									</select>
								</div>
							</div>

							<button type="submit" name="submit" class="btn">
								<i class="fas fa-save"></i> Lưu thay đổi
							</button>
						</form>
						<?php endif; ?>
					</div>
				</div>

				<!-- Bookings Tab -->
				<div class="tab-pane" id="bookings-tab">
					<div class="card">
						<h3>Lịch sử đặt tour</h3>
						<p class="helper-text">Theo dõi tất cả các tour bạn đã đặt và trạng thái của chúng.</p>
						
						<?php if (count($data["bookings"]) > 0): ?>
							<div class="bookings-grid">
								<?php foreach ($data["bookings"] as $booking): 
									$statusText = "Chờ xử lý";
									$statusClass = "is-pending";
									$statusIcon = "clock";
									if ($booking->status == 1) {
										$statusText = "Đã xác nhận";
										$statusClass = "is-approved";
										$statusIcon = "check-circle";
									}
									if ($booking->status == 2) {
										$statusText = "Đã hủy";
										$statusClass = "is-cancelled";
										$statusIcon = "times-circle";
									}
								?>
								<div class="booking-card">
									<div class="booking-header">
										<span class="booking-code">#BK<?php echo htmlentities($booking->bookid); ?></span>
										<span class="status-chip <?php echo $statusClass; ?>">
											<i class="fas fa-<?php echo $statusIcon; ?>"></i>
											<?php echo htmlentities($statusText); ?>
										</span>
									</div>
									<div class="booking-body">
										<h4 class="booking-tour-name">
											<a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($booking->pkgid); ?>">
												<?php echo htmlentities($booking->packagename); ?>
											</a>
										</h4>
										<div class="booking-details">
											<div class="booking-detail-item">
												<i class="fas fa-calendar"></i>
												<span>Ngày khởi hành: <?php echo htmlentities($booking->fromdate); ?></span>
											</div>
											<div class="booking-detail-item">
												<i class="fas fa-money-bill-wave"></i>
												<span class="booking-price"><?php echo Controller::formatVND($booking->packageprice); ?></span>
											</div>
											<div class="booking-detail-item">
												<i class="fas fa-clock"></i>
												<span>Đặt ngày <?php echo date('d/m/Y', strtotime($booking->regdate)); ?></span>
											</div>
											<?php if ($booking->status == 2 && !empty($booking->cancelby)): ?>
											<div class="booking-detail-item">
												<i class="fas fa-times-circle" style="color: var(--danger);"></i>
												<span>Đã hủy - 
													<a href="#" onclick="showCancelReason(event, '<?php echo htmlentities($booking->cancelreason ?? 'Không có thông tin'); ?>')" style="text-decoration: underline; cursor: pointer; color: var(--primary);">Xem lý do</a>
												</span>
											</div>
											<?php endif; ?>
										</div>
										<?php if (!empty($booking->comment)): ?>
										<div class="booking-comment">
											<i class="fas fa-comment"></i>
											<span><?php echo htmlentities($booking->comment); ?></span>
										</div>
										<?php endif; ?>
										
										<?php if (!empty($booking->customermessage)): ?>
										<div class="admin-message">
											<div class="admin-message-header">
												<span class="admin-message-icon">📨</span>
												<span class="admin-message-title">Lời nhắn từ Admin</span>
											</div>
											<div class="admin-message-content"><?php echo nl2br(htmlentities($booking->customermessage)); ?></div>
										</div>
										<?php endif; ?>
									</div>
									<div class="booking-footer">
										<a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($booking->pkgid); ?>" class="btn btn-ghost btn-compact">
											<i class="fas fa-eye"></i> Xem chi tiết
										</a>
										<?php if ($booking->status != 2): ?>
										<a class="btn btn-compact" style="background: var(--danger);" href="<?php echo BASE_URL; ?>tour/cancel/<?php echo htmlentities($booking->bookid); ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?');">
											<i class="fas fa-times"></i> Hủy đơn
										</a>
										<?php endif; ?>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<div class="empty-state">
								<i class="fas fa-inbox" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
								<p>Bạn chưa có đặt tour nào.</p>
								<a href="<?php echo BASE_URL; ?>package" class="btn">
									<i class="fas fa-search"></i> Khám phá tour
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Security Tab -->
				<div class="tab-pane" id="security-tab">
					<div class="card">
						<h3>Đổi mật khẩu</h3>
						<p class="helper-text">Đảm bảo tài khoản của bạn luôn an toàn bằng mật khẩu mạnh.</p>
						
						<form name="changePasswordForm" method="post" class="form-stack" action="<?php echo BASE_URL; ?>user/updatePassword">
							<div class="form-group">
								<label for="password">Mật khẩu hiện tại <span class="required">*</span></label>
								<input type="password" name="password" id="password" required>
							</div>
							<div class="form-group">
								<label for="newpassword">Mật khẩu mới <span class="required">*</span></label>
								<input type="password" name="newpassword" id="newpassword" required minlength="6">
								<p class="helper-text">Mật khẩu phải có ít nhất 6 ký tự.</p>
							</div>
							<div class="form-group">
								<label for="confirmpassword">Nhập lại mật khẩu mới <span class="required">*</span></label>
								<input type="password" name="confirmpassword" id="confirmpassword" required minlength="6">
							</div>
							<button type="submit" name="submit5" class="btn">
								<i class="fas fa-lock"></i> Đổi mật khẩu
							</button>
						</form>
					</div>
				</div>

				<!-- Wishlist Tab -->
				<div class="tab-pane" id="wishlist-tab">
					<div class="card">
						<h3>Tour yêu thích</h3>
						<p class="helper-text">Danh sách các tour bạn đã lưu để xem sau.</p>
						
						<?php if (count($data["wishlistItems"]) > 0): ?>
							<div class="tour-grid">
								<?php foreach ($data["wishlistItems"] as $item): ?>
								<div class="tour-card" data-package-id="<?php echo htmlentities($item->PackageId); ?>">
									<!-- Tour Type Badge -->
									<div class="badge"><?php echo htmlentities($item->PackageType); ?></div>

									<!-- Wishlist Heart -->
									<button class="wishlist-heart active" data-package-id="<?php echo htmlentities($item->PackageId); ?>" title="Xóa khỏi yêu thích">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
											<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
										</svg>
									</button>

									<!-- Image -->
									<div class="tilt">
										<div class="img">
											<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($item->PackageImage); ?>" alt="<?php echo htmlentities($item->PackageName); ?>">
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
												<?php echo htmlentities($item->PackageLocation); ?>
											</div>
										</div>

										<!-- Tour Name -->
										<h2 class="title"><?php echo htmlentities($item->PackageName); ?></h2>
							
										<!-- Description -->
										<p class="desc"><?php echo htmlentities($item->PackageFetures); ?></p>

										<!-- Duration -->
										<div class="duration-row">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<circle cx="12" cy="12" r="10"></circle>
												<polyline points="12 6 12 12 16 14"></polyline>
											</svg>
											<span><?php echo htmlentities($item->TourDuration); ?></span>
										</div>

										<!-- Price -->
										<div class="price-row">
											<span class="price-label">Chỉ từ</span>
											<span class="price-value"><?php echo Controller::formatVND($item->PackagePrice); ?></span>
										</div>

										<!-- Button -->
										<a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($item->PackageId); ?>" class="btn-detail">
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
								<i class="fas fa-heart-broken" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
								<p>Bạn chưa có tour yêu thích nào.</p>
								<a href="<?php echo BASE_URL; ?>package" class="btn">
									<i class="fas fa-search"></i> Khám phá tour
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
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
<script src="<?php echo BASE_URL; ?>public/js/account.js?v=1.0"></script>
<script src="<?php echo BASE_URL; ?>public/js/wishlist.js?v=1.1"></script>
<script>
	function showCancelReason(event, reason) {
		event.preventDefault();
		alert('Lý do hủy đơn:\n\n' + reason);
	}
</script>
</body>
</html>
