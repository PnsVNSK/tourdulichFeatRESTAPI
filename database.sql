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
DROP TABLE IF EXISTS `tbltourreviews`;
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
  `AdminReply` MEDIUMTEXT NULL,
  `ReplyDate` TIMESTAMP NULL DEFAULT NULL,
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

CREATE TABLE `tbltourreviews` (
  `ReviewId` INT AUTO_INCREMENT PRIMARY KEY,
  `PackageId` INT NOT NULL,
  `UserEmail` VARCHAR(70) NOT NULL,
  `Rating` TINYINT UNSIGNED NOT NULL,
  `Note` VARCHAR(1000) NOT NULL DEFAULT '',
  `RegDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_package` (`UserEmail`, `PackageId`),
  KEY `idx_tourreviews_package` (`PackageId`)
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

ALTER TABLE `tbltourreviews`
ADD CONSTRAINT `fk_tourreviews_package`
FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages`(`PackageId`)
ON DELETE CASCADE,
ADD CONSTRAINT `fk_tourreviews_user`
FOREIGN KEY (`UserEmail`) REFERENCES `tblusers`(`EmailId`)
ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SEED DATA
-- ============================================
-- Mat khau khach (tblusers) trong file nay: plain text "123456" (luu MD5 e10adc3949ba59abbe56e057f20f883e).

INSERT INTO `tbladmin` VALUES
(1, 'admin', 'f925916e2754e5e03f75dd58a5733251', '2017-05-13 11:18:49');

INSERT INTO `tblusers` VALUES
(12, 'Le Van Uy', '0763165881', 'leuy26011@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2025-11-20 04:20:33', NULL, NULL, NULL, NULL, NULL),
(13, 'Le Van Uy', '0389378485', 'leuy260105@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2025-11-21 03:14:09', NULL, NULL, NULL, NULL, NULL);

-- Du lieu cu ben tren duoc GIU NGUYEN.
-- Duoi day la du lieu bo sung de test CRUD (20 dong moi bang chinh).

INSERT INTO `tblusers` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Password`, `Address`, `DateOfBirth`, `Gender`) VALUES
(101, 'User 01', '0910000001', 'user01@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Nang', '1998-01-01', 'Nam'),
(102, 'User 02', '0910000002', 'user02@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hue', '1998-01-02', 'Nu'),
(103, 'User 03', '0910000003', 'user03@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hoi An', '1998-01-03', 'Nam'),
(104, 'User 04', '0910000004', 'user04@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Lat', '1998-01-04', 'Nu'),
(105, 'User 05', '0910000005', 'user05@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Nha Trang', '1998-01-05', 'Nam'),
(106, 'User 06', '0910000006', 'user06@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Phu Quoc', '1998-01-06', 'Nu'),
(107, 'User 07', '0910000007', 'user07@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Quy Nhon', '1998-01-07', 'Nam'),
(108, 'User 08', '0910000008', 'user08@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Ha Noi', '1998-01-08', 'Nu'),
(109, 'User 09', '0910000009', 'user09@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hai Phong', '1998-01-09', 'Nam'),
(110, 'User 10', '0910000010', 'user10@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Can Tho', '1998-01-10', 'Nu'),
(111, 'User 11', '0910000011', 'user11@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Nang', '1998-01-11', 'Nam'),
(112, 'User 12', '0910000012', 'user12@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hue', '1998-01-12', 'Nu'),
(113, 'User 13', '0910000013', 'user13@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hoi An', '1998-01-13', 'Nam'),
(114, 'User 14', '0910000014', 'user14@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Lat', '1998-01-14', 'Nu'),
(115, 'User 15', '0910000015', 'user15@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Nha Trang', '1998-01-15', 'Nam'),
(116, 'User 16', '0910000016', 'user16@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Phu Quoc', '1998-01-16', 'Nu'),
(117, 'User 17', '0910000017', 'user17@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Quy Nhon', '1998-01-17', 'Nam'),
(118, 'User 18', '0910000018', 'user18@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Ha Noi', '1998-01-18', 'Nu'),
(119, 'User 19', '0910000019', 'user19@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hai Phong', '1998-01-19', 'Nam'),
(120, 'User 20', '0910000020', 'user20@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Can Tho', '1998-01-20', 'Nu');

INSERT INTO `tbltourpackages` (`PackageId`, `PackageName`, `PackageType`, `TourDuration`, `PackageLocation`, `PackagePrice`, `PackageFetures`, `PackageDetails`, `PackageImage`) VALUES
(201, 'Đà Nẵng Biển Ngọc 3N2Đ', 'Nghi duong', '3 ngay 2 dem', 'Da Nang', 3100000, 'Khach san 3 sao', 'Ngày 1 tham quan bán đảo Sơn Trà và biển Mỹ Khê. Ngày 2 đi Bà Nà Hills, tối tự do ăn đêm. Ngày 3 mua sắm đặc sản và kết thúc hành trình.', 'tour01.jpg'),
(202, 'Sắc Màu Cố Đô Huế', 'Van hoa', '2 ngay 1 dem', 'Hue', 3200000, 'Xe dua don', 'Tham quan Đại Nội, chùa Thiên Mụ và lăng Khải Định. Thưởng thức ẩm thực cung đình Huế và nghe ca Huế trên sông Hương vào buổi tối.', 'tour02.jpg'),
(203, 'Phố Cổ Hội An Về Đêm', 'Kham pha', '3 ngay 2 dem', 'Hoi An', 3300000, 'Huong dan vien', 'Khám phá phố cổ Hội An, đi thuyền trên sông Hoài, thăm làng gốm Thanh Hà và trải nghiệm đèn lồng về đêm tại chùa Cầu.', 'tour03.jpg'),
(204, 'Đà Lạt Mùa Sương Sớm', 'Nghi duong', '2 ngay 1 dem', 'Da Lat', 3400000, 'Bua sang', 'Check-in vườn hoa thành phố, đồi chè Cầu Đất, tham quan thung lũng tình yêu và thưởng thức cà phê view đồi thông.', 'tour04.jpg'),
(205, 'Nha Trang Làn Biển Xanh', 'Bien dao', '3 ngay 2 dem', 'Nha Trang', 3500000, 'Cano', 'Đi cano tham quan Hòn Mun, lặn ngắm san hô, tắm biển bãi Trũ. Buổi tối dạo chợ đêm Nha Trang và thưởng thức hải sản tươi.', 'tour05.jpg'),
(206, 'Phú Quốc Sunset Retreat', 'Cao cap', '4 ngay 3 dem', 'Phu Quoc', 3600000, 'Resort', 'Nghỉ dưỡng tại resort gần biển, tham quan Grand World, cáp treo Hòn Thơm và ngắm hoàng hôn tại Sunset Sanato.', 'tour06.jpg'),
(207, 'Quy Nhơn Eo Gió Kỳ Co', 'Nghi duong', '3 ngay 2 dem', 'Quy Nhon', 3700000, 'Khach san 4 sao', 'Không gian nghỉ dưỡng thoáng mát, check-in Eo Gió - Kỳ Co, trải nghiệm ẩm thực Bình Định và nghe nhạc acoustic buổi tối.', 'tour07.jpg'),
(208, 'Hà Nội Di Sản Phố Cổ', 'Van hoa', '2 ngay 1 dem', 'Ha Noi', 3800000, 'Ve tham quan', 'Thăm Văn Miếu, Hồ Gươm, phố cổ Hà Nội. Thưởng thức phở, bún chả và xem múa rối nước để hiểu thêm văn hóa Thủ đô.', 'tour08.jpg'),
(209, 'Cát Bà Vịnh Lan Hạ', 'Kham pha', '3 ngay 2 dem', 'Hai Phong', 3900000, 'Xe du lich', 'Khám phá Cát Bà, tham quan Vịnh Lan Hạ, chèo kayak và thưởng thức hải sản Hải Phòng nổi tiếng trong không gian ven biển.', 'tour09.jpg'),
(210, 'Cần Thơ Chợ Nổi Miền Tây', 'Nghi duong', '2 ngay 1 dem', 'Can Tho', 4000000, 'Bua toi', 'Trải nghiệm chợ nổi Cái Răng lúc sáng sớm, ghé thăm vườn trái cây và nhà cổ Bình Thủy. Tối đi du thuyền trên sông Hậu.', 'tour10.jpg'),
(211, 'Đà Nẵng City And Beach', 'Bien dao', '3 ngay 2 dem', 'Da Nang', 4100000, 'Buffet', 'Lịch trình kết hợp nghỉ dưỡng biển và vui chơi, tham quan Ngũ Hành Sơn, du ngoạn cầu tình yêu và ngắm thành phố về đêm.', 'tour11.jpg'),
(212, 'Huế Hoài Niệm Kinh Kỳ', 'Van hoa', '2 ngay 1 dem', 'Hue', 4200000, 'Check-in dia danh', 'Hành trình đến các điểm di sản, ghé chợ Đông Ba và học làm bánh Huế. Phù hợp du khách muốn tìm hiểu nét đẹp cố đô.', 'tour12.jpg'),
(213, 'Hội An Lựa Chọn Tự Do', 'Kham pha', '3 ngay 2 dem', 'Hoi An', 4300000, 'Lich trinh linh hoat', 'Khách được lựa chọn lịch trình theo sở thích: phố cổ, biển Cửa Đại, rừng dừa, hoặc tham quan làng nghề truyền thống.', 'tour13.jpg'),
(214, 'Đà Lạt Tình Yêu Xanh', 'Nghi duong', '2 ngay 1 dem', 'Da Lat', 4400000, 'Khach san trung tam', 'Tận hưởng không khí lạnh, tham quan Hồ Xuân Hương, chợ Đà Lạt và vườn dâu. Tối BBQ ngoài trời tại homestay sân vườn.', 'tour14.jpg'),
(215, 'Nha Trang Hòn Chồng Night', 'Bien dao', '3 ngay 2 dem', 'Nha Trang', 4500000, 'Tam bien', 'Chuỗi hoạt động biển phong phú: mô tô nước, check-in Hòn Chồng, thưởng thức bữa tối hải sản và dạo phố đêm Trần Phú.', 'tour15.jpg'),
(216, 'Phú Quốc Luxury Escape', 'Cao cap', '4 ngay 3 dem', 'Phu Quoc', 4600000, 'Cap treo', 'Tour cao cấp với phòng hướng biển, đưa đón sân bay, tham quan nam đảo, cáp treo vượt biển và bữa tối set menu.', 'tour16.jpg'),
(217, 'Quy Nhơn Ghềnh Ráng Trip', 'Nghi duong', '3 ngay 2 dem', 'Quy Nhon', 4700000, 'Xe cao cap', 'Tham quan Tháp Đôi, Ghềnh Ráng, bãi tắm đẹp và khu đá Trứng. Lịch trình nhẹ nhàng phù hợp gia đình và nhóm bạn.', 'tour17.jpg'),
(218, 'Hà Nội Bát Tràng Story', 'Van hoa', '2 ngay 1 dem', 'Ha Noi', 4800000, 'Khach san 4 sao', 'Ghé thăm làng gốm Bát Tràng, bảo tàng dân tộc học, thưởng thức cà phê trứng và chụp ảnh phố cổ vào buổi sáng sớm.', 'tour18.jpg'),
(219, 'Hải Phòng Đồ Sơn Cát Hải', 'Kham pha', '3 ngay 2 dem', 'Hai Phong', 4900000, 'An trua', 'Lịch trình đến Đồ Sơn và Cát Hải, trải nghiệm đời sống ngư dân địa phương, thưởng thức bánh đa cua và hải sản tươi.', 'tour19.jpg'),
(220, 'Miền Tây Cồn Sơn Melody', 'Nghi duong', '2 ngay 1 dem', 'Can Tho', 5000000, 'An toi', 'Du ngoạn miền Tây với tài nghe đờn ca tài tử, tham quan Cồn Sơn, vườn trái cây và thưởng thức đặc sản bánh xèo.', 'tour20.jpg');

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

INSERT INTO `tbltourreviews` (`PackageId`, `UserEmail`, `Rating`, `Note`) VALUES
(201, 'user01@gmail.com', 5, 'Tour rất tuyệt, hướng dẫn viên nhiệt tình.'),
(201, 'user02@gmail.com', 4, 'Dịch vụ ổn, khách sạn đúng mô tả.'),
(201, 'user03@gmail.com', 5, 'Cảnh đẹp, lịch trình hợp lý.'),
(202, 'user04@gmail.com', 3, 'Tôi sẽ quay lại lần nữa!'),
(202, 'user05@gmail.com', 4, 'Giá hợp lý so với chất lượng.'),
(202, 'user06@gmail.com', 5, 'Xe đưa đón đúng giờ, ăn uống ngon.'),
(203, 'user07@gmail.com', 4, 'Một vài điểm chưa như mong đợi nhưng nhìn chung ổn.'),
(203, 'user08@gmail.com', 3, 'Phù hợp gia đình, trẻ em vui lắm.'),
(203, 'user09@gmail.com', 5, 'Thời tiết đẹp, chụp ảnh rất đã.'),
(204, 'user10@gmail.com', 4, 'Hơi vội ở một số điểm nhưng vẫn đáng đi.'),
(204, 'user11@gmail.com', 5, 'Đặt tour qua web rất tiện.'),
(204, 'user12@gmail.com', 4, 'Nhóm đông nhưng vẫn được tổ chức tốt.'),
(205, 'user13@gmail.com', 3, 'Ẩm thực địa phương rất ngon.'),
(205, 'user14@gmail.com', 5, 'View đẹp, phòng sạch sẽ.'),
(205, 'user15@gmail.com', 4, 'Chương trình văn hóa thú vị.'),
(206, 'user16@gmail.com', 5, 'Biển trong xanh, tắm rất thích.'),
(206, 'user17@gmail.com', 3, 'Resort sang, nhân viên chuyên nghiệp.'),
(206, 'user18@gmail.com', 4, 'Đường đi hơi xa nhưng xứng đáng.'),
(207, 'user19@gmail.com', 5, 'Chợ nổi rất đặc sắc.'),
(207, 'user20@gmail.com', 4, 'Homestay ấm cúng, chủ nhà thân thiện.'),
(207, 'user01@gmail.com', 5, 'Tour rất tuyệt, hướng dẫn viên nhiệt tình.'),
(208, 'user02@gmail.com', 4, 'Dịch vụ ổn, khách sạn đúng mô tả.'),
(208, 'user03@gmail.com', 5, 'Cảnh đẹp, lịch trình hợp lý.'),
(208, 'user04@gmail.com', 3, 'Tôi sẽ quay lại lần nữa!'),
(209, 'user05@gmail.com', 4, 'Giá hợp lý so với chất lượng.'),
(209, 'user06@gmail.com', 5, 'Xe đưa đón đúng giờ, ăn uống ngon.'),
(209, 'user07@gmail.com', 4, 'Một vài điểm chưa như mong đợi nhưng nhìn chung ổn.'),
(210, 'user08@gmail.com', 3, 'Phù hợp gia đình, trẻ em vui lắm.'),
(210, 'user09@gmail.com', 5, 'Thời tiết đẹp, chụp ảnh rất đã.'),
(210, 'user10@gmail.com', 4, 'Hơi vội ở một số điểm nhưng vẫn đáng đi.'),
(211, 'user11@gmail.com', 5, 'Đặt tour qua web rất tiện.'),
(211, 'user12@gmail.com', 4, 'Nhóm đông nhưng vẫn được tổ chức tốt.'),
(211, 'user13@gmail.com', 3, 'Ẩm thực địa phương rất ngon.'),
(212, 'user14@gmail.com', 5, 'View đẹp, phòng sạch sẽ.'),
(212, 'user15@gmail.com', 4, 'Chương trình văn hóa thú vị.'),
(212, 'user16@gmail.com', 5, 'Biển trong xanh, tắm rất thích.'),
(213, 'user17@gmail.com', 3, 'Resort sang, nhân viên chuyên nghiệp.'),
(213, 'user18@gmail.com', 4, 'Đường đi hơi xa nhưng xứng đáng.'),
(213, 'user19@gmail.com', 5, 'Chợ nổi rất đặc sắc.'),
(214, 'user20@gmail.com', 4, 'Homestay ấm cúng, chủ nhà thân thiện.'),
(214, 'user01@gmail.com', 5, 'Tour rất tuyệt, hướng dẫn viên nhiệt tình.'),
(214, 'user02@gmail.com', 4, 'Dịch vụ ổn, khách sạn đúng mô tả.'),
(215, 'user03@gmail.com', 5, 'Cảnh đẹp, lịch trình hợp lý.'),
(215, 'user04@gmail.com', 3, 'Tôi sẽ quay lại lần nữa!'),
(215, 'user05@gmail.com', 4, 'Giá hợp lý so với chất lượng.'),
(216, 'user06@gmail.com', 5, 'Xe đưa đón đúng giờ, ăn uống ngon.'),
(216, 'user07@gmail.com', 4, 'Một vài điểm chưa như mong đợi nhưng nhìn chung ổn.'),
(216, 'user08@gmail.com', 3, 'Phù hợp gia đình, trẻ em vui lắm.'),
(217, 'user09@gmail.com', 5, 'Thời tiết đẹp, chụp ảnh rất đã.'),
(217, 'user10@gmail.com', 4, 'Hơi vội ở một số điểm nhưng vẫn đáng đi.'),
(217, 'user11@gmail.com', 5, 'Đặt tour qua web rất tiện.'),
(218, 'user12@gmail.com', 4, 'Nhóm đông nhưng vẫn được tổ chức tốt.'),
(218, 'user13@gmail.com', 3, 'Ẩm thực địa phương rất ngon.'),
(218, 'user14@gmail.com', 5, 'View đẹp, phòng sạch sẽ.'),
(219, 'user15@gmail.com', 4, 'Chương trình văn hóa thú vị.'),
(219, 'user16@gmail.com', 5, 'Biển trong xanh, tắm rất thích.'),
(219, 'user17@gmail.com', 3, 'Resort sang, nhân viên chuyên nghiệp.'),
(220, 'user18@gmail.com', 4, 'Đường đi hơi xa nhưng xứng đáng.'),
(220, 'user19@gmail.com', 5, 'Chợ nổi rất đặc sắc.'),
(220, 'user20@gmail.com', 4, 'Homestay ấm cúng, chủ nhà thân thiện.');
