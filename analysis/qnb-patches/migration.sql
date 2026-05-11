-- QNB Pay Security Patches — DB Migration
-- 2026-05-12
-- Patch 03 (recurringCancel) için gerekli yeni tablo

-- BACKUP ÖNCESİ ÖNERİ:
-- mysqldump -u USER -p ravenden_1 > backup-before-qnb-patches.sql

CREATE TABLE IF NOT EXISTS `oc_qnbpay_recurring_cancel_requests` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `customer_id` INT NOT NULL,
    `requested_at` DATETIME NOT NULL,
    `processed_at` DATETIME NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
    `notes` TEXT,
    `processed_by` INT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_order` (`order_id`),
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='QNB Pay recurring iptal talepleri — manuel admin işleme için';

-- Opsiyonel: yeni order status "Cancel Requested"
-- INSERT INTO `oc_order_status` (`order_status_id`, `language_id`, `name`)
-- VALUES (NULL, 2, 'Recurring İptal Talebi');
-- Yeni ID'yi `payment_qnbpay_order_status_id_cancel_requested` setting'ine ekle

-- Doğrulama
-- SELECT COUNT(*) FROM `oc_qnbpay_recurring_cancel_requests`;
-- SHOW CREATE TABLE `oc_qnbpay_recurring_cancel_requests`;
