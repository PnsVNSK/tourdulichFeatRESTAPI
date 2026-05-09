<?php

require_once ROOT . "/core/Model.php";

class UserModel extends Model
{
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function updateUserProfile($email, $name, $mobile)
    {
        $sql =
            "UPDATE tblusers SET FullName=:name,MobileNumber=:mobile WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":name", $name, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        return $query->execute();
    }

    public function updateUserProfileExtended($email, $name, $mobile, $address, $dateOfBirth, $gender)
    {
        $sql = "UPDATE tblusers SET FullName=:name, MobileNumber=:mobile, Address=:address, DateOfBirth=:dob, Gender=:gender WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":name", $name, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->bindParam(":address", $address, PDO::PARAM_STR);
        $query->bindParam(":dob", $dateOfBirth, PDO::PARAM_STR);
        $query->bindParam(":gender", $gender, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        return $query->execute();
    }

    public function updateAvatar($email, $avatarPath)
    {
        $sql = "UPDATE tblusers SET Avatar=:avatar WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":avatar", $avatarPath, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkPassword($email, $password)
    {
        $sql = "SELECT Password FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_OBJ);
        
        if (!$user) {
            return false;
        }
        
        // Ho tro ca md5 cu va password_hash moi
        if (strlen($user->Password) === 32 && ctype_xdigit($user->Password)) {
            // Mat khau md5 cu - kiem tra va nang cap
            if ($user->Password === md5($password)) {
                // Nang cap sang password_hash
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $this->updatePassword($email, $newHash);
                return true;
            }
            return false;
        } else {
            // Mat khau password_hash moi
            return password_verify($password, $user->Password);
        }
    }

    public function updatePassword($email, $newpassword)
    {
        // Dam bao mat khau duoc bam neu chua bam
        $isAlreadyHashed = (strlen($newpassword) >= 60 && (strpos($newpassword, '$2y$') === 0 || strpos($newpassword, '$2a$') === 0 || strpos($newpassword, '$2x$') === 0));
        
        if (!$isAlreadyHashed) {
            $newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
        }
        
        $sql = "UPDATE tblusers SET Password=:newpassword WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":newpassword", $newpassword, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkUserByEmailAndMobile($email, $mobile)
    {
        $sql =
            "SELECT EmailId FROM tblusers WHERE EmailId=:email and MobileNumber=:mobile";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public function resetPassword($email, $mobile, $newpassword)
    {
        // Bam mat khau
        $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
        
        $sql =
            "UPDATE tblusers SET Password=:newpassword WHERE EmailId=:email and MobileNumber=:mobile";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->bindParam(":newpassword", $hashedPassword, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkEmailAvailability($email)
    {
        $sql = "SELECT EmailId FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public function createUser($fname, $mnumber, $email, $password)
    {
        // Bam mat khau
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql =
            "INSERT INTO tblusers(FullName,MobileNumber,EmailId,Password) VALUES(:fname,:mnumber,:email,:password)";
        $query = $this->db->prepare($sql);
        $query->bindParam(":fname", $fname, PDO::PARAM_STR);
        $query->bindParam(":mnumber", $mnumber, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $query->execute();
        return $this->db->lastInsertId();
    }
}
