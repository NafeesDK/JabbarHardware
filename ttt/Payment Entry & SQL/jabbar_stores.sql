-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2025 at 01:39 AM
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
-- Database: `jabbar_stores`
--

-- --------------------------------------------------------

--
-- Table structure for table `c.l.details`
--

CREATE TABLE `c.l.details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `c.l.details`
--

INSERT INTO `c.l.details` (`id`, `user_id`, `full_name`, `address`, `city`, `phone_no`, `created_at`) VALUES
(1, 12, 'tony stank', '123,somewhere', 'Kalmunai', '0710946094', '2025-04-21 17:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `carddetails`
--

CREATE TABLE `carddetails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cardholderName` varchar(255) NOT NULL,
  `cardNumber` varchar(255) NOT NULL,
  `expiryDate` varchar(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carddetails`
--

INSERT INTO `carddetails` (`id`, `user_id`, `cardholderName`, `cardNumber`, `expiryDate`, `created_at`) VALUES
(1, 12, 'tony stank', '************6981', '06/31', '2025-04-23 22:33:33');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(121, 12, 5, 1, '2025-04-23 21:38:50'),
(122, 12, 4, 2, '2025-04-23 21:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` enum('offer','regular') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `original_price`, `discounted_price`, `image_url`, `category`, `created_at`) VALUES
(1, 'Hammer', 'Durable claw hammer for construction tasks.', 1600.00, 1100.00, 'https://th.bing.com/th/id/R.81c223274dcbc4f8ae12dbb638470753?rik=vVlOjUkCQJl0%2bw&pid=ImgRaw&r=0', 'offer', '2025-03-07 20:37:36'),
(2, 'Screwdriver Set', 'Multi-head screwdriver set for all needs.', 2040.00, 1850.00, 'https://images.thdstatic.com/productImages/23307023-290f-4e58-8870-776f0de0cb6f/svn/stanley-screwdriver-sets-stht60084-64_600.jpg', 'offer', '2025-03-07 20:37:36'),
(3, 'Adjustable Wrench', 'Versatile wrench for various bolt sizes.', 7650.00, 6930.00, 'https://th.bing.com/th/id/R.9e1c59a72066465a76e15f38ee128979?rik=cxxjcChywt94Gg&pid=ImgRaw&r=0', 'offer', '2025-03-08 19:30:24'),
(4, 'Cordless Drill', 'Rechargeable drill for DIY projects.', 17300.00, 16900.00, 'https://th.bing.com/th/id/OIP.3MyvjEm26TRSjVU_1wKVLAHaGW?rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:31:39'),
(5, 'Tape Measure', '25-foot tape measure with lock mechanism.', 2200.00, 1850.00, 'https://th.bing.com/th/id/OIP.MOKs7gu1oKxREx5qTC3t4wHaHa?rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:32:12'),
(6, 'Utility Knife', 'Sharp retractable blade for cutting tasks.', 280.00, 240.00, 'https://th.bing.com/th/id/OIP.dE6XH4gHvp3-1psIwPuKnAHaHa?rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:32:26'),
(7, 'Pliers', 'Slip-joint pliers for gripping and bending.', 1200.00, 1000.00, 'https://th.bing.com/th/id/OIP.HcADGlWdo7tWojC69-7aPAHaHa?rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:32:42'),
(8, 'Level', '24-inch level for accurate measurements.', 2640.00, 2200.00, 'https://th.bing.com/th/id/OIP.cOfZYD2-pUS2TU0wYD8-AQHaHa?rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:33:03'),
(9, 'Socket Set', '40-piece socket set for automotive repairs.', 3200.00, 2700.00, 'https://i5.walmartimages.com/asr/a982272a-e59a-4ff5-a44f-b2f445779c96_2.670322bf5e316141dd60e9191c97e7a6.jpeg?odnWidth=1000&odnHeight=1000&odnBg=ffffff', 'offer', '2025-03-08 19:33:35'),
(10, 'Power Saw', 'Electric saw for cutting wood and metal.', 28400.00, 24300.00, 'https://th.bing.com/th/id/OIP.3bR9fpj3qKN8nNCJ8gGXRwHaHa?w=660&h=660&rs=1&pid=ImgDetMain', 'offer', '2025-03-08 19:34:06'),
(11, 'Chisel Set', '6-piece chisel set for woodworking.', 4110.00, NULL, 'https://th.bing.com/th/id/R.7d600ef4f505db9386a38a881d602cac?rik=N6MDIgqCKCpXlA&riu=http%3a%2f%2fecx.images-amazon.com%2fimages%2fI%2f418kjbu6oUL.jpg&ehk=iDVg33FNdiXClUebsQ2Z%2ffysL5lzlS%2f7tJLVeB0qRyA%3d&risl=&pid=ImgRaw&r=0', 'regular', '2025-03-08 19:34:36'),
(12, 'Tool Box', 'Portable toolbox with compartments.', 7550.00, NULL, 'https://th.bing.com/th/id/OIP.OUdtNBnUW3BugX7iPzmiXgHaHa?w=800&h=800&rs=1&pid=ImgDetMain', 'regular', '2025-03-08 19:34:52'),
(13, 'Safety Glasses', 'Protective eyewear for construction work.', 900.00, NULL, 'https://th.bing.com/th/id/OIP.MjiNP6Kb4-gT5v-5bOQsqQHaHa?rs=1&pid=ImgDetMain', 'regular', '2025-03-08 19:35:06'),
(14, 'Work Gloves', 'Heavy-duty gloves for hand protection.', 400.00, NULL, 'https://www.b-id.co.uk/media/facebook/4c5f7ac3-d6fb-11ee-bdd6-fa163e5e3817.jpg', 'regular', '2025-03-08 19:35:18'),
(15, 'Sledgehammer', 'Heavy-duty hammer for demolition tasks.', 8500.00, NULL, 'https://www.brights.co.za/cdn-cgi/image/width=600,quality=60,format=auto,onerror=redirect/wp-content/uploads/2020/11/15080_HAMMER-SLEDGE-UNBREAKABLE-6.3KG.jpg', 'regular', '2025-03-08 19:38:55'),
(16, 'Circular Saw', 'High-power saw for precise cutting.', 2200.00, NULL, 'https://th.bing.com/th/id/OIP.3tg9Dw9MYb1M3R1fA0BetQHaHa?rs=1&pid=ImgDetMain', 'regular', '2025-03-08 19:39:04'),
(17, 'Allen Wrench Set', 'Hex key set for assembling furniture.', 6000.00, NULL, 'https://th.bing.com/th/id/OIP.qdIoY_GNQ7O6Al6OpPfldgHaHa?w=480&h=480&rs=1&pid=ImgDetMain', 'regular', '2025-03-08 19:39:14'),
(18, 'Stud Finder', 'Wall scanner for locating studs and wires.', 2800.00, NULL, 'https://th.bing.com/th/id/OIP.Q2lRmByCKfP5rUaWFohtlQHaHa?rs=1&pid=ImgDetMain', 'regular', '2025-03-08 19:39:42'),
(19, 'Paint Roller', 'Roller with ergonomic handle for painting.', 1800.00, NULL, 'https://www.toolstop.com.my/wp-content/uploads/2021/03/09193.jpg', 'regular', '2025-03-08 19:39:52'),
(20, 'Drill Bit Set', '20-piece set for drilling various materials.', 2750.00, NULL, 'https://www.stanleytools.ie/EMEA/PRODUCT/IMAGES/HIRES/STA88550/STA88550_P2.jpg?resize=530x530', 'regular', '2025-03-08 19:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'Hajith Nafees', '1027629@bcas.ac', '$2y$10$5s/38PG5dVEziB/eOchpW.wWnPavBSUW11yB.FSYB93mIjCkUcc1W', '2025-03-29 20:37:30'),
(2, 'Stark Junior', '1027628@bcas.ac', '$2y$10$Etnak5L8X7rohYCO1T8f5uB8XV0HqIFOKayvzwPq.j4KKXaqsf1yO', '2025-03-29 20:56:53'),
(11, 'Zeyn Ezdan', 'zeyn@gmail.com', '$2y$10$KdNErRRJgCtqHueXw.Rl0urzZqwNCDhctGS73YA5rAxVEdYoYLv7S', '2025-04-03 16:25:33'),
(12, 'Tony Stank', 'tonystank@gmail.com', '$2y$10$C9Cw4cL00jAjFxkB3igGauTrzFJP201f6g36aXBB2qRd.SrzyZPpa', '2025-04-03 16:26:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `c.l.details`
--
ALTER TABLE `c.l.details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carddetails`
--
ALTER TABLE `carddetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `c.l.details`
--
ALTER TABLE `c.l.details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carddetails`
--
ALTER TABLE `carddetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carddetails`
--
ALTER TABLE `carddetails`
  ADD CONSTRAINT `carddetails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
