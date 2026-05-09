<?php

require_once(ROOT . '/core/Model.php');

class WishlistModel extends Model {
    /**
     * them goi tour vao danh sach yeu thich
     */
    public function addToWishlist($userEmail, $packageId) {
        try {
            // Kiem tra ton tai truoc de tranh loi trung khoa
            if ($this->isInWishlist($userEmail, $packageId)) {
                return true; // Already in wishlist, consider it success
            }
            
            $sql = "INSERT INTO tblwishlist (UserEmail, PackageId) VALUES (:email, :packageId)";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            // Ghi log loi de debug
            error_log("WishlistModel::addToWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * xoa goi tour khoi danh sach yeu thich
     */
    public function removeFromWishlist($userEmail, $packageId) {
        try {
            $sql = "DELETE FROM tblwishlist WHERE UserEmail = :email AND PackageId = :packageId";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            // Ghi log loi de debug
            error_log("WishlistModel::removeFromWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * lay danh sach yeu thich kem chi tiet goi tour
     */
    public function getWishlistByUser($userEmail) {
        $sql = "SELECT w.id, w.PackageId, w.CreatedAt, 
                   p.PackageName, p.PackageType, p.PackageLocation, 
                   p.PackagePrice, p.PackageFetures, p.PackageImage, p.TourDuration
                FROM tblwishlist w
                INNER JOIN tbltourpackages p ON w.PackageId = p.PackageId
                WHERE w.UserEmail = :email
                ORDER BY w.CreatedAt DESC";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * kiem tra goi tour co trong danh sach yeu thich
     */
    public function isInWishlist($userEmail, $packageId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM tblwishlist WHERE UserEmail = :email AND PackageId = :packageId";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result->count > 0;
        } catch (PDOException $e) {
            error_log("WishlistModel::isInWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * lay mang id goi tour trong danh sach yeu thich
     */
    public function getWishlistPackageIds($userEmail) {
        $sql = "SELECT PackageId FROM tblwishlist WHERE UserEmail = :email";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        
        $packageIds = [];
        foreach ($results as $row) {
            $packageIds[] = $row->PackageId;
        }
        return $packageIds;
    }

    /**
     * dao trang thai yeu thich (chua co thi them, co roi thi xoa)
     */
    public function toggleWishlist($userEmail, $packageId) {
        try {
            if ($this->isInWishlist($userEmail, $packageId)) {
                return $this->removeFromWishlist($userEmail, $packageId);
            } else {
                return $this->addToWishlist($userEmail, $packageId);
            }
        } catch (Exception $e) {
            error_log("WishlistModel::toggleWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * dem so luong yeu thich cua nguoi dung
     */
    public function getWishlistCount($userEmail) {
        $sql = "SELECT COUNT(*) as count FROM tblwishlist WHERE UserEmail = :email";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
