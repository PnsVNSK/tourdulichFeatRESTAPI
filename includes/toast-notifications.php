<?php
/**
 * tien ich thong bao toast
 * Hiển thị thông báo từ PHP session
 */

// Hien thi thong bao toast neu co message trong session
if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])): ?>
    <div data-toast-success="<?php echo htmlentities($_SESSION['msg']); ?>" style="display:none;"></div>
<?php unset($_SESSION['msg']); endif;

if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
    <div data-toast-error="<?php echo htmlentities($_SESSION['error']); ?>" style="display:none;"></div>
<?php unset($_SESSION['error']); endif;

if (isset($_SESSION['info']) && !empty($_SESSION['info'])): ?>
    <div data-toast-info="<?php echo htmlentities($_SESSION['info']); ?>" style="display:none;"></div>
<?php unset($_SESSION['info']); endif;

if (isset($_SESSION['warning']) && !empty($_SESSION['warning'])): ?>
    <div data-toast-warning="<?php echo htmlentities($_SESSION['warning']); ?>" style="display:none;"></div>
<?php unset($_SESSION['warning']); endif;
?>
