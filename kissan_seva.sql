-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2026 at 10:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kissan_seva`
--

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `SNo` int(11) NOT NULL,
  `Centre` varchar(20) NOT NULL,
  `Location` varchar(30) NOT NULL,
  `MobileNo` varchar(12) NOT NULL,
  `Date` varchar(30) NOT NULL,
  `FarmerId` varchar(10) DEFAULT NULL,
  `TotalQuantity` int(11) NOT NULL,
  `RemainingQuantity` int(11) NOT NULL,
  `Longitude` decimal(11,8) DEFAULT NULL,
  `Status` char(1) NOT NULL DEFAULT 'N',
  `Latitude` decimal(10,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`SNo`, `Centre`, `Location`, `MobileNo`, `Date`, `FarmerId`, `TotalQuantity`, `RemainingQuantity`, `Longitude`, `Status`, `Latitude`) VALUES
(1, 'Civil Supplies Offic', 'Warangal/HanamKonda', '7894561230', 'Sep 24, 2026', NULL, 5000, 400, 79.55429800, 'N', 17.99742100),
(2, 'District Cooperative', 'Warangal', '9876543210', 'Sep 15, 2026', NULL, 5000, 2200, 79.55429800, 'N', 17.99742100),
(3, 'Enumamula Agricultur', 'Sundaraiah Nagar, Grain Market', '8676955345', 'Sep 30, 2026', NULL, 5000, 4700, 79.61718000, 'N', 18.00006000),
(4, ' Agricultural Market', 'Godown-12, Grain Market, Balaj', '9657498434', 'Sep 19, 2026', NULL, 5000, 3000, 79.63060000, 'N', 17.99140000),
(5, 'Jajan Pry Health Cen', 'Bharatpur - I, Murshidabad, We', '8145236013', 'Sep 20, 2026', NULL, 5000, 0, 88.02458800, 'F', 23.89131000),
(6, 'Indpur Gfd Cpc', 'Indpur, Bankura, West Bengal', '7550815818', 'Sep 15, 2026', NULL, 5000, 5000, 86.93402100, 'N', 23.16443100),
(7, 'Ghughudanga Kharija ', 'Jalpaiguri, Jalpaiguri, West B', '9609202658', 'Sep 17, 2026', NULL, 5000, 5000, 88.74123300, 'N', 26.40381000),
(8, 'Mal Krishak Bazar', 'Kranti, Jalpaiguri, West Benga', '9635665432', 'Oct 01, 2026', NULL, 6000, 6000, 88.73754000, 'N', 26.86595000),
(9, 'Zilla Parishad Marke', 'Amta - II, Howrah, West Bengal', '8100432818', 'Oct 03, 2026', NULL, 4000, 4000, 87.90680600, 'N', 22.57250000),
(10, 'Ghughumari Patihati', 'Cooch Behar - I, Cooch Behar, ', '8653651817', 'Oct 07, 2026', NULL, 10000, 10000, 89.47025900, 'N', 26.28575200),
(11, 'NIT Warnagal', 'Hanamkonda', '9876543210', 'Sep 11, 2026', NULL, 8000, 8000, 79.52697200, 'N', 17.98831600),
(12, 'Satmile Bazar 2Nd Cp', 'Cooch Behar - I, Cooch Behar, ', '9641907348', '2026-09-25', NULL, 3500, 3500, 89.40000000, 'N', 26.30000000),
(13, 'Chandamari 2Nd Cpc', 'Cooch Behar - I, Cooch Behar, ', '9851539133', '2026-10-08', NULL, 6800, 6800, 89.32861500, 'N', 26.23202300),
(14, 'Khapaidanga Bazar', 'Cooch Behar - II, Cooch Behar,', '9733148912', '2026-09-18', NULL, 9000, 9000, 89.51567400, 'N', 26.34525100),
(15, 'Bolpur Krishak Bazar', 'Bolpur Sriniketan, Birbhum, We', '8967819712', '2026-11-03', NULL, 15000, 15000, 87.70411700, 'N', 23.66245100),
(16, 'Mayureswar-I Krishak', 'Mayureswar - I, Birbhum, West ', '7001978723', '2026-10-21', NULL, 5800, 5800, 87.77280000, 'N', 24.03810000),
(17, 'Shitaljore Primary H', 'Sonamukhi, Bankura, West Benga', '9614146276', '2026-12-05', NULL, 7000, 7000, 87.41000000, 'N', 23.30500000),
(18, 'Chhatna Krishak Baza', 'Chhatna, Bankura, West Bengal', '9064230799', '2026-09-29', NULL, 12500, 12500, 86.96690000, 'N', 23.31150000),
(19, 'Saltora Krishak Baza', 'Saltora, Bankura, West Bengal', '7384168949', '2026-10-14', NULL, 8500, 8500, NULL, 'N', NULL),
(20, 'Mahatpur Karmatirtha', 'Chapra, Nadia, West Bengal', '7001577714', '2026-11-18', NULL, 6000, 6000, NULL, 'N', NULL),
(21, 'Kaliganj Krishak Baz', 'Kaliganj, Nadia, West Bengal', '8972985387', '2026-09-16', NULL, 5000, 5000, NULL, 'N', NULL),
(22, 'Jahangirpur Krishak ', 'Krishnagar - I, Nadia, West Be', '9593582324', '2026-10-30', NULL, 5000, 5000, NULL, 'N', NULL),
(23, 'Bagmundi Krishak Baz', 'Bagmundi, Purulia, West Bengal', '9749086752', '2026-12-12', NULL, 9000, 9000, NULL, 'N', NULL),
(24, 'Hura Krishak Bazar', 'Hura, Purulia, West Bengal', '7384826168', '2026-09-23', NULL, 8800, 8800, NULL, 'N', NULL),
(25, 'Manbazar Krishak Baz', 'Manbazar - I, Purulia, West Be', '9002820208', '2026-11-09', NULL, 11000, 11000, NULL, 'N', NULL),
(26, 'Purbasthali Krishak ', 'Purbasthali - I, Purba Bardham', '8017809844', '2026-10-05', NULL, 0, 0, NULL, 'N', NULL),
(27, 'Bhatar Krishak Bazar', 'Bhatar, Purba Bardhaman, West ', '9748199667', '2026-11-27', NULL, 0, 0, NULL, 'N', NULL),
(28, 'Debsala Krishak Baza', 'Ausgram - II, Purba Bardhaman,', '8697455299', '2026-09-20', NULL, 0, 0, NULL, 'N', NULL),
(29, 'Dangapara Krishak Ba', 'Bamangola, Maldah, West Bengal', '8537067124', '2026-10-26', NULL, 0, 0, NULL, 'N', NULL),
(30, 'Kaliachak-Iii Krisha', 'Kaliachak - III, Maldah, West ', '7501464351', '2026-12-19', NULL, 0, 0, NULL, 'N', NULL),
(31, 'Old Malda Krishak Ba', 'Maldah (old), Maldah, West Ben', '9851887376', '2026-11-14', NULL, 0, 0, NULL, 'N', NULL),
(32, 'Falakata Krishak Baz', 'Falakata, Alipurduar, West Ben', '9875606476', '2026-10-11', NULL, 0, 0, NULL, 'N', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slot_bookings`
--

CREATE TABLE `slot_bookings` (
  `BookingId` int(11) NOT NULL,
  `SNo` int(11) NOT NULL,
  `FarmerId` varchar(8) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `BookingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `TokenNo` int(11) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slot_bookings`
--

INSERT INTO `slot_bookings` (`BookingId`, `SNo`, `FarmerId`, `Quantity`, `BookingDate`, `TokenNo`, `StartTime`, `EndTime`) VALUES
(2, 3, 'F1357', 300, '2026-09-12 17:40:17', 11, '09:00:00', '09:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `SNo` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `MobileNo` varchar(10) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `FarmerId` varchar(8) NOT NULL,
  `Crop` varchar(20) NOT NULL,
  `Quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`SNo`, `Name`, `MobileNo`, `Password`, `Location`, `FarmerId`, `Crop`, `Quantity`) VALUES
(1, 'Somiron Gogoi', '9395043599', '$2y$10$DoBNoeVBgW2SAhB7iYaJcuyHbj/lbfWO3reDOIs9wkXF1XFnrEqYS', 'Dibrugarh, Assam', 'F1357', 'Rice', 500);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`SNo`);

--
-- Indexes for table `slot_bookings`
--
ALTER TABLE `slot_bookings`
  ADD PRIMARY KEY (`BookingId`),
  ADD KEY `SNo` (`SNo`),
  ADD KEY `FarmerId` (`FarmerId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`SNo`),
  ADD UNIQUE KEY `unique_farmer_id` (`FarmerId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `SNo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `slot_bookings`
--
ALTER TABLE `slot_bookings`
  MODIFY `BookingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `SNo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `slot_bookings`
--
ALTER TABLE `slot_bookings`
  ADD CONSTRAINT `slot_bookings_ibfk_1` FOREIGN KEY (`SNo`) REFERENCES `slots` (`SNo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `slot_bookings_ibfk_2` FOREIGN KEY (`FarmerId`) REFERENCES `users` (`FarmerId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
