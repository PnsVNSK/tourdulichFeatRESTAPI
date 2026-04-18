-- ============================================
-- DATABASE tour - CLEAN & FIXED
-- ============================================

CREATE DATABASE IF NOT EXISTS `tour` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `tour`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- DROP TABLES
-- ============================================
DROP TABLE IF EXISTS `tblitinerary`;
DROP TABLE IF EXISTS `tblwishlist`;
DROP TABLE IF EXISTS `tblbooking`;
DROP TABLE IF EXISTS `tblissues`;
DROP TABLE IF EXISTS `tblenquiry`;
DROP TABLE IF EXISTS `tblpages`;
DROP TABLE IF EXISTS `tbltourpackages`;
DROP TABLE IF EXISTS `tblusers`;
DROP TABLE IF EXISTS `tbladmin`;

-- ============================================
-- TABLES
-- ============================================

CREATE TABLE `tbladmin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `UserName` VARCHAR(100) NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `updationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `tblusers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `FullName` VARCHAR(100) NOT NULL,
  `MobileNumber` CHAR(10) NOT NULL,
  `EmailId` VARCHAR(70) NOT NULL UNIQUE,
  `Password` VARCHAR(100) NOT NULL,
  `RegDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` TIMESTAMP NULL DEFAULT NULL 
    ON UPDATE CURRENT_TIMESTAMP,
  `Avatar` VARCHAR(255),
  `Address` VARCHAR(255),
  `DateOfBirth` DATE,
  `Gender` VARCHAR(10)
) ENGINE=InnoDB;

CREATE TABLE `tbltourpackages` (
  `PackageId` INT AUTO_INCREMENT PRIMARY KEY,
  `PackageName` VARCHAR(200) NOT NULL,
  `PackageType` VARCHAR(150) NOT NULL,
  `TourDuration` VARCHAR(100) NOT NULL,
  `PackageLocation` VARCHAR(100) NOT NULL,
  `PackagePrice` INT NOT NULL,
  `PackageFetures` VARCHAR(255) NOT NULL,
  `PackageDetails` MEDIUMTEXT NOT NULL,
  `PackageImage` VARCHAR(100) NOT NULL,
  `Creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` TIMESTAMP NULL DEFAULT NULL 
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `tblbooking` (
  `BookingId` INT AUTO_INCREMENT PRIMARY KEY,
  `PackageId` INT NOT NULL,
  `UserEmail` VARCHAR(100) NOT NULL,
  `FromDate` DATE NOT NULL,
  `ToDate` DATE NOT NULL,
  `Comment` MEDIUMTEXT,
  `NumberOfPeople` INT NOT NULL,
  `TotalPrice` DECIMAL(10,2) NOT NULL,
  `AdminNotes` MEDIUMTEXT,
  `CancelReason` MEDIUMTEXT,
  `RegDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` INT NOT NULL,
  `CancelledBy` VARCHAR(5),
  `UpdationDate` TIMESTAMP NULL DEFAULT NULL 
    ON UPDATE CURRENT_TIMESTAMP,
  `CustomerMessage` MEDIUMTEXT
) ENGINE=InnoDB;

CREATE TABLE `tblenquiry` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `FullName` VARCHAR(100) NOT NULL,
  `EmailId` VARCHAR(100) NOT NULL,
  `MobileNumber` CHAR(10) NOT NULL,
  `Subject` VARCHAR(100) NOT NULL,
  `Description` MEDIUMTEXT NOT NULL,
  `PostingDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `Status` INT
) ENGINE=InnoDB;

CREATE TABLE `tblissues` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `UserEmail` VARCHAR(100) NOT NULL,
  `Issue` VARCHAR(100) NOT NULL,
  `Description` MEDIUMTEXT NOT NULL,
  `PostingDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `AdminRemark` MEDIUMTEXT,
  `AdminremarkDate` TIMESTAMP NULL DEFAULT NULL 
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `tblpages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(255) NOT NULL,
  `detail` LONGTEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `tblwishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `UserEmail` VARCHAR(70) NOT NULL,
  `PackageId` INT NOT NULL,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (`UserEmail`, `PackageId`)
) ENGINE=InnoDB;

CREATE TABLE `tblitinerary` (
  `ItineraryId` INT AUTO_INCREMENT PRIMARY KEY,
  `PackageId` INT NOT NULL,
  `TimeLabel` VARCHAR(255) NOT NULL,
  `Activity` TEXT NOT NULL,
  `SortOrder` INT DEFAULT 0,
  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- FOREIGN KEYS
-- ============================================

ALTER TABLE `tblbooking`
ADD CONSTRAINT `fk_booking_package` 
FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages`(`PackageId`) 
ON DELETE CASCADE,
ADD CONSTRAINT `fk_booking_user` 
FOREIGN KEY (`UserEmail`) REFERENCES `tblusers`(`EmailId`) 
ON DELETE CASCADE;

ALTER TABLE `tblwishlist`
ADD CONSTRAINT `fk_wishlist_package` 
FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages`(`PackageId`) 
ON DELETE CASCADE,
ADD CONSTRAINT `fk_wishlist_user` 
FOREIGN KEY (`UserEmail`) REFERENCES `tblusers`(`EmailId`) 
ON DELETE CASCADE;

ALTER TABLE `tblitinerary`
ADD CONSTRAINT `fk_itinerary_package` 
FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages`(`PackageId`) 
ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO `tbladmin` VALUES
(1, 'admin', 'f925916e2754e5e03f75dd58a5733251', '2017-05-13 11:18:49');

INSERT INTO `tblusers` VALUES
(12, 'Le Van Uy', '0763165881', 'leuy26011@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', '2025-11-20 04:20:33', NULL, NULL, NULL, NULL, NULL),
(13, 'Le Van Uy', '0389378485', 'leuy260105@gmail.com', '258d88e5ebfa52d32ad49bf932146263', '2025-11-21 03:14:09', NULL, NULL, NULL, NULL, NULL);

-- Du lieu cu ben tren duoc GIU NGUYEN.
-- Duoi day la du lieu bo sung de test CRUD (20 dong moi bang chinh).

INSERT INTO `tblusers` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Password`, `Address`, `DateOfBirth`, `Gender`) VALUES
(101, 'User 01', '0910000001', 'user01@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Da Nang', '1998-01-01', 'Nam'),
(102, 'User 02', '0910000002', 'user02@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hue', '1998-01-02', 'Nu'),
(103, 'User 03', '0910000003', 'user03@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hoi An', '1998-01-03', 'Nam'),
(104, 'User 04', '0910000004', 'user04@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Da Lat', '1998-01-04', 'Nu'),
(105, 'User 05', '0910000005', 'user05@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Nha Trang', '1998-01-05', 'Nam'),
(106, 'User 06', '0910000006', 'user06@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Phu Quoc', '1998-01-06', 'Nu'),
(107, 'User 07', '0910000007', 'user07@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Quy Nhon', '1998-01-07', 'Nam'),
(108, 'User 08', '0910000008', 'user08@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Ha Noi', '1998-01-08', 'Nu'),
(109, 'User 09', '0910000009', 'user09@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hai Phong', '1998-01-09', 'Nam'),
(110, 'User 10', '0910000010', 'user10@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Can Tho', '1998-01-10', 'Nu'),
(111, 'User 11', '0910000011', 'user11@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Da Nang', '1998-01-11', 'Nam'),
(112, 'User 12', '0910000012', 'user12@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hue', '1998-01-12', 'Nu'),
(113, 'User 13', '0910000013', 'user13@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hoi An', '1998-01-13', 'Nam'),
(114, 'User 14', '0910000014', 'user14@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Da Lat', '1998-01-14', 'Nu'),
(115, 'User 15', '0910000015', 'user15@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Nha Trang', '1998-01-15', 'Nam'),
(116, 'User 16', '0910000016', 'user16@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Phu Quoc', '1998-01-16', 'Nu'),
(117, 'User 17', '0910000017', 'user17@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Quy Nhon', '1998-01-17', 'Nam'),
(118, 'User 18', '0910000018', 'user18@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Ha Noi', '1998-01-18', 'Nu'),
(119, 'User 19', '0910000019', 'user19@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Hai Phong', '1998-01-19', 'Nam'),
(120, 'User 20', '0910000020', 'user20@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', 'Can Tho', '1998-01-20', 'Nu');

INSERT INTO `tbltourpackages` (`PackageId`, `PackageName`, `PackageType`, `TourDuration`, `PackageLocation`, `PackagePrice`, `PackageFetures`, `PackageDetails`, `PackageImage`) VALUES
(201, 'Tour 01', 'Nghi duong', '3 ngay 2 dem', 'Da Nang', 3100000, 'Khach san 3 sao', 'Ngay 1 tham quan ban dao Son Tra va bien My Khe. Ngay 2 di Ba Na Hills, toi tu do an dem. Ngay 3 mua sam dac san va ket thuc hanh trinh.', 'tour01.jpg'),
(202, 'Tour 02', 'Van hoa', '2 ngay 1 dem', 'Hue', 3200000, 'Xe dua don', 'Tham quan Dai Noi, chua Thien Mu va lang Khai Dinh. Thuong thuc am thuc cung dinh Hue va nghe ca Hue tren song Huong vao buoi toi.', 'tour02.jpg'),
(203, 'Tour 03', 'Kham pha', '3 ngay 2 dem', 'Hoi An', 3300000, 'Huong dan vien', 'Kham pha pho co Hoi An, di thuyen tren song Hoai, tham lang gom Thanh Ha va trai nghiem den long ve dem tai chua Cau.', 'tour03.jpg'),
(204, 'Tour 04', 'Nghi duong', '2 ngay 1 dem', 'Da Lat', 3400000, 'Bua sang', 'Check-in vuon hoa thanh pho, doi che Cau Dat, tham quan thung lung tinh yeu va thuong thuc cafe view doi thong.', 'tour04.jpg'),
(205, 'Tour 05', 'Bien dao', '3 ngay 2 dem', 'Nha Trang', 3500000, 'Cano', 'Di cano tham quan Hon Mun, lan ngam san ho, tam bien bai Tru. Buoi toi dao cho dem Nha Trang va thuong thuc hai san tuoi.', 'tour05.jpg'),
(206, 'Tour 06', 'Cao cap', '4 ngay 3 dem', 'Phu Quoc', 3600000, 'Resort', 'Nghi duong tai resort gan bien, tham quan Grand World, cap treo Hon Thom va ngam hoang hon tai Sunset Sanato.', 'tour06.jpg'),
(207, 'Tour 07', 'Nghi duong', '3 ngay 2 dem', 'Quy Nhon', 3700000, 'Khach san 4 sao', 'Khong gian nghi duong thoang mat, check-in Eo Gio - Ky Co, trai nghiem am thuc Binh Dinh va nghe nhac acoustic buoi toi.', 'tour07.jpg'),
(208, 'Tour 08', 'Van hoa', '2 ngay 1 dem', 'Ha Noi', 3800000, 'Ve tham quan', 'Tham Van Mieu, Ho Guom, pho co Ha Noi. Thuong thuc pho, bun cha va xem mua roi nuoc de hieu them van hoa Thu do.', 'tour08.jpg'),
(209, 'Tour 09', 'Kham pha', '3 ngay 2 dem', 'Hai Phong', 3900000, 'Xe du lich', 'Khams pha Cat Ba, tham quan Vinh Lan Ha, cheo kayak va thuong thuc hai san Hai Phong noi tieng trong khong gian ven bien.', 'tour09.jpg'),
(210, 'Tour 10', 'Nghi duong', '2 ngay 1 dem', 'Can Tho', 4000000, 'Bua toi', 'Trải nghiệm chợ nổi Cai Rang luc sang som, ghe tham vuon trai cay va nha co Binh Thuy. Toi di du thuyen tren song Hau.', 'tour10.jpg'),
(211, 'Tour 11', 'Bien dao', '3 ngay 2 dem', 'Da Nang', 4100000, 'Buffet', 'Lich trinh ket hop nghi duong bien va vui choi, tham quan Ngu Hanh Son, du ngoan cau tinh yeu va ngam thanh pho ve dem.', 'tour11.jpg'),
(212, 'Tour 12', 'Van hoa', '2 ngay 1 dem', 'Hue', 4200000, 'Check-in dia danh', 'Hanh trinh den cac diem di san, ghe cho Dong Ba va hoc lam banh Hue. Phu hop du khach muon tim hieu net dep co do.', 'tour12.jpg'),
(213, 'Tour 13', 'Kham pha', '3 ngay 2 dem', 'Hoi An', 4300000, 'Lich trinh linh hoat', 'Khach duoc lua chon lich trinh theo so thich: pho co, bien Cua Dai, rung dua, hoac tham quan lang nghe truyen thong.', 'tour13.jpg'),
(214, 'Tour 14', 'Nghi duong', '2 ngay 1 dem', 'Da Lat', 4400000, 'Khach san trung tam', 'Tam huong khong khi lanh, tham quan Ho Xuan Huong, cho Da Lat va vuon dau. Toi BBQ ngoai troi tai homestay san vuon.', 'tour14.jpg'),
(215, 'Tour 15', 'Bien dao', '3 ngay 2 dem', 'Nha Trang', 4500000, 'Tam bien', 'Chuoi hoat dong bien phong phu: moto nuoc, check-in Hon Chong, thuong thuc bua toi hai san va dao pho dem Tran Phu.', 'tour15.jpg'),
(216, 'Tour 16', 'Cao cap', '4 ngay 3 dem', 'Phu Quoc', 4600000, 'Cap treo', 'Tour cao cap voi phong huong bien, dua don san bay, tham quan nam dao, cap treo vuot bien va bua toi set menu.', 'tour16.jpg'),
(217, 'Tour 17', 'Nghi duong', '3 ngay 2 dem', 'Quy Nhon', 4700000, 'Xe cao cap', 'Tham quan Thap Doi, Ghenh Rang, bai tam dep va khu da Trung. Lich trinh nhe nhang phu hop gia dinh va nhom ban.', 'tour17.jpg'),
(218, 'Tour 18', 'Van hoa', '2 ngay 1 dem', 'Ha Noi', 4800000, 'Khach san 4 sao', 'Ghe tham lang gom Bat Trang, bao tang dan toc hoc, thuong thuc cafe trung va chup anh pho co vao buoi sang som.', 'tour18.jpg'),
(219, 'Tour 19', 'Kham pha', '3 ngay 2 dem', 'Hai Phong', 4900000, 'An trua', 'Lich trinh den Do Son va Cat Hai, trai nghiem doi song ngu dan dia phuong, thuong thuc banh da cua va hai san tuoi.', 'tour19.jpg'),
(220, 'Tour 20', 'Nghi duong', '2 ngay 1 dem', 'Can Tho', 5000000, 'An toi', 'Du ngoan mien Tay voi tai nghe don ca tai tu, tham quan con Son, vuon trai cay va thuong thuc dac san banh xeo.', 'tour20.jpg');

INSERT INTO `tblitinerary` (`ItineraryId`, `PackageId`, `TimeLabel`, `Activity`, `SortOrder`) VALUES
(301, 201, '07:00', 'Khoi hanh', 1),
(302, 201, '12:00', 'An trua', 2),
(303, 202, '08:00', 'Tham quan dai noi', 1),
(304, 202, '14:00', 'Check-in lang co', 2),
(305, 203, '07:30', 'Pho co Hoi An', 1),
(306, 203, '13:00', 'Rung dua Bay Mau', 2),
(307, 204, '08:30', 'Doi che', 1),
(308, 204, '15:00', 'Thac nuoc', 2),
(309, 205, '09:00', 'Di dao', 1),
(310, 205, '14:30', 'Lan ngam san ho', 2),
(311, 206, '08:00', 'Grand World', 1),
(312, 206, '16:00', 'Bai Sao', 2),
(313, 207, '07:00', 'Eo Gio', 1),
(314, 207, '13:00', 'Ky Co', 2),
(315, 208, '08:00', 'Ho Guom', 1),
(316, 208, '14:00', 'Van Mieu', 2),
(317, 209, '09:00', 'Do Son', 1),
(318, 209, '15:00', 'Hai san', 2),
(319, 210, '08:30', 'Cho noi', 1),
(320, 210, '14:30', 'Nha co', 2);

INSERT INTO `tblbooking` (`BookingId`, `PackageId`, `UserEmail`, `FromDate`, `ToDate`, `Comment`, `NumberOfPeople`, `TotalPrice`, `status`, `CancelledBy`) VALUES
(401, 201, 'user01@gmail.com', '2026-06-01', '2026-06-03', 'Booking 01', 2, 6200000.00, 0, NULL),
(402, 202, 'user02@gmail.com', '2026-06-02', '2026-06-03', 'Booking 02', 1, 3200000.00, 1, NULL),
(403, 203, 'user03@gmail.com', '2026-06-03', '2026-06-05', 'Booking 03', 2, 6600000.00, 0, NULL),
(404, 204, 'user04@gmail.com', '2026-06-04', '2026-06-05', 'Booking 04', 3, 10200000.00, 1, NULL),
(405, 205, 'user05@gmail.com', '2026-06-05', '2026-06-07', 'Booking 05', 1, 3500000.00, 0, NULL),
(406, 206, 'user06@gmail.com', '2026-06-06', '2026-06-09', 'Booking 06', 2, 7200000.00, 0, NULL),
(407, 207, 'user07@gmail.com', '2026-06-07', '2026-06-09', 'Booking 07', 2, 7400000.00, 1, NULL),
(408, 208, 'user08@gmail.com', '2026-06-08', '2026-06-09', 'Booking 08', 1, 3800000.00, 2, 'u'),
(409, 209, 'user09@gmail.com', '2026-06-09', '2026-06-11', 'Booking 09', 2, 7800000.00, 0, NULL),
(410, 210, 'user10@gmail.com', '2026-06-10', '2026-06-11', 'Booking 10', 1, 4000000.00, 1, NULL),
(411, 211, 'user11@gmail.com', '2026-06-11', '2026-06-13', 'Booking 11', 2, 8200000.00, 0, NULL),
(412, 212, 'user12@gmail.com', '2026-06-12', '2026-06-13', 'Booking 12', 1, 4200000.00, 0, NULL),
(413, 213, 'user13@gmail.com', '2026-06-13', '2026-06-15', 'Booking 13', 2, 8600000.00, 1, NULL),
(414, 214, 'user14@gmail.com', '2026-06-14', '2026-06-15', 'Booking 14', 1, 4400000.00, 0, NULL),
(415, 215, 'user15@gmail.com', '2026-06-15', '2026-06-17', 'Booking 15', 2, 9000000.00, 2, 'u'),
(416, 216, 'user16@gmail.com', '2026-06-16', '2026-06-19', 'Booking 16', 1, 4600000.00, 0, NULL),
(417, 217, 'user17@gmail.com', '2026-06-17', '2026-06-19', 'Booking 17', 3, 14100000.00, 1, NULL),
(418, 218, 'user18@gmail.com', '2026-06-18', '2026-06-19', 'Booking 18', 1, 4800000.00, 0, NULL),
(419, 219, 'user19@gmail.com', '2026-06-19', '2026-06-21', 'Booking 19', 2, 9800000.00, 0, NULL),
(420, 220, 'user20@gmail.com', '2026-06-20', '2026-06-21', 'Booking 20', 1, 5000000.00, 1, NULL);

INSERT INTO `tblwishlist` (`id`, `UserEmail`, `PackageId`) VALUES
(501, 'user01@gmail.com', 201),
(502, 'user02@gmail.com', 202),
(503, 'user03@gmail.com', 203),
(504, 'user04@gmail.com', 204),
(505, 'user05@gmail.com', 205),
(506, 'user06@gmail.com', 206),
(507, 'user07@gmail.com', 207),
(508, 'user08@gmail.com', 208),
(509, 'user09@gmail.com', 209),
(510, 'user10@gmail.com', 210),
(511, 'user11@gmail.com', 211),
(512, 'user12@gmail.com', 212),
(513, 'user13@gmail.com', 213),
(514, 'user14@gmail.com', 214),
(515, 'user15@gmail.com', 215),
(516, 'user16@gmail.com', 216),
(517, 'user17@gmail.com', 217),
(518, 'user18@gmail.com', 218),
(519, 'user19@gmail.com', 219),
(520, 'user20@gmail.com', 220);
