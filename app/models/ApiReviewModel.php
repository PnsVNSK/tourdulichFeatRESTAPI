<?php

require_once(ROOT . '/core/Model.php');

class ApiReviewModel extends Model
{
    public function getStatsByTourId($tourId)
    {
        $sql = "SELECT ROUND(AVG(Rating), 1) AS avg_rating, COUNT(*) AS review_count
                FROM tbltourreviews
                WHERE PackageId = :tourId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'avg_rating' => isset($row['avg_rating']) ? ($row['avg_rating'] !== null ? (float)$row['avg_rating'] : null) : null,
            'review_count' => isset($row['review_count']) ? (int)$row['review_count'] : 0,
        ];
    }

    public function getByTourId($tourId, $limit = 50, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT r.ReviewId, r.PackageId, r.UserEmail, r.Rating, r.Note, r.RegDate, u.FullName
                FROM tbltourreviews r
                INNER JOIN tblusers u ON u.EmailId = r.UserEmail
                WHERE r.PackageId = :tourId
                ORDER BY r.RegDate DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserReview($tourId, $userEmail)
    {
        $sql = "SELECT ReviewId, PackageId, UserEmail, Rating, Note, RegDate
                FROM tbltourreviews
                WHERE PackageId = :tourId AND UserEmail = :userEmail
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->bindValue(':userEmail', $userEmail, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existsUserReview($tourId, $userEmail)
    {
        return (bool)$this->getUserReview($tourId, $userEmail);
    }

    public function create($tourId, $userEmail, $rating, $note)
    {
        $sql = "INSERT INTO tbltourreviews (PackageId, UserEmail, Rating, Note)
                VALUES (:tourId, :userEmail, :rating, :note)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->bindValue(':userEmail', $userEmail, PDO::PARAM_STR);
        $stmt->bindValue(':rating', (int)$rating, PDO::PARAM_INT);
        $stmt->bindValue(':note', $note, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateByUser($tourId, $userEmail, $rating, $note)
    {
        $sql = "UPDATE tbltourreviews
                SET Rating = :rating, Note = :note, RegDate = NOW()
                WHERE PackageId = :tourId AND UserEmail = :userEmail";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->bindValue(':userEmail', $userEmail, PDO::PARAM_STR);
        $stmt->bindValue(':rating', (int)$rating, PDO::PARAM_INT);
        $stmt->bindValue(':note', $note, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteByUser($tourId, $userEmail)
    {
        $sql = "DELETE FROM tbltourreviews WHERE PackageId = :tourId AND UserEmail = :userEmail";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tourId', (int)$tourId, PDO::PARAM_INT);
        $stmt->bindValue(':userEmail', $userEmail, PDO::PARAM_STR);
        return $stmt->execute();
    }
}

