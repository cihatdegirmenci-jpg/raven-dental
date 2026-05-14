#!/usr/bin/env python3
"""
Türkçeleştirme — Journal3 skin_setting (lang_2 = TR) + mobile header modülleri.

Site TR-only. lang_1 (EN) ve lang_2 (TR) alanlarına aynı TR metin yazıyoruz
(EN locale destekleyince ileride lang_1'i ayırırız).
"""
import json
import os
import subprocess

HERE = os.path.dirname(os.path.abspath(__file__))
DC = "/Users/ipci/raven-dental/docker-compose.yml"

# Skin setting çevirileri (lang_1 = lang_2 = TR)
SKIN_TRANSLATIONS = {
    "allProductsPageMetaDescription": "Tüm Ürünler — Raven Dental",
    "allProductsPageMetaKeywords": "tüm ürünler, raven dental",
    "allProductsPageMetaTitle": "Tüm Ürünler — Raven Dental",
    "allProductsPageTitle": "Tüm Ürünler",
    "checkoutTitle": "Hızlı Ödeme",
    "confirmOrderAddressErrorText": "Adres eksik — lütfen Hesabım > Adreslerim bölümünden düzenleyin.",
    "confirmOrderLanguage": "Siparişi Onayla",
    "countdownDay": "Gün",
    "countdownHour": "Saat",
    "countdownMin": "Dk",
    "countdownSec": "Sn",
    # Test verisi gibi görünen değerler — temizle
    "expandButtonText": "Daha Fazla Göster",
    "expandButtonTextLess": "Daha Az Göster",
    "globalExpandButtonText": "Daha Fazla Göster",
    "globalExpandButtonTextLess": "Daha Az Göster",
    "infiniteScrollLoading": "Yükleniyor...",
    "infiniteScrollLoadNext": "Sonraki Ürünleri Yükle",
    "infiniteScrollLoadPrev": "Önceki Ürünleri Yükle",
    "infiniteScrollNoneLeft": "Listenin sonuna ulaştınız.",
    "loaderText": "Yükleniyor",
    "maintenanceContent": "Şu anda planlı bakım çalışması yapıyoruz. Lütfen kısa süre sonra tekrar deneyin.",
    "maintenanceMetaTitle": "Bakım",
    "oldBrowserTitle": "Tarayıcınız güncel değil!",
    "quickviewExpandButtonTextLess": "Daha Az Göster",
    "quickviewExtraText": "Daha Fazla Detay",
    "quickviewText": "Hızlı Bakış",
    "sectionGuestText": "Misafir",
    "sectionLoginText": "Giriş Yap",
    "sectionRegisterText": "Üye Ol",
    "sectionTitleConfirm": "Siparişinizi Onaylayın",
    "sectionTitleCouponVoucherReward": "Kupon / Hediye Çeki / Puan",
    "sectionTitleLogin": "Giriş Yap veya Üye Ol",
    "sectionTitlePaymentAddress": "Fatura Adresi",
    "sectionTitlePaymentDetails": "Ödeme Bilgileri",
    "sectionTitlePaymentMethod": "Ödeme Yöntemi",
    "sectionTitlePersonal": "Kişisel Bilgileriniz",
    "sectionTitleShippingAddress": "Teslimat Adresi",
    "sectionTitleShippingMethod": "Teslimat Yöntemi",
    "sectionTitleShoppingCart": "Sepet",
    "subcategoriesTitle": "Alt Kategoriler",
}

# defaultExpandButtonText / Less + quickviewExpandButtonText boş kalsın — özellikle boş tanımlı
EMPTY_KEEP = {
    "allProductsPageMetaDescription_lang_2_empty": False,  # placeholder
}

# Module-level: mobile header Cart + Menu title overrides
# headerMobileCartTitle ve headerMobileMainMenuTitle (varsa) module_data JSON'da
MOBILE_CART_TITLE_TR = "Sepetim"
MOBILE_MENU_TITLE_TR = "Menü"
MOBILE_FILTER_TITLE_TR = "Filtreler"
MOBILE_SEARCH_TITLE_TR = "Arama"


def sh(cmd):
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        raise RuntimeError(f"FAIL: {cmd}\n{r.stderr}")
    return r.stdout


def shell_quote(s):
    return s.replace("\\", "\\\\").replace("'", "''")


def update_skin_settings(translations):
    sql_lines = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    for name, tr in translations.items():
        # Encode JSON as ASCII (lang_2=TR, lang_1=TR for now since site is TR-only)
        value = json.dumps({"lang_2": tr, "lang_1": tr}, ensure_ascii=True, separators=(",", ":"))
        escaped = shell_quote(value)
        sql_lines.append(
            f"UPDATE oc_journal3_skin_setting SET setting_value='{escaped}' "
            f"WHERE setting_name='{name}' AND skin_id=1;"
        )
    sql_lines.append("COMMIT;")
    return "\n".join(sql_lines) + "\n"


def fetch_module(mid):
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        f"-e \"SELECT module_data FROM oc_journal3_module WHERE module_id={mid};\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    if r.returncode != 0:
        raise RuntimeError(r.stderr.decode())
    val = r.stdout.decode("utf-8")
    if val.endswith("\n"):
        val = val[:-1]
    return val


def patch_module_text(data, key_paths, new_value):
    """Recursively find dict entries where the key matches and value has 'lang_X' format; update to new_value."""
    def walk(obj):
        if isinstance(obj, dict):
            for k, v in list(obj.items()):
                if k in key_paths and isinstance(v, dict) and ("lang_1" in v or "lang_2" in v):
                    obj[k] = {"lang_1": new_value, "lang_2": new_value}
                else:
                    walk(v)
        elif isinstance(obj, list):
            for item in obj:
                walk(item)
    walk(data)


def update_mobile_modules():
    """Mobile header modules 5, 16, 17, 18: change Cart/Menu/Filter/Search titles."""
    sql_lines = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    title_map = {
        "headerMobileCartTitle": MOBILE_CART_TITLE_TR,
        "headerMobileMainMenuTitle": MOBILE_MENU_TITLE_TR,
        "headerMobileFilterTitle": MOBILE_FILTER_TITLE_TR,
        "headerMobileSearchTitle": MOBILE_SEARCH_TITLE_TR,
    }
    for mid in [5, 16, 17, 18]:
        raw = fetch_module(mid)
        data = json.loads(raw)
        for key, tr_value in title_map.items():
            patch_module_text(data, {key}, tr_value)
        # Also patch the generic "Your Cart" / "Menu" raw values within title fields
        # by walking the tree and replacing English with Turkish
        def replace_value(obj):
            if isinstance(obj, dict):
                for k, v in list(obj.items()):
                    if isinstance(v, dict) and ("lang_1" in v or "lang_2" in v):
                        # Check both langs
                        for lk in ("lang_1", "lang_2"):
                            if v.get(lk) == "Your Cart":
                                v[lk] = MOBILE_CART_TITLE_TR
                            elif v.get(lk) == "Menu":
                                v[lk] = MOBILE_MENU_TITLE_TR
                            elif v.get(lk) == "Filters":
                                v[lk] = MOBILE_FILTER_TITLE_TR
                            elif v.get(lk) == "Search":
                                v[lk] = MOBILE_SEARCH_TITLE_TR
                    else:
                        replace_value(v)
            elif isinstance(obj, list):
                for item in obj:
                    replace_value(item)
        replace_value(data)
        new_json = json.dumps(data, ensure_ascii=True, separators=(",", ":"))
        escaped = shell_quote(new_json)
        sql_lines.append(
            f"UPDATE oc_journal3_module SET module_data='{escaped}' WHERE module_id={mid};"
        )
    sql_lines.append("COMMIT;")
    return "\n".join(sql_lines) + "\n"


def apply_sql(path):
    cmd = (
        f"docker compose -f {DC} exec -T mysql "
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {path}"
    )
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        raise SystemExit(1)


def main():
    skin_sql = update_skin_settings(SKIN_TRANSLATIONS)
    skin_path = os.path.join(HERE, "update-skin.sql")
    with open(skin_path, "w") as f:
        f.write(skin_sql)
    print(f"Skin SQL: {len(SKIN_TRANSLATIONS)} settings, {len(skin_sql)} bytes")

    mod_sql = update_mobile_modules()
    mod_path = os.path.join(HERE, "update-modules.sql")
    with open(mod_path, "w") as f:
        f.write(mod_sql)
    print(f"Module SQL: 4 modules, {len(mod_sql)} bytes")

    apply_sql(skin_path)
    apply_sql(mod_path)
    print("Applied OK")

    # Clear cache
    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )

    # Verify
    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    home = r.stdout
    r2 = subprocess.run(["curl", "-s", "http://localhost:8000/el-aletleri"], capture_output=True, text=True)
    cat = r2.stdout
    print("\nVerify — count of remaining English fragments:")
    for s in ["Your Cart", "Quickview", "Filter Products", "Load Previous Products",
              "Load Next Products", "Loading...", "You have reached", "Login", "Register",
              "Day", "Hour", "Min", "Sec"]:
        c_home = home.count(s)
        c_cat = cat.count(s)
        # Day/Hour are common, only count if as standalone word in JS
        if s in ("Day", "Hour", "Min", "Sec"):
            # countdown labels look like: "countdownDay":"Day"
            c_home = home.count(f'":"{s}"')
            c_cat = cat.count(f'":"{s}"')
        print(f"  '{s}': home={c_home}, cat={c_cat}")


if __name__ == "__main__":
    main()
