-- Performance Patch 01 — Missing Indexes
-- Hedef: oc_product, oc_seo_url, oc_session ve diğer sık sorgulanan tablolarda
--        WHERE/ORDER BY için index'ler
-- Risk: Düşük (additive — sadece index ekler, data tutamaz)
-- Beklenen kazanım: 50-300ms kategori/anasayfa sorgu

-- ============================================================
-- oc_product: status, manufacturer_id, date_available, sort_order
-- ============================================================
ALTER TABLE `oc_product` ADD INDEX `idx_status` (`status`);
ALTER TABLE `oc_product` ADD INDEX `idx_manufacturer_id` (`manufacturer_id`);
ALTER TABLE `oc_product` ADD INDEX `idx_date_available` (`date_available`);
ALTER TABLE `oc_product` ADD INDEX `idx_sort_order` (`sort_order`);
-- Composite: kategori/anasayfa list query'leri için
ALTER TABLE `oc_product` ADD INDEX `idx_status_date` (`status`, `date_available`);

-- ============================================================
-- oc_seo_url: query + language lookup (route)
-- ============================================================
ALTER TABLE `oc_seo_url` ADD INDEX `idx_query_lang` (`query`(100), `language_id`);
ALTER TABLE `oc_seo_url` ADD INDEX `idx_keyword_lang` (`keyword`(100), `language_id`);

-- ============================================================
-- oc_product_description: product + language combined
-- ============================================================
ALTER TABLE `oc_product_description` ADD INDEX `idx_product_lang` (`product_id`, `language_id`);

-- ============================================================
-- oc_category_description: category + language
-- ============================================================
ALTER TABLE `oc_category_description` ADD INDEX `idx_category_lang` (`category_id`, `language_id`);

-- ============================================================
-- oc_product_to_category: kategori başına ürün listeleme
-- ============================================================
ALTER TABLE `oc_product_to_category` ADD INDEX `idx_category` (`category_id`);

-- ============================================================
-- oc_setting: code+key lookup (sık çağrılır)
-- ============================================================
ALTER TABLE `oc_setting` ADD INDEX `idx_code_key` (`code`, `key`(100));

-- ============================================================
-- oc_url_alias (eski OpenCart 2.x — varsa)
-- ============================================================
-- ALTER TABLE `oc_url_alias` ADD INDEX `idx_query` (`query`(100));

-- ============================================================
-- oc_session: timestamp temizlik için
-- ============================================================
ALTER TABLE `oc_session` ADD INDEX `idx_expire` (`expire`);

-- ============================================================
-- Doğrulama
-- ============================================================
SHOW INDEX FROM oc_product;
SHOW INDEX FROM oc_seo_url;
