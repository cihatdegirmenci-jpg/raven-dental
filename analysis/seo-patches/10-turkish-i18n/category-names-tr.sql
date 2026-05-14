-- Kategori name field'larını TR Proper Case'e çevir.
-- ALL-CAPS İngilizce isimler menü, breadcrumb, kategori listesi gibi yerlerde görünüyor.
-- meta_title zaten doğru ayarlanmış (önceki SEO patches'te), bu sadece "name" field.

SET NAMES utf8mb4;
START TRANSACTION;

UPDATE oc_category_description SET name='Diagnostik'         WHERE language_id=2 AND category_id=59;
UPDATE oc_category_description SET name='Restorasyon'        WHERE language_id=2 AND category_id=60;
UPDATE oc_category_description SET name='Çekim'              WHERE language_id=2 AND category_id=61;
UPDATE oc_category_description SET name='Cerrahi'            WHERE language_id=2 AND category_id=62;
UPDATE oc_category_description SET name='Periodonti'         WHERE language_id=2 AND category_id=63;
UPDATE oc_category_description SET name='İmplantoloji'       WHERE language_id=2 AND category_id=64;
UPDATE oc_category_description SET name='Protez'             WHERE language_id=2 AND category_id=65;
UPDATE oc_category_description SET name='El Aletleri'        WHERE language_id=2 AND category_id=66;
UPDATE oc_category_description SET name='Ortodonti'          WHERE language_id=2 AND category_id=67;
UPDATE oc_category_description SET name='Endodonti'          WHERE language_id=2 AND category_id=68;
UPDATE oc_category_description SET name='İşlem'              WHERE language_id=2 AND category_id=69;
UPDATE oc_category_description SET name='Sarf'               WHERE language_id=2 AND category_id=70;
UPDATE oc_category_description SET name='Raven Cerrahi Aletler' WHERE language_id=2 AND category_id=71;
UPDATE oc_category_description SET name='Elektronik Cihazlar' WHERE language_id=2 AND category_id=72;

COMMIT;
