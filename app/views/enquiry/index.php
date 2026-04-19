<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Liên hệ</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo BASE_URL; ?>admin/packageimages/tour_halong.webp') no-repeat center center; background-size: cover;">
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1 style="color: #fff">Liên hệ</h1>
			<p style="color: #e5e7eb">Điền form bên dưới để gửi tin nhắn — chúng tôi phản hồi qua email bạn cung cấp.<?php if (!empty($_SESSION['login'])): ?> Xem phản hồi đã gửi tại <a href="<?php echo BASE_URL; ?>enquiry/inbox" style="color:#fff;text-decoration:underline">hộp thư</a>.<?php endif; ?></p>
		</section>
		<section class="card enquiry-card" style="background: rgba(255,255,255,0.96); border: none;">
			<?php if (!empty($error)) { ?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } ?>
			<?php if (!empty($msg)) { ?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php } ?>
			<form name="enquiry" method="post" class="form-stack" action="<?php echo BASE_URL; ?>enquiry/submit">
				<div class="form-grid">
					<div class="form-group">
						<label for="fname">Họ và tên</label>
						<input type="text" name="fname" id="fname" placeholder="Nguyễn Văn A" required
							value="<?php echo !empty($prefill) ? htmlentities($prefill->FullName) : ''; ?>">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" name="email" id="email" placeholder="ban@example.com" required
							value="<?php echo !empty($prefill) ? htmlentities($prefill->EmailId) : ''; ?>">
					</div>
					<div class="form-group">
						<label for="mobileno">Số điện thoại</label>
						<input type="text" name="mobileno" id="mobileno" maxlength="10" placeholder="10 chữ số" required
							value="<?php echo !empty($prefill) ? htmlentities($prefill->MobileNumber) : ''; ?>">
					</div>
					<div class="form-group">
						<label for="subject">Tiêu đề tin nhắn</label>
						<input type="text" name="subject" id="subject" placeholder="Ví dụ: Hỏi về lịch khởi hành tour Đà Lạt" required maxlength="100">
					</div>
				</div>
				<div class="form-group">
					<label for="description">Nội dung tin nhắn</label>
					<textarea name="description" id="description" rows="6" placeholder="Viết nội dung cần hỏi hoặc ghi chú cho đội ngũ GoTravel…" required></textarea>
				</div>
				<button type="submit" name="submit1" class="btn">Gửi tin nhắn</button>
			</form>
		</section>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
</body>
</html>
