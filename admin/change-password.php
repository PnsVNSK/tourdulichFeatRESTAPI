<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_POST['submit']))
{
	$password = $_POST['password'] ?? '';
	$newpassword = $_POST['newpassword'] ?? '';
	$confirmpassword = $_POST['confirmpassword'] ?? '';
	$username = $_SESSION['alogin'];
	
	// Validate inputs
	if (empty($password) || empty($newpassword) || empty($confirmpassword)) {
		$error = "Vui lòng nhập đầy đủ thông tin";
	} elseif ($newpassword !== $confirmpassword) {
		$error = "Mật khẩu mới và xác nhận mật khẩu không trùng khớp";
	} elseif (strlen($newpassword) < 6) {
		$error = "Mật khẩu mới phải có ít nhất 6 ký tự";
	} else {
		$sql = "SELECT Password FROM tbladmin WHERE UserName=:username";
		$query = $dbh->prepare($sql);
		$query->bindParam(':username', $username, PDO::PARAM_STR);
		$query->execute();
		$admin = $query->fetch(PDO::FETCH_OBJ);
		
		if ($admin) {
			// Support both old MD5 and new password_hash
			$passwordValid = false;
			if (strlen($admin->Password) === 32 && ctype_xdigit($admin->Password)) {
				// Old MD5 hash
				$passwordValid = ($admin->Password === md5($password));
			} else {
				// New password_hash
				$passwordValid = password_verify($password, $admin->Password);
			}
			
			if ($passwordValid) {
				$hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
				$con = "UPDATE tbladmin SET Password=:newpassword WHERE UserName=:username";
				$chngpwd1 = $dbh->prepare($con);
				$chngpwd1->bindParam(':username', $username, PDO::PARAM_STR);
				$chngpwd1->bindParam(':newpassword', $hashedPassword, PDO::PARAM_STR);
				if ($chngpwd1->execute()) {
					$msg = "Mật khẩu đã được thay đổi thành công";
				} else {
					$error = "Có lỗi xảy ra. Vui lòng thử lại";
				}
			} else {
				$error = "Mật khẩu hiện tại không đúng";
			}
		} else {
			$error = "Không tìm thấy tài khoản";
		}
	}
}

$pageTitle = "GoTravel Admin | Đổi mật khẩu";
$currentPage = 'manage-users';
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Đổi mật khẩu</h1>
			<p>Cập nhật mật khẩu quản trị viên để đảm bảo an toàn tài khoản.</p>
		</div>
	</section>
	<?php if($error){?><div class="alert error"><?php echo htmlentities($error); ?></div><?php } ?>
	<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg); ?></div><?php } ?>
	<section class="card">
		<form name="chngpwd" method="post" class="form-stack" onsubmit="return validatePasswords();">
			<div class="form-group">
				<label for="current-password">Mật khẩu hiện tại</label>
				<input type="password" name="password" id="current-password" required>
			</div>
			<div class="form-grid">
				<div class="form-group">
					<label for="newpassword">Mật khẩu mới</label>
					<input type="password" name="newpassword" id="newpassword" required>
				</div>
				<div class="form-group">
					<label for="confirmpassword">Xác nhận mật khẩu</label>
					<input type="password" name="confirmpassword" id="confirmpassword" required>
				</div>
			</div>
			<button type="submit" name="submit" class="btn btn-primary">Cập nhật</button>
		</form>
	</section>
	<script>
	function validatePasswords(){
		const newPass = document.getElementById('newpassword').value;
		const confirmPass = document.getElementById('confirmpassword').value;
		if(newPass !== confirmPass){
			alert('Mật khẩu mới và xác nhận mật khẩu không trùng khớp!');
			return false;
		}
		return true;
	}
	</script>
<?php include('includes/layout-end.php');?>
