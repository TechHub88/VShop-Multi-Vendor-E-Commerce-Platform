<?php
$host = 'srv1643.hstgr.io';
$db   = 'u605775003_shop';
$user = 'u605775003_shopAdmin';
$pass = 'WLUrYJv4lpfDgPayQpbdz7idsaNl48zM';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $sql = "
    CREATE TABLE IF NOT EXISTS `seller_campaign_participations` (
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `seller_id` bigint unsigned NOT NULL,
      `product_id` bigint unsigned NOT NULL,
      `campaign_type` varchar(255) NOT NULL,
      `combo_details` text,
      `status` varchar(255) NOT NULL DEFAULT 'approved',
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `seller_campaign_participations_seller_id_foreign` (`seller_id`),
      KEY `seller_campaign_participations_product_id_foreign` (`product_id`),
      CONSTRAINT `seller_campaign_participations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
      CONSTRAINT `seller_campaign_participations_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Table seller_campaign_participations created successfully!\n";
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
