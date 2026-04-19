<?php
session_start();
include('includes/config.php');
$loginError = '';
if(isset($_POST['login']))
{
	$uname = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	
	if (empty($uname) || empty($password)) {
		$loginError = "Vui lòng nhập đầy đủ thông tin";
	} else {
		$sql = "SELECT UserName,Password FROM tbladmin WHERE UserName=:uname";
		$query = $dbh->prepare($sql);
		$query->bindParam(':uname', $uname, PDO::PARAM_STR);
		$query->execute();
		$admin = $query->fetch(PDO::FETCH_OBJ);
		
		if ($admin) {
			// Support both old MD5 and new password_hash
			$passwordValid = false;
			if (strlen($admin->Password) === 32 && ctype_xdigit($admin->Password)) {
				// Old MD5 hash - verify and upgrade
				if ($admin->Password === md5($password)) {
					$passwordValid = true;
					// Upgrade to password_hash
					$newHash = password_hash($password, PASSWORD_DEFAULT);
					$updateSql = "UPDATE tbladmin SET Password=:newpassword WHERE UserName=:uname";
					$updateQuery = $dbh->prepare($updateSql);
					$updateQuery->bindParam(':uname', $uname, PDO::PARAM_STR);
					$updateQuery->bindParam(':newpassword', $newHash, PDO::PARAM_STR);
					$updateQuery->execute();
				}
			} else {
				// New password_hash
				$passwordValid = password_verify($password, $admin->Password);
			}
			
			if ($passwordValid) {
				$_SESSION['alogin'] = $uname;
				header('location:dashboard.php');
				exit;
			} else {
				$loginError = "Thông tin đăng nhập không hợp lệ";
			}
		} else {
			$loginError = "Thông tin đăng nhập không hợp lệ";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel Admin | Đăng nhập</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/style.css">
</head>
<body class="auth-page" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../admin/packageimages/tour_halong.webp') no-repeat center center; background-size: cover;">
	<div class="auth-card">
		<h1 style="color:#fff">Đăng nhập quản trị</h1>
		<p class="helper-text" style="color:#e5e7eb">Sử dụng thông tin tài khoản được cấp để truy cập hệ thống quản trị.</p>
		<?php if($loginError){?><div class="alert error"><?php echo htmlentities($loginError);?></div><?php } ?>
		<form method="post">
			<div class="form-group">
				<label for="username" style="color:#fff">Tên đăng nhập</label>
				<input type="text" name="username" id="username" required>
			</div>
			<div class="form-group">
				<label for="password" style="color:#fff">Mật khẩu</label>
				<input type="password" name="password" id="password" required>
			</div>
			<button type="submit" name="login" class="btn btn-primary">Đăng nhập</button>
		</form>
		<p class="helper-text"><a href="<?php echo BASE_URL; ?>" style="color:#e5e7eb">← Quay lại trang khách</a></p>
	</div>
	<script src="<?php echo BASE_URL; ?>admin/js/app.js" defer></script>
</body>
</html>