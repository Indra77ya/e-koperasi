-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2025 at 02:34 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-koperasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nik` bigint(20) NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenkel` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `nik`, `nama`, `email`, `no_hp`, `jenkel`, `agama`, `pekerjaan`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `created_at`, `updated_at`) VALUES
(1, 4145090226886023, 'Mylene', 'jannie.pfeffer@example.org', '+9745435089405', 'L', 'Islam', 'Transit Police OR Railroad Police', '52218 Schuster Curve\nSouth Maximillian, CA 54853-0031', 'Lake Christophemouth', '2021-06-08', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(2, 9478034728070524, 'Joanie', 'rkertzmann@example.org', '+7483801104064', 'L', 'Islam', 'Brattice Builder', '953 Grant Junctions Apt. 090\nLake Isaias, WY 10573', 'Port Ernestoborough', '2023-10-04', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(3, 3343443871309230, 'Harold', 'rowland.donnelly@example.com', '+8633201184714', 'L', 'Islam', 'Mechanical Inspector', '1147 Rickie Squares Apt. 857\nNew Marc, NH 95658', 'New Alfred', '1982-07-31', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(4, 5680886616567340, 'Madyson', 'river.blick@example.com', '+6378565575104', 'L', 'Islam', 'Architecture Teacher', '380 Zola Lodge Suite 161\nSimonisfurt, DC 17045', 'West Yasmineport', '1971-06-29', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(5, 5165303639900807, 'Maurine', 'gruecker@example.com', '+1686771716344', 'L', 'Islam', 'Insurance Sales Agent', '1521 Rogahn Common Suite 640\nWest Laverneland, CO 54182', 'Littelton', '1989-11-21', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(6, 2450102887258420, 'Edyth', 'kathlyn.stehr@example.org', '+6057854609887', 'L', 'Islam', 'Machine Operator', '246 Catherine Gardens\nErnaton, MA 01648-1225', 'Feltonborough', '2022-11-27', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(7, 8566986635752291, 'Irving', 'phackett@example.org', '+4873777967797', 'L', 'Islam', 'Fabric Mender', '34326 Bradtke Parks\nBeiermouth, RI 21083', 'North Kieran', '2006-08-17', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(8, 7861524365550705, 'Aurelie', 'alex.casper@example.org', '+8016038394003', 'L', 'Islam', 'Geographer', '166 Ethyl Fork Apt. 873\nNorth Kenyattaview, PA 83904', 'North Tyriqueside', '1991-06-21', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(9, 9519856639557880, 'Lavina', 'wava11@example.org', '+5827622726778', 'L', 'Islam', 'Chemical Plant Operator', '7301 Randi Lane Apt. 771\nPort Jalenview, VT 34692', 'Gutmannmouth', '2008-01-31', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(10, 6981558140265421, 'Randal', 'coralie08@example.net', '+4662411024669', 'L', 'Islam', 'Nuclear Engineer', '280 Heidenreich Mall Apt. 467\nMerlview, CT 90568-8597', 'Littelberg', '1983-12-27', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(11, 5460554982504996, 'Pete', 'yost.delta@example.com', '+6148137801107', 'L', 'Islam', 'Plating Operator OR Coating Machine Operator', '113 Jack Ville Suite 950\nEast Kira, VA 31389', 'Leuschkeland', '2001-08-14', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(12, 8307873323331522, 'Tracey', 'tyra.corwin@example.net', '+8327104540305', 'L', 'Islam', 'Clerk', '136 Lilliana Rapid Apt. 142\nAdrainbury, WY 14709', 'Rippinport', '2017-03-28', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(13, 5823433186631404, 'Queen', 'stehr.aron@example.org', '+2248101770064', 'L', 'Islam', 'Personal Home Care Aide', '5462 Heidenreich Way Apt. 692\nLeannonfort, IA 75684-8426', 'Lake Darioton', '2011-03-07', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(14, 4117214812618468, 'Lenora', 'ali.little@example.org', '+4081757295301', 'L', 'Islam', 'Central Office and PBX Installers', '4836 Janiya Lock Suite 798\nWuckertport, OR 34478', 'South Henriettestad', '2012-05-05', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(15, 1667694211261062, 'Fern', 'jailyn85@example.com', '+9136390846932', 'L', 'Islam', 'Truck Driver', '239 Gretchen Wall\nMalindafurt, MS 85814-4076', 'Aracelyfort', '2023-03-04', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(16, 6873494863477911, 'Alda', 'angelita.bernier@example.org', '+6285605058303', 'L', 'Islam', 'Bellhop', '6431 Donato Valleys\nStanleyshire, TN 70508', 'Samsonville', '1985-02-28', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(17, 8222178654927367, 'Alfredo', 'gking@example.net', '+9125955524078', 'L', 'Islam', 'Chemical Equipment Tender', '38621 Lelah Cliff\nCadechester, OK 22093', 'East Janaeshire', '1980-02-28', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(18, 5216085896548521, 'Michael', 'lakin.ewell@example.net', '+6236560352098', 'L', 'Islam', 'Armored Assault Vehicle Officer', '692 Harber Haven Suite 869\nShaniaview, MD 03457', 'North Norbert', '1977-07-19', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(19, 1504311549890477, 'Dena', 'cstiedemann@example.com', '+5493272221525', 'L', 'Islam', 'Bellhop', '85382 Garnet Valleys\nEbertborough, DC 55441-5660', 'Zemlakland', '1977-07-25', '2025-11-26 03:36:01', '2025-11-26 03:36:01'),
(20, 6044183489458515, 'Manuel', 'boyer.graciela@example.com', '+7593041241445', 'L', 'Islam', 'Forest and Conservation Technician', '9774 Damien Highway\nLarissaton, GA 05327', 'East Avaport', '1981-03-31', '2025-11-26 03:36:01', '2025-11-26 03:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `bunga_tabungan`
--

CREATE TABLE `bunga_tabungan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `bulan` int(11) DEFAULT NULL,
  `saldo_terendah` bigint(20) DEFAULT NULL,
  `suku_bunga` tinyint(4) DEFAULT NULL,
  `nominal_bunga` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_02_07_082604_create_anggota_table', 1),
(4, '2019_02_07_082623_create_tabungan_table', 1),
(5, '2019_02_07_082624_create_setoran_table', 1),
(6, '2019_02_07_082626_create_penarikan_table', 1),
(7, '2019_02_07_082724_create_riwayat_tabungan_table', 1),
(8, '2019_02_07_082725_create_bunga_tabungan_table', 1),
(9, '2019_02_09_093543_add_tahun_to_bunga_tabungan_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penarikan`
--

CREATE TABLE `penarikan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `biaya_administrasi` int(11) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_tabungan`
--

CREATE TABLE `riwayat_tabungan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debet` bigint(20) DEFAULT NULL,
  `kredit` bigint(20) DEFAULT NULL,
  `saldo` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setoran`
--

CREATE TABLE `setoran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tabungan`
--

CREATE TABLE `tabungan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `saldo` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Petugas E-Koperasi', 'ekoperasi@gmail.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '2NvKnOirfYNXCkAEjSLfbSdVwri2n0WxLcjqSkhDgVWIjHR6H8VMPCAK1Mtn', '2025-11-26 03:36:00', '2025-11-26 03:36:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anggota_nik_unique` (`nik`);

--
-- Indexes for table `bunga_tabungan`
--
ALTER TABLE `bunga_tabungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bunga_tabungan_anggota_id_foreign` (`anggota_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `penarikan`
--
ALTER TABLE `penarikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penarikan_anggota_id_foreign` (`anggota_id`);

--
-- Indexes for table `riwayat_tabungan`
--
ALTER TABLE `riwayat_tabungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `riwayat_tabungan_anggota_id_foreign` (`anggota_id`);

--
-- Indexes for table `setoran`
--
ALTER TABLE `setoran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setoran_anggota_id_foreign` (`anggota_id`);

--
-- Indexes for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tabungan_anggota_id_foreign` (`anggota_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bunga_tabungan`
--
ALTER TABLE `bunga_tabungan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `penarikan`
--
ALTER TABLE `penarikan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_tabungan`
--
ALTER TABLE `riwayat_tabungan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setoran`
--
ALTER TABLE `setoran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabungan`
--
ALTER TABLE `tabungan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bunga_tabungan`
--
ALTER TABLE `bunga_tabungan`
  ADD CONSTRAINT `bunga_tabungan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penarikan`
--
ALTER TABLE `penarikan`
  ADD CONSTRAINT `penarikan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_tabungan`
--
ALTER TABLE `riwayat_tabungan`
  ADD CONSTRAINT `riwayat_tabungan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setoran`
--
ALTER TABLE `setoran`
  ADD CONSTRAINT `setoran_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD CONSTRAINT `tabungan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
