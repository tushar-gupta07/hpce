-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 08:25 AM
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
-- Database: `rkhospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `user_id`, `action`, `detail`, `ip`, `created_at`) VALUES
(1, 1, 'user_updated', 'Updated user: Admin (admin@gmail.com) role: editor', '::1', '2026-03-30 13:50:42'),
(2, 1, 'user_updated', 'Updated user: Admin (admin@gmail.com) role: editor', '::1', '2026-03-30 13:52:29'),
(3, 1, 'login', 'Login with 2FA captcha', '::1', '2026-03-30 15:52:12'),
(4, 1, 'logout', 'Admin logged out', '::1', '2026-03-30 15:54:25'),
(5, 1, 'login', 'Login with 2FA captcha', '::1', '2026-03-30 15:54:39'),
(6, 1, 'login', 'Login with 2FA captcha', '::1', '2026-03-31 11:28:45'),
(7, 2, 'user_created', 'Created user: Yash Chikahle (yashchikhale711@gmail.com) with role: admin', '::1', '2026-03-31 11:31:01'),
(8, 1, 'logout', 'Admin logged out', '::1', '2026-03-31 11:31:39'),
(9, 2, 'login', 'Login with 2FA captcha', '::1', '2026-03-31 11:32:10');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','editor','viewer') NOT NULL DEFAULT 'admin',
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `two_fa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `login_count` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `avatar`, `phone`, `status`, `two_fa_enabled`, `last_login`, `last_login_ip`, `login_count`, `notes`, `updated_at`, `reset_token`, `reset_expires`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$7LOSNnhH61YkPmfaA9J2gOD8Hr8qQ9hMtyvnkLgXZbCxAVij00dry', 'superadmin', NULL, '', 1, 1, '2026-03-31 11:28:45', '::1', 3, '', '2026-03-30 13:52:29', NULL, NULL, '2026-03-26 09:21:34'),
(2, 'Yash Chikahle', 'yashchikhale711@gmail.com', '$2y$10$LSy9tMTjoLHSOIpy5iq7CeV1Zk8EpDCJ9SR0hzo4Q4rf9wNed7xhu', 'admin', NULL, '9860303965', 1, 1, '2026-03-31 11:32:10', '::1', 1, 'Managing Doctors', '2026-03-31 11:31:01', NULL, NULL, '2026-03-31 06:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT 'assets/img/blog/blog-01.jpg',
  `image_alt` varchar(255) DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `tags` varchar(500) DEFAULT NULL COMMENT 'comma-separated tags',
  `views` int(10) UNSIGNED DEFAULT 0,
  `comments` int(10) UNSIGNED DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `published_at` date NOT NULL,
  `reading_time` tinyint(3) UNSIGNED DEFAULT NULL,
  `meta_title` varchar(70) DEFAULT NULL,
  `meta_description` varchar(180) DEFAULT NULL,
  `focus_keyword` varchar(100) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'article',
  `twitter_title` varchar(200) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_card` varchar(50) DEFAULT 'summary_large_image',
  `robots_meta` varchar(50) DEFAULT 'index,follow',
  `schema_type` varchar(50) DEFAULT 'BlogPosting',
  `schema_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `excerpt`, `content`, `image`, `image_alt`, `category_id`, `doctor_id`, `tags`, `views`, `comments`, `is_published`, `published_at`, `reading_time`, `meta_title`, `meta_description`, `focus_keyword`, `canonical_url`, `og_title`, `og_description`, `og_image`, `og_type`, `twitter_title`, `twitter_description`, `twitter_card`, `robots_meta`, `schema_type`, `schema_json`, `created_at`, `updated_at`) VALUES
(1, 'Joint Replacement Surgery: What to Expect Before & After', 'joint-replacement-surgery-what-to-expect', 'A complete guide to knee and hip replacement — preparation, procedure, recovery, and when to return to normal activity.', 'Joint replacement surgery is one of the most successful orthopedic procedures available today.', 'https://plus.unsplash.com/premium_photo-1711305682256-3b1874c923bd?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 1, 1, 'Orthopedics,Joint Replacement,Knee Surgery,Hip Surgery', 145, 18, 1, '2026-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-28 11:42:23'),
(2, 'Normal vs C-Section Delivery: Helping You Make the Right Choice', 'normal-vs-c-section-delivery', 'Understanding the difference between normal and cesarean delivery, risks, benefits, and what your doctor recommends.', 'The decision between normal delivery and a C-section is one of the most significant choices expectant mothers face.', 'https://plus.unsplash.com/premium_photo-1772729895535-7597677645e3?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 2, 2, 'Gynecology,Delivery,C-Section,Normal Delivery,Pregnancy', 99, 12, 1, '2026-01-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-26 19:14:04'),
(3, 'Pregnancy Nutrition: Foods That Support a Healthy Baby', 'pregnancy-nutrition-foods-healthy-baby', 'Essential dietary advice for expecting mothers — what to eat, what to avoid, and how to manage common pregnancy discomforts.', 'Good nutrition during pregnancy is one of the most powerful gifts you can give your growing baby.', 'https://plus.unsplash.com/premium_photo-1772729895535-7597677645e3?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 3, 3, 'Pregnancy,Nutrition,Maternal Health,Baby Health,Diet', 76, 8, 1, '2026-01-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-26 19:14:07'),
(4, 'Chronic Back Pain: Causes, Diagnosis & Modern Treatment Options', 'chronic-back-pain-causes-diagnosis-treatment', 'From slip disc to spondylosis — understand the root cause of your back pain and the advanced treatment options available.', 'Chronic back pain is one of the most common reasons patients visit R.K. Hospital.', 'https://plus.unsplash.com/premium_photo-1772729895535-7597677645e3?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 5, 1, 'Spine Care,Back Pain,Slip Disc,Spondylosis,Orthopedics', 111, 15, 1, '2026-02-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-26 19:14:09'),
(5, 'PCOS & PCOD: Symptoms, Causes and How We Treat It', 'pcos-pcod-symptoms-causes-treatment', 'Polycystic ovary syndrome affects millions of women. Learn about early warning signs, hormonal imbalance, and treatment plans.', 'PCOS and PCOD are among the most common hormonal disorders affecting women of reproductive age.', 'https://plus.unsplash.com/premium_photo-1772729895535-7597677645e3?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 6, 2, 'PCOS,PCOD,Women\'s Health,Hormonal Health,Fertility', 136, 20, 1, '2026-02-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-26 19:14:12'),
(6, 'Laparoscopic Surgery: Minimally Invasive, Maximum Recovery', 'laparoscopic-surgery-minimally-invasive', 'Discover why laparoscopic surgery is safer, faster to recover from, and now available for a wide range of procedures.', 'Laparoscopic surgery has revolutionized the way we perform operations at R.K. Hospital.', 'https://plus.unsplash.com/premium_photo-1772729895535-7597677645e3?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, 4, 4, 'Laparoscopy,Surgery,Minimally Invasive,Keyhole Surgery', 107, 10, 1, '2026-02-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'article', NULL, NULL, 'summary_large_image', 'index,follow', 'BlogPosting', NULL, '2026-03-26 09:21:34', '2026-03-26 19:14:14'),
(7, 'this is test', 'this-is-test', 'wpsd', '<p>dfds</p>', '', NULL, 2, 5, 'sfda, sfasdf, sdaf, sdfd, sdfdf, sdfsd', 0, 0, 0, '2026-03-27', 1, 'this is test', 'dfsds', '', 'http://localhost/hpce/blog/f', 'this is test', 'dfsds', '', 'article', 'this is test', 'dfsds', 'summary_large_image', 'index,follow', 'BlogPosting', '{\"@context\":\"https://schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"this is test\",\"description\":\"dfsds\",\"url\":\"http://localhost/hpce/blog/f\",\"image\":\"\",\"datePublished\":\"2026-03-27T16:17\",\"dateModified\":\"2026-03-28\"}', '2026-03-28 09:03:48', '2026-03-28 09:03:48'),
(8, 'fgfds df fjsd fngfs', 'fgfds-df-fjsd-fngfs', '', '<p><strong>dfdxdfxfcs ahoshke</strong></p><p><br></p><p><br></p><h2><strong>text abhishek</strong></h2>', 'assets/img/blog/fgfds-df-fjsd-fngfs-69c7b0f12e594.webp', NULL, 2, 5, 'sfda, sfasdf, sdaf, sdfd, sdfdf, sdfsd', 3, 0, 1, '2026-03-19', NULL, 'fgfds df fjsd fngfs', 'dfd', '', '', 'fgfds df fjsd fngfs', 'dfd', 'assets/img/blog/blog-1774690813-69c7a1fdf3b97.png', 'article', 'fgfds df fjsd fngfs', 'dfd', 'summary_large_image', 'index,follow', 'BlogPosting', '{\"@context\":\"https://schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"fgfds df fjsd fngfs\",\"description\":\"dfd\",\"url\":\"\",\"image\":\"assets/img/blog/blog-1774690813-69c7a1fdf3b97.png\",\"datePublished\":\"2026-03-19T00:00\",\"dateModified\":\"2026-03-28\"}', '2026-03-28 09:14:50', '2026-03-28 11:43:23'),
(10, 'Complete Guide to Knee Replacement Surgery: Benefits, Procedure & Recovery', 'knee-replacement-surgery-guide', 'Learn everything about knee replacement surgery, including procedure, benefits, risks, and recovery tips to regain pain-free mobility.', '<h3>&nbsp;Introduction</h3><p>Knee pain can severely impact your daily life, making simple activities like walking, climbing stairs, or even sitting uncomfortable. When medications and therapies fail to provide relief, <strong>knee replacement surgery</strong> becomes an effective solution for long-term mobility and pain relief.</p><p><br></p><h3>🔍 What is Knee Replacement Surgery?</h3><p>Knee replacement surgery (also called knee arthroplasty) is a procedure where damaged parts of the knee joint are replaced with artificial implants. It is commonly recommended for patients suffering from:</p><ul><li>Severe arthritis</li><li>Joint deformity</li><li>Chronic knee pain</li><li>Injury-related damage</li></ul><h3>⚡ When Do You Need Knee Replacement?</h3><p>You may need surgery if you experience:</p><ul><li>Persistent knee pain even at rest</li><li>Difficulty in walking or climbing stairs</li><li>Swelling and stiffness in the joint</li><li>Limited mobility affecting daily life</li><li>No improvement with medications or physiotherapy</li></ul><h3>🏥 Types of Knee Replacement</h3><p>There are mainly two types:</p><ol><li><strong>Total Knee Replacement (TKR)</strong> – Entire joint is replaced</li><li><strong>Partial Knee Replacement (PKR)</strong> – Only damaged portion is replaced</li></ol><h3>🔧 Procedure Explained</h3><p>The surgery usually involves:</p><ol><li>Removal of damaged cartilage and bone</li><li>Reshaping the knee joint</li><li>Placement of artificial implants (metal/plastic)</li><li>Closing the incision</li></ol><p>⏱ Duration: 1–2 hours</p><p>🛌 Hospital Stay: 2–4 days</p><h3>💪 Benefits of Knee Replacement</h3><ul><li>Significant pain relief</li><li>Improved mobility and flexibility</li><li>Better quality of life</li><li>Long-lasting results (15–20 years)</li></ul><h3>⚠️ Risks &amp; Complications</h3><p>Although safe, some risks may include:</p><ul><li>Infection</li><li>Blood clots</li><li>Implant wear over time</li><li>Temporary stiffness</li></ul><p>👉 Choosing an experienced surgeon minimizes risks.</p><p><br></p><h3>🏃 Recovery &amp; Rehabilitation</h3><p>Recovery is crucial for success:</p><ul><li>Start walking within 24–48 hours</li><li>Physiotherapy for strength and mobility</li><li>Full recovery in 6–12 weeks</li><li>Avoid high-impact activities initially</li></ul><h3>🍎 Tips for Faster Recovery</h3><ul><li>Follow doctor’s advice strictly</li><li>Maintain healthy weight</li><li>Do regular exercises</li><li>Keep the surgical area clean</li><li>Attend follow-up visits</li></ul><h3>✅ Conclusion</h3><p>Knee replacement surgery is a life-changing procedure for people suffering from chronic knee pain. With modern techniques and proper rehabilitation, patients can return to an active, pain-free life.</p>', 'assets/img/blog/knee-replacement-surgery-guide-69c7b0d147cdb.webp', NULL, 1, 5, 'Orthopedics, Knee Pain, Joint Replacement, Surgery, Health Tips', 4, 0, 1, '2026-03-29', NULL, 'Knee Replacement Surgery: Procedure, Benefits', 'Discover knee replacement surgery benefits, procedure, risks, and recovery tips. Regain mobility and live pain-free with expert care.', 'knee replacement surgery', 'http://localhost/hpce/blog/knee-replacement-surgery-guide', 'Knee Replacement Surgery: Procedure, Benefits', 'Discover knee replacement surgery benefits, procedure, risks, and recovery tips. Regain mobility and live pain-free with expert care.', 'assets/img/blog/blog-1774693609-69c7ace97dd90.png', 'article', 'Knee Replacement Surgery: Procedure, Benefits', 'Discover knee replacement surgery benefits, procedure, risks, and recovery tips. Regain mobility and live pain-free with expert care.', 'summary_large_image', 'index,follow', 'BlogPosting', '{\"@context\":\"https://schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Knee Replacement Surgery: Procedure, Benefits\",\"description\":\"Discover knee replacement surgery benefits, procedure, risks, and recovery tips. Regain mobility and live pain-free with expert care.\",\"url\":\"http://localhost/hpce/blog/knee-replacement-surgery-guide\",\"image\":\"assets/img/blog/blog-1774693609-69c7ace97dd90.png\",\"datePublished\":\"2026-03-29T00:00\",\"dateModified\":\"2026-03-28\"}', '2026-03-28 09:55:41', '2026-03-28 11:43:14'),
(11, 'dfs', 'dfs', '', '<p>dfdfs</p>', 'assets/img/blog/dfs-69c7b34c01221.webp', NULL, 1, 5, 'sfda, sfasdf, sdaf, sdfd, sdfdf, sdfsd', 0, 0, 0, '2026-03-27', 1, 'dfs', '', '', '', 'dfs', '', 'assets/img/blog/dfs-69c7b34c01221.webp', 'article', 'dfs', '', 'summary_large_image', 'index,follow', 'BlogPosting', '{\"@context\":\"https://schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"dfs\",\"description\":\"\",\"url\":\"\",\"image\":\"assets/img/blog/dfs-69c7b34c01221.webp\",\"datePublished\":\"2026-03-27T16:17\",\"dateModified\":\"2026-03-28\"}', '2026-03-28 10:54:04', '2026-03-28 10:54:04'),
(12, 'zsAF asaf sdaf', 'zsaf-asaf-sdaf', 'f sadfasdffafdfasfsfd s sadf aadfsd sd sad f', '<p>sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd sadf dfasdf safd fasdf sdf asfs df afds dfsd</p>', 'assets/img/blog/blog-1774697759-69c7bd1f54c27.jpg', NULL, 1, 5, '', 7, 0, 1, '2026-03-28', NULL, 'zsAF asaf sdaf', 'f sadfasdffafdfasfsfd s sadf aadfsd sd sad f', '', '', 'zsAF asaf sdaf', 'f sadfasdffafdfasfsfd s sadf aadfsd sd sad f', 'assets/img/blog/blog-1774695569-69c7b491c2825.jpg', 'article', 'zsAF asaf sdaf', 'f sadfasdffafdfasfsfd s sadf aadfsd sd sad f', 'summary_large_image', 'index,follow', 'BlogPosting', '{\"@context\":\"https://schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"zsAF asaf sdaf\",\"description\":\"f sadfasdffafdfasfsfd s sadf aadfsd sd sad f\",\"url\":\"\",\"image\":\"assets/img/blog/blog-1774695569-69c7b491c2825.jpg\",\"datePublished\":\"2026-03-28T00:00\",\"dateModified\":\"2026-03-28\"}', '2026-03-28 10:58:22', '2026-03-28 12:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `created_at`) VALUES
(1, 'Orthopedics', 'orthopedics', 'flaticon-bone', 'Expert care for bones, joints, and musculoskeletal conditions.', '2026-03-26 09:21:34'),
(2, 'Gynecology', 'gynecology', 'flaticon-baby', 'Comprehensive women\'s reproductive health and obstetric services.', '2026-03-26 09:21:34'),
(3, 'Pregnancy Care', 'pregnancy-care', 'flaticon-pregnant', 'Antenatal and postnatal care for mother and baby.', '2026-03-26 09:21:34'),
(4, 'Surgery', 'surgery', 'flaticon-surgery', 'Advanced open and laparoscopic surgical procedures.', '2026-03-26 09:21:34'),
(5, 'Spine Care', 'spine-care', 'flaticon-spine', 'Diagnosis and treatment of spine and disc disorders.', '2026-03-26 09:21:34'),
(6, 'Women\'s Health', 'womens-health', NULL, 'Hormonal health, PCOS/PCOD, and fertility care.', '2026-03-26 09:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `designation` varchar(200) DEFAULT NULL,
  `specialty` varchar(150) DEFAULT NULL,
  `satisfaction_rate` int(11) DEFAULT 0,
  `feedback_count` int(11) DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `consultation_fee` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `education_json` longtext DEFAULT NULL,
  `experience_json` longtext DEFAULT NULL,
  `awards_json` longtext DEFAULT NULL,
  `specializations` varchar(500) DEFAULT NULL,
  `map_iframe` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'assets/img/patients/default.jpg',
  `profile_url` varchar(255) DEFAULT 'doctor-profile.html',
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `published_at` datetime DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `focus_keyword` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'profile',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_card` varchar(50) DEFAULT 'summary_large_image',
  `robots_meta` varchar(50) DEFAULT 'index, follow',
  `schema_type` varchar(100) DEFAULT 'Physician',
  `schema_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `slug`, `designation`, `specialty`, `satisfaction_rate`, `feedback_count`, `location`, `consultation_fee`, `bio`, `education_json`, `experience_json`, `awards_json`, `specializations`, `map_iframe`, `photo`, `profile_url`, `excerpt`, `content`, `tags`, `views`, `is_published`, `published_at`, `meta_title`, `meta_description`, `focus_keyword`, `canonical_url`, `og_title`, `og_description`, `og_image`, `og_type`, `twitter_title`, `twitter_description`, `twitter_card`, `robots_meta`, `schema_type`, `schema_json`, `created_at`, `updated_at`) VALUES
(5, 'Dr. Manan Gogia', 'dr-manan-gogia', 'General Surgeon', 'Cardiologist', 0, 0, '', '', 'Experienced surgeon specializing in minimally invasive procedures.', '[{\"title\":\"rdsd\",\"degree\":\"dsf\",\"year\":\"1998\"}]', '[]', '[]', '', '', 'assets/img/doctors/dr-manan-gogia-69ca49b580380.webp', 'dr-manan-gogia', '', NULL, 'Orthopedics, Knee Pain, Joint Replacement, Surgery, Health Tips', 0, 1, '2026-03-30 00:00:00', 'Dr. Manan Gogia', '', '', '', 'Dr. Manan Gogia', '', 'assets/img/doctors/dr-manan-gogia-69ca49b580380.webp', 'profile', 'Dr. Manan Gogia', '', 'summary_large_image', 'index,follow', 'Physician', '{\"@context\":\"https://schema.org\",\"@type\":\"Physician\",\"name\":\"Dr. Manan Gogia\",\"description\":\"\",\"image\":\"assets/img/doctors/dr-manan-gogia-69ca49b580380.webp\",\"url\":\"\",\"medicalSpecialty\":\"Cardiologist\"}', '2026-03-30 05:15:11', '2026-03-30 10:00:21'),
(7, 'Abhishek', 'abhishek', 'mbbs', 'Cardiologist', 100, 0, 'nagpur', '500', 'kjhlkjh jghkhjjh kjghjhgkjh jhgkjkhj kjghkjghkjhg kjghkj ', '[{\"title\":\"sdaf fd\",\"degree\":\"mbbs\",\"year\":\"1998\"},{\"title\":\"fdasf\",\"degree\":\"bds\",\"year\":\"19963\"}]', '[{\"title\":\"safdf asdf df asdf\",\"year\":\"2015\"}]', '[{\"title\":\"sfdsdf\",\"desc\":\"gdfsg sdgfsfsfg sdfgsd gsf\",\"year\":\"jsn 2023\"}]', 'adfsfdfs, sfsdfaff, asfdsf, asfddfaf, asfddsf, sadf, safddf', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3722.1859625272937!2d79.05525637430827!3d21.10515098515243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bd4bf9b715bed7f%3A0xd25d0ea4b2173fbb!2sPramod%20Rajshree%20Plaza%2C%20Khamla%2C%20Nagpur%2C%20Maharashtra%20440025!5e0!3m2!1sen!2sin!4v1774846995124!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 'assets/img/doctors/abhishek-69ca45bba533b.webp', 'doctor-profile.html', '', NULL, '', 0, 1, '2026-03-30 00:00:00', 'Abhishek', '', '', '', 'Abhishek', '', 'assets/img/doctors/abhishek-69ca45bba533b.webp', 'profile', 'Abhishek', '', 'summary_large_image', 'index,follow', 'Physician', '{\"@context\":\"https://schema.org\",\"@type\":\"Physician\",\"name\":\"Abhishek\",\"description\":\"\",\"image\":\"assets/img/doctors/abhishek-69ca45bba533b.webp\",\"url\":\"\",\"medicalSpecialty\":\"Cardiologist\"}', '2026-03-30 05:15:11', '2026-03-30 09:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_services`
--

CREATE TABLE `doctor_services` (
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `hero_content_json` longtext DEFAULT NULL,
  `service_card_json` longtext DEFAULT NULL,
  `why_choose_json` longtext DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `hero_image_alt` varchar(255) DEFAULT NULL,
  `slug` varchar(280) NOT NULL,
  `short_description` text DEFAULT NULL,
  `h1_title` varchar(255) DEFAULT NULL,
  `breadcrumb_json` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `sections_json` longtext DEFAULT NULL,
  `faqs_json` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/services/default.jpg',
  `image_alt` varchar(255) DEFAULT NULL,
  `gallery_json` longtext DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `related_services_json` text DEFAULT NULL,
  `doctor_ids` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED DEFAULT 0,
  `meta_title` varchar(70) DEFAULT NULL,
  `meta_description` varchar(180) DEFAULT NULL,
  `focus_keyword` varchar(100) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'website',
  `twitter_title` varchar(200) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_card` varchar(50) DEFAULT 'summary_large_image',
  `robots_meta` varchar(50) DEFAULT 'index,follow',
  `schema_type` varchar(50) DEFAULT 'MedicalProcedure',
  `schema_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_services`
--
ALTER TABLE `doctor_services`
  ADD PRIMARY KEY (`doctor_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctor_services`
--
ALTER TABLE `doctor_services`
  ADD CONSTRAINT `doctor_services_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
