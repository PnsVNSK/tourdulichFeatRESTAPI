<?php
class ItineraryModel extends Model
{
    /**
     * lay tat ca muc lich trinh cua 1 goi tour
     */
    public function getByPackageId($packageId)
    {
        $sql = "SELECT * FROM tblitinerary 
                WHERE PackageId = :packageId 
                ORDER BY SortOrder ASC, ItineraryId ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':packageId', $packageId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * lay 1 muc lich trinh theo id
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM tblitinerary WHERE ItineraryId = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * tao muc lich trinh moi
     */
    public function create($packageId, $timeLabel, $activity, $sortOrder = 0)
    {
        $sql = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) 
                VALUES (:packageId, :timeLabel, :activity, :sortOrder)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':packageId', $packageId, PDO::PARAM_INT);
        $stmt->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
        $stmt->bindParam(':activity', $activity, PDO::PARAM_STR);
        $stmt->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * cap nhat muc lich trinh
     */
    public function update($id, $timeLabel, $activity, $sortOrder = 0)
    {
        $sql = "UPDATE tblitinerary 
                SET TimeLabel = :timeLabel, 
                    Activity = :activity, 
                    SortOrder = :sortOrder 
                WHERE ItineraryId = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
        $stmt->bindParam(':activity', $activity, PDO::PARAM_STR);
        $stmt->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * xoa muc lich trinh
     */
    public function delete($id)
    {
        $sql = "DELETE FROM tblitinerary WHERE ItineraryId = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * lay thu tu sap xep tiep theo cho goi tour
     */
    public function getNextSortOrder($packageId)
    {
        $sql = "SELECT COALESCE(MAX(SortOrder), 0) + 1 as NextOrder 
                FROM tblitinerary 
                WHERE PackageId = :packageId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':packageId', $packageId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->NextOrder : 1;
    }

    /**
     * dem so muc lich trinh cua goi tour
     */
    public function countByPackageId($packageId)
    {
        $sql = "SELECT COUNT(*) as total FROM tblitinerary WHERE PackageId = :packageId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':packageId', $packageId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->total : 0;
    }
}
