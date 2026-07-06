<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate tables first if necessary, or just run INSERT IGNORE
        // Using DB::unprepared to run raw SQL

        $sql = <<<SQL
INSERT IGNORE INTO `advertisements` (`id`, `seller_id`, `title`, `image`, `position`, `link`, `status`, `clicks_count`, `impressions_count`, `start_date`, `end_date`, `created_at`, `updated_at`, `type`, `description`, `redirect_url`, `target_type`, `product_id`, `category_id`, `is_active`, `category_name`) VALUES
(4, NULL, 'Wellness', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 04:15:00', '2026-07-31 04:15:00', '2026-06-22 16:46:11', '2026-06-23 16:20:05', 'banner', 'yjjtjytjyj', NULL, 'product', 145, NULL, 1, 'Wellness'),
(5, NULL, 'Language', NULL, NULL, NULL, 'active', 0, 0, NULL, NULL, '2026-06-22 21:56:09', '2026-06-22 21:56:09', 'banner', NULL, NULL, 'product', 256, NULL, 1, 'LanguageLab'),
(7, NULL, 'DeepTech', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:22:00', '2026-07-31 10:22:00', '2026-06-22 22:52:42', '2026-06-23 16:19:07', 'banner', 'DeepTech', NULL, 'product', NULL, NULL, 1, 'DeepTech'),
(8, NULL, 'skills', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:23:00', '2026-07-31 10:23:00', '2026-06-22 22:53:14', '2026-06-23 16:36:42', 'banner', 'skills', NULL, 'product', NULL, NULL, 1, 'Skills'),
(9, NULL, 'games', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:23:00', '2026-07-31 10:23:00', '2026-06-22 22:53:47', '2026-06-23 16:17:51', 'banner', 'games', NULL, 'product', NULL, NULL, 1, 'Games'),
(10, NULL, 'space', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:24:00', '2026-07-31 10:24:00', '2026-06-22 22:54:18', '2026-06-23 16:20:45', 'banner', 'space', NULL, 'product', NULL, NULL, 1, 'Space'),
(11, NULL, 'climate', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:25:00', '2026-07-31 10:25:00', '2026-06-22 22:55:17', '2026-06-23 16:37:27', 'banner', 'climate', NULL, 'product', NULL, NULL, 1, 'Climate'),
(12, NULL, 'Academics', NULL, NULL, NULL, 'active', 0, 0, '2026-06-22 10:25:00', '2026-07-31 10:25:00', '2026-06-22 22:55:51', '2026-06-23 16:19:41', 'banner', 'Academics', NULL, 'product', NULL, NULL, 1, 'Academics'),
(19, NULL, '20%Offer', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 00:59:00', '2026-08-01 00:59:00', '2026-07-02 13:29:38', '2026-07-02 13:29:38', 'featured', '20% off', NULL, 'both', NULL, 58, 1, 'Wellness'),
(22, NULL, 'Buy1Get1', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 01:19:00', '2026-08-01 01:19:00', '2026-07-02 13:49:34', '2026-07-02 13:59:27', 'campaign', 'efwfgfgw', NULL, 'both', NULL, 58, 1, 'Wellness'),
(23, NULL, 'Product Demo Video', NULL, NULL, NULL, 'active', 0, 0, NULL, NULL, '2026-07-02 14:29:53', '2026-07-02 14:29:53', 'video', 'Product Demo Video', NULL, 'both', NULL, 58, 1, 'Wellness'),
(24, NULL, 'Wellness Herbal Tea', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 03:33:00', '2026-07-07 03:34:00', '2026-07-02 16:04:27', '2026-07-02 16:04:27', 'video', 'Wellness Herbal Tea', NULL, 'both', NULL, 22, 1, 'Wellness'),
(26, NULL, 'academic', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 16:22:00', '2026-07-04 16:23:00', '2026-07-02 16:23:02', '2026-07-02 22:55:32', 'campaign', NULL, NULL, 'product', NULL, NULL, 1, 'Academics'),
(27, NULL, 'DeepTech Demo Video', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 04:27:00', '2026-07-07 04:27:00', '2026-07-02 16:57:58', '2026-07-02 16:57:58', 'banner', 'DeepTech Demo Video', NULL, 'both', NULL, 15, 1, 'DeepTech'),
(29, NULL, 'deeptech', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 17:20:00', '2026-07-08 17:20:00', '2026-07-02 17:20:45', '2026-07-02 17:20:45', 'campaign', NULL, NULL, 'product', NULL, 15, 1, 'DeepTech'),
(30, NULL, 'Climate', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 17:25:00', '2026-07-10 17:25:00', '2026-07-02 17:26:00', '2026-07-02 17:26:00', 'campaign', NULL, NULL, 'product', NULL, 21, 1, 'Climate'),
(31, NULL, 'Games Campaign', NULL, NULL, NULL, 'active', 0, 0, '2026-07-02 10:34:00', '2026-07-02 10:34:00', '2026-07-02 23:04:16', '2026-07-02 23:04:16', 'campaign', 'vfdvdv', NULL, 'both', NULL, 18, 1, 'Games'),
(32, NULL, 'Seller campaign', NULL, NULL, NULL, 'pending', 0, 0, '2026-07-03 14:07:00', '2026-07-17 14:07:00', '2026-07-03 14:07:33', '2026-07-03 14:07:33', 'campaign', NULL, NULL, 'product', NULL, NULL, 1, NULL),
(33, NULL, 'Discount campaign', NULL, NULL, NULL, 'pending', 0, 0, '2026-07-03 14:10:00', '2026-07-22 14:10:00', '2026-07-03 14:10:25', '2026-07-03 14:10:25', 'campaign', NULL, NULL, 'product', NULL, NULL, 1, NULL),
(34, NULL, 'space banner', NULL, NULL, NULL, 'pending', 0, 0, '2026-07-03 14:15:00', '2026-07-22 14:15:00', '2026-07-03 14:15:41', '2026-07-03 14:15:41', 'banner', NULL, NULL, 'product', NULL, 19, 1, 'Space'),
(35, NULL, 'skills banner', NULL, NULL, NULL, 'pending', 0, 0, NULL, NULL, '2026-07-03 14:36:55', '2026-07-03 14:36:55', 'banner', NULL, NULL, 'product', NULL, 17, 1, 'Skills'),
(36, NULL, 'wellness banner 2', NULL, NULL, NULL, 'pending', 0, 0, NULL, NULL, '2026-07-03 14:38:20', '2026-07-03 14:38:20', 'banner', NULL, NULL, 'product', NULL, 22, 1, 'Wellness'),
(37, NULL, 'Academics', NULL, NULL, NULL, 'pending', 0, 0, NULL, NULL, '2026-07-03 14:39:35', '2026-07-03 14:39:35', 'banner', NULL, NULL, 'product', NULL, 32, 1, 'Academics'),
(38, NULL, 'go green', NULL, NULL, NULL, 'pending', 0, 0, NULL, NULL, '2026-07-03 14:49:13', '2026-07-03 14:49:13', 'campaign', NULL, NULL, 'product', NULL, NULL, 1, NULL);

INSERT IGNORE INTO `advertisement_media` (`id`, `advertisement_id`, `media_type`, `file_path`, `created_at`, `updated_at`) VALUES
(32, 9, 'image', 'ads/images/1782197271_6a3a2c17592f5.png', '2026-06-23 16:17:51', '2026-06-23 16:17:51'),
(33, 5, 'image', 'ads/images/1782197309_6a3a2c3dd8002.png', '2026-06-23 16:18:29', '2026-06-23 16:18:29'),
(34, 7, 'image', 'ads/images/1782197347_6a3a2c63beb97.png', '2026-06-23 16:19:07', '2026-06-23 16:19:07'),
(35, 12, 'image', 'ads/images/1782197381_6a3a2c85e8fe1.png', '2026-06-23 16:19:41', '2026-06-23 16:19:41'),
(36, 4, 'image', 'ads/images/1782197405_6a3a2c9dca56e.png', '2026-06-23 16:20:05', '2026-06-23 16:20:05'),
(37, 10, 'image', 'ads/images/1782197445_6a3a2cc54f7fa.png', '2026-06-23 16:20:45', '2026-06-23 16:20:45'),
(38, 8, 'image', 'ads/images/1782198402_6a3a30822125c.png', '2026-06-23 16:36:42', '2026-06-23 16:36:42'),
(39, 11, 'image', 'ads/images/1782198447_6a3a30af272a8.png', '2026-06-23 16:37:27', '2026-06-23 16:37:27'),
(44, 19, 'image', 'ads/images/dz7OO1fJvgUiL0JS4WFSjU5jcY8YEqaxuRdKn7NE.jpg', '2026-07-02 13:29:38', '2026-07-02 13:29:38'),
(48, 22, 'image', 'ads/images/T8zzIjZbp6G7DtxcxDsRclqcXSCfpWbshYImezvt.jpg', '2026-07-02 13:59:27', '2026-07-02 13:59:27'),
(49, 23, 'video', 'ads/videos/ulZfyhmZmI7fQ6COG6IjP9D8Fn8kP0s0JcOaMHLh.mp4', '2026-07-02 14:29:53', '2026-07-02 14:29:53'),
(50, 24, 'video', 'ads/videos/pBiQRGAg8tRe0QxLpSkvmkHAiFGtCLQPNVZf6gcP.mp4', '2026-07-02 16:04:27', '2026-07-02 16:04:27'),
(56, 27, 'video', 'ads/videos/rEljwCYDNXsvlvgqqDq0J6jdNqpyY5Vfd7qbBzUR.mp4', '2026-07-02 16:57:58', '2026-07-02 16:57:58'),
(60, 29, 'image', 'ads/images/QaS1G3ZibeIB6o4HvhE0T7QsDWZjjJYI0BrwldWR.jpeg', '2026-07-02 17:20:45', '2026-07-02 17:20:45'),
(61, 30, 'image', 'ads/images/xE8JgvwCMbD9FDKBSA0bTIguAF6doFzFBFMang84.png', '2026-07-02 17:26:00', '2026-07-02 17:26:00'),
(62, 26, 'image', 'ads/images/oLBIrs0BbDz6STnTFaziazKEjmeCo7Cu66RGmVyc.png', '2026-07-02 22:55:32', '2026-07-02 22:55:32'),
(63, 31, 'image', 'ads/images/6JngjUFzNQMPXOuNqkahAg403ZL7qwCwTBPdld8W.jpg', '2026-07-02 23:04:16', '2026-07-02 23:04:16'),
(64, 32, 'image', 'ads/images/dmJu52faQv1qj1kjJ41UyHBFmPppnzZR0ZS3uBgM.png', '2026-07-03 14:07:33', '2026-07-03 14:07:33'),
(65, 33, 'image', 'ads/images/wKq6HzPxeoPtBhaLMTDGH7i5reTSkoJo4RUAQTIA.png', '2026-07-03 14:10:25', '2026-07-03 14:10:25'),
(66, 34, 'image', 'ads/images/DxhlmscvmGx3lGJ7tXSaFHwqiMCOWvly2i7o6oA1.png', '2026-07-03 14:15:41', '2026-07-03 14:15:41'),
(67, 35, 'image', 'ads/images/VLLk2jiYDxbSxjzTe4m47stG3vGiriUJs8Xgf67y.png', '2026-07-03 14:36:55', '2026-07-03 14:36:55'),
(68, 36, 'image', 'ads/images/BwAaYmyeeXkEMvb2bA0CiYnxU6vevlG7vKbVOUcl.png', '2026-07-03 14:38:20', '2026-07-03 14:38:20'),
(69, 37, 'image', 'ads/images/uV0INWRgKdjZcVuFizHH987r3mpRPZugsL2FJDYT.png', '2026-07-03 14:39:35', '2026-07-03 14:39:35'),
(70, 38, 'image', 'ads/images/KfedEX2xyAbEfWOY9H0IVFHtFSSVpodfPRjEYSIB.png', '2026-07-03 14:49:13', '2026-07-03 14:49:13');
SQL;

        DB::unprepared($sql);
    }
}
