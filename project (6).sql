-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 04, 2023 at 06:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`category_id`, `category_name`, `category_description`) VALUES
(1, 'Bearings', 'Different types of Bearings'),
(2, 'Lubricant', 'Lubricant'),
(3, 'Packaging', 'Different types of packaging'),
(4, 'Maintenance Products', 'Different types of maintenance products'),
(18, 'Others', 'Other categories of products');

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `comment_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_content` text DEFAULT NULL,
  `comment_date` datetime DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `is_disemvoweled` tinyint(1) DEFAULT 0,
  `original_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Comments`
--

INSERT INTO `Comments` (`comment_id`, `username`, `product_id`, `user_id`, `comment_content`, `comment_date`, `is_hidden`, `is_disemvoweled`, `original_comment`) VALUES
(1, 'Tingting', 71, NULL, 'I Like this product！', '2023-11-22 11:24:33', 0, 0, NULL),
(4, 'Alex', 73, NULL, 'Good Item！', '2023-11-22 11:44:24', 0, 0, 'Good Item！'),
(6, 'Zhan', 72, NULL, 'Nice.', '2023-11-23 16:20:40', 0, 0, 'Nice.'),
(7, 'Hong', 71, NULL, 'I will buy it again!', '2023-11-23 20:46:01', 0, 0, NULL),
(8, 'Wei', 71, NULL, 'Good value for money.', '2023-11-23 22:57:46', 0, 0, 'Good value for money.'),
(27, 'Anonymous User', 71, NULL, 'well', '2023-12-03 14:28:18', 0, 0, NULL),
(30, 'Wei', 71, NULL, '1', '2023-12-03 18:18:43', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

CREATE TABLE `Images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `upload_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Images`
--

INSERT INTO `Images` (`id`, `product_id`, `filename`, `upload_time`) VALUES
(48, 83, 'pump.jpg', '2023-12-03 02:34:11'),
(49, 71, 'thrust.jpg', '2023-12-03 02:35:01'),
(50, 94, 'lubricant.jpg', '2023-12-03 02:36:26'),
(51, 78, 'kraftpaper.jpg', '2023-12-03 02:36:52'),
(52, 77, 'lubricantcontainer.jpg', '2023-12-03 02:37:19'),
(53, 84, 'jawpuller.jpg', '2023-12-03 02:37:41'),
(54, 80, 'barrel.jpg', '2023-12-03 02:38:11'),
(55, 79, 'plasticpaper.jpg', '2023-12-03 02:38:31'),
(56, 73, 'spherical.jpg', '2023-12-03 02:39:06'),
(57, 74, 'steelplain.jpg', '2023-12-03 02:39:28'),
(58, 72, 'tapered.jpg', '2023-12-03 02:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`product_id`, `product_name`, `product_description`, `product_price`, `stock_quantity`, `category_id`) VALUES
(71, 'Thrust Ball Bearings', '<p>Single and double direction thrust ball bearings are used to accommodate pure axial loads, mostly in low-speed applications. Though their design is rather simple, these robust bearings can provide long, trouble-free service life. All Naian thrust ball bearings are produced to the same high quality standards in order to fulfil the requirements of the application. Thrust ball bearings are separable so that pressed steel, machined brass or machined steel cages.</p>', 0.50, 1000000, 1),
(72, 'Tapered Bearings', '<p>Naian tapered roller bearings are designed to meet and exceed the quality and performance requirements of applications where there are heavy combined loads and tilting moments. The logarithmic profile of the rollers provides superior load distribution. The design of the flange/roller-end contact area reduces edge loading and promotes the formation of a lubricant film even under arduous operating conditions. The optimized finish of all contact surfaces maximizes the effectiveness of the lubricant. These features significantly decrease noise, temperature and vibration levels, and virtually eliminate temperature peaks during a typical start-up period. Naian tapered roller bearings are proven to increase uptime, while decreasing maintenance and operating costs.</p>', 0.60, 150000, 1),
(73, 'Spherical Bearings', '<p>Spherical Plain Bearings, also known as spherical plain or plain spherical bearings, are essential components in mechanical systems that require pivotal movement along non-rotating axes. These bearings facilitate smooth and controlled motion in applications where angular misalignment, tilting, or oscillating movement is essential. With their self-lubricating design, Spherical Plain Bearings minimize friction and wear, contributing to prolonged equipment life and reduced maintenance requirements. At Naian Bearing, our Spherical Plain Bearings are engineered to precision, adhering to international quality standards. Our commitment to excellence is reflected in the design, manufacturing processes, and materials used in crafting these bearings.</p>', 0.65, 100000, 1),
(74, 'Steelplain Bearings', '<p>Naian steelplain bearings are initially lubricated and sealed to eliminate the need for relubrication in applications with low to moderate levels of contamination, such as those in off-highway applications. This generates significant savings by reducing maintenance costs and grease consumption. These virtually maintenance-free bearings also improve reliability by eliminating failures due to missed lubrication intervals and improper lubrication practices. All of this adds up to reduced Total Cost of Ownership (TCO). The extensive laboratory and field tests showed that the relubrication-free Naian steel/steel plain bearings last significantly longer than the conventional bearings, even when they were frequently relubricated.</p>', 0.90, 85000, 1),
(77, 'Lubricant Container', '<p>Nian Lubricant Containers are sturdy, practical containers designed to store and distribute lubricants efficiently. These containers are made from materials such as high-density polyethylene to withstand the chemical makeup of the lubricant, ensuring long-term durability. They are cylindrical in shape and have safety caps for a tight seal. Containers are clearly labeled with lubricant type, viscosity grade and usage instructions to meet the needs of retail consumers and industrial customers. This container prioritizes ease of use and safety, preventing spills and contamination during lubrication.</p>', 20.00, 200, 3),
(78, 'Kraft Paper Packaging', '<p>Kraft paper packaging for bearings is an environmentally friendly and sturdy solution that provides protection and sustainability. This type of packaging usually uses multiple layers of thick kraft paper to wrap the bearings in cushioning to prevent shock and vibration during transportation. Kraft paper\'s high tensile strength ensures it resists tears and punctures, while its elasticity allows it to accommodate a variety of bearing sizes without the risk of breakage. Packaging is typically sealed with heavy-duty tape and clearly labeled with handling and installation instructions, ensuring the bearings arrive in optimal condition and ready for use.</p>', 100.00, 100, 3),
(79, 'Plastic Paper Packaging', '<p>Bearing plastic packaging offers a modern and highly protective solution, utilizing the transparency and elasticity of plastic to protect the contents. This packaging is usually made of polyethylene, a durable clear plastic material that allows visual inspection of the bearings without opening the packaging. The conformable nature of the plastic packaging ensures that the bearings remain in place, reducing the risk of damage due to movement during shipping and handling. Additionally, the moisture-resistant properties of the plastic help prevent bearing corrosion, while the sealed design prevents contaminants from entering.</p>', 150.00, 100, 3),
(80, 'Plastic Barrel', '<p>The plastic barrel used to package bearings is a sturdy container designed for the safe transportation and storage of loose bearings. Made from high-density polyethylene (HDPE), these cartridges are engineered to withstand the rigors of industrial handling, focusing on impact resistance and durability. The cylindrical design maximizes space efficiency, allowing an optimal number of bearings to be stored within it. These  barrels are equipped with air-tight covers to ensure a moisture-free environment and protect the bearings from rust and environmental contaminants. The smooth inner surface of the cylinder minimizes wear and protects the integrity of the bearing during movement. Plastic barrels are the first choice for manufacturers who prioritize safe distribution of their products.</p>', 0.30, 200000, 3),
(83, 'Hydraulic Pump', '<p><strong><u>The hydraulic pump is crucial in a hydraulic system, indirectly ensuring the proper functioning of bearings. It converts mechanical to hydraulic energy, powering hydraulic components. For bearings, it aids in precise positioning and applying preload, delivers lubrication, transmits power, and helps prevent overload by regulating system pressure to protect the bearings from excessive forces.</u></strong></p>', 270.00, 22, 4),
(84, 'Mechanical Jaw Puller', '<p>A mechanical jaw puller for bearings is a tool commonly used to remove fasteners or bearings. This tool is designed with two or more \"jaws\" that can be adjusted to accommodate different sized bearings. When working, the claws are placed under the inner or outer ring of the bearing. By rotating the handle of the puller or using impact force, the claws grip the bearing tightly and then pull it out from the shaft. Claw pullers are hydraulic and provide greater force for disassembly. The advantages of a claw puller include its adjustability, ability to accommodate a variety of bearing sizes, and ability to evenly distribute extraction force, reducing damage to the bearing. Proper use of pullers ensures safe bearing removal and extends the service life of bearings and equipment.</p>', 399.00, 10, 4),
(94, 'Lubricant', '<p>Naian lubricants are designed for your needs and tested for performance in real applications. Our experience with bearings, lubricants and applications helps us to offer the right lubricants for your applications and improve your overall lubrication scheme. Testing and validating each production batch is our way to offer excellent lubricant quality around the globe. Specialized lubricant tests and continuous research in the field of lubrication allows naian to further optimize our knowledge and support you with right lubrication for your machines.</p>', 60.00, 100, 2),
(117, 'New', '<p>1</p>', 1.00, 1, 18);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `user_name`, `user_password`, `role`) VALUES
(1, 'tingting', '$2y$10$E.wLiZrjI2r3y.E54XzFMevJnXiPPVpH0TVNUCNeRh8ApBMytxwom', 'admin'),
(2, 'hongzhan', '$2y$10$elSVLE1o2jFVS2NDlUda..2yfWJgPUadfe7Jws5fM3aE0rORtiATS', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `Comments`
--
ALTER TABLE `Comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Images`
--
ALTER TABLE `Images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Categories`
--
ALTER TABLE `Categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `Images`
--
ALTER TABLE `Images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Comments`
--
ALTER TABLE `Comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Images`
--
ALTER TABLE `Images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Products`
--
ALTER TABLE `Products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
