<?php
$isLoggedIn = !empty($_SESSION["login"]); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<header class="site-header">
	<div class="top-bar">
		<div class="container top-bar__content">
			<div class="top-bar__links">
				<?php if ($isLoggedIn): ?>
					<span class="link-muted">Xin chào, <?php echo htmlentities(
         $_SESSION["login"],
     ); ?></span>
					<a class="link-muted" href="<?php echo BASE_URL; ?>user/profile">Hồ sơ</a>
					<a class="link-muted" href="<?php echo BASE_URL; ?>user/change-password">Đổi mật khẩu</a>
					<a class="link-muted" href="<?php echo BASE_URL; ?>tour/history">Lịch sử tour</a>
					<a class="btn btn-ghost btn-compact" href="<?php echo BASE_URL; ?>user/logout">Đăng xuất</a>
				<?php else: ?>
					<a class="link-muted" href="<?php echo BASE_URL; ?>admin/index.php">Quản trị</a>
					<a class="link-muted" href="#" data-modal-target="signup-modal">Đăng ký</a>
					<a class="btn btn-ghost btn-compact" href="#" data-modal-target="signin-modal">Đăng nhập</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="nav-bar">
		<div class="container nav-bar__content">
			<a class="brand" href="<?php echo BASE_URL; ?>">GoTravel</a>
			<form class="nav-search" action="<?php echo BASE_URL; ?>package" method="get">
				<input type="text" name="keyword" placeholder="Tìm kiếm tour...">
				<button type="submit"><i class="fa fa-search"></i></button>
			</form>
			<button class="nav-toggle" type="button" aria-label="Mở menu" aria-expanded="false">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<nav class="site-nav" id="siteNav">
				<a href="<?php echo BASE_URL; ?>">Trang chủ</a>
				<a href="<?php echo BASE_URL; ?>page/aboutus">Giới thiệu</a>
				<a href="<?php echo BASE_URL; ?>package">Gói du lịch</a>
				<a href="<?php echo BASE_URL; ?>page/privacy">Chính sách</a>
				<a href="<?php echo BASE_URL; ?>page/terms">Điều khoản</a>
				<a href="<?php echo BASE_URL; ?>enquiry">Liên hệ</a>
				<?php if ($isLoggedIn): ?>
					<a href="<?php echo BASE_URL; ?>user/account" class="account-link"><i class="fas fa-user-circle"></i> Tài khoản</a>
					<a href="#" data-modal-target="support-modal">Yêu cầu hỗ trợ</a>
				<?php else: ?>
					<a href="#" data-modal-target="signin-modal" class="account-link"><i class="fas fa-user-circle"></i> Tài khoản</a>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</header>
<?php include ROOT . "/includes/toast-notifications.php"; ?>
<script src="<?php echo BASE_URL; ?>public/js/toast-notifications.js?v=1.0"></script>
