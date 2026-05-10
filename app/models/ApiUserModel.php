<?php

require_once(ROOT . '/core/Model.php');

class ApiUserModel extends Model
{
    public function createUser($fullName, $mobile, $email, $password)
    {
        $sql = "INSERT INTO tblusers(FullName, MobileNumber, EmailId, Password)
                VALUES(:fullName, :mobile, :email, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fullName', $fullName, PDO::PARAM_STR);
        $stmt->bindValue(':mobile', $mobile, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT id, FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, Avatar, RegDate FROM tblusers WHERE id = :id");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT id, FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, Avatar, RegDate FROM tblusers WHERE EmailId = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyCredentials($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblusers WHERE EmailId = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }

        $hash = $user['Password'];
        $valid = false;
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            $valid = md5($password) === $hash;
            if ($valid) {
                $this->updatePassword($email, $password);
            }
        } else {
            $valid = password_verify($password, $hash);
        }
        return $valid ? $this->getByEmail($email) : false;
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM tblusers WHERE EmailId = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function updateProfile($email, $data)
    {
        $sql = "UPDATE tblusers
                SET FullName = :fullName,
                    MobileNumber = :mobile,
                    Address = :address,
                    DateOfBirth = :dob,
                    Gender = :gender
                WHERE EmailId = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fullName', $data['FullName'], PDO::PARAM_STR);
        $stmt->bindValue(':mobile', $data['MobileNumber'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $data['Address'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':dob', $data['DateOfBirth'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':gender', $data['Gender'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateProfileById($userId, $data)
    {
        $user = $this->getById($userId);
        if (!$user) {
            return false;
        }
        return $this->updateProfile($user['EmailId'], $data);
    }

    public function updatePassword($email, $newPassword)
    {
        $stmt = $this->db->prepare("UPDATE tblusers SET Password = :password WHERE EmailId = :email");
        $stmt->bindValue(':password', password_hash($newPassword, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
