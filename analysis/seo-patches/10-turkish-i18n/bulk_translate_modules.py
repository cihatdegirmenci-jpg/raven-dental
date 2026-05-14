#!/usr/bin/env python3
"""
TÜM module_data JSON'lerini tara, İngilizce kalmış kategori isimleri / UI metinleri TR'ye çevir.

Çalışma şekli:
1. oc_journal3_module'deki tüm modülleri çek
2. Her birinde belirli {"lang_1":..., "lang_2":...} dict'lerini bul
3. lang_1/lang_2 değeri TRANSLATE dict'inde varsa değiştir
4. Değişen modülleri SQL UPDATE olarak yaz
5. Apply + verify
"""
import json
import os
import subprocess

DC = "/Users/ipci/raven-dental/docker-compose.yml"
HERE = os.path.dirname(os.path.abspath(__file__))

# Kategori adları (ALL-CAPS English → TR Proper Case)
TRANSLATE = {
    # Kategoriler
    "DIAGNOSTICS": "Diagnostik",
    "Diagnostics": "Diagnostik",
    "diagnostics": "Diagnostik",
    "PRESERVATION": "Restorasyon",
    "Preservation": "Restorasyon",
    "EXTRACTION": "Çekim",
    "Extraction": "Çekim",
    "SURGERY": "Cerrahi",
    "Surgery": "Cerrahi",
    "PERIODONTICS": "Periodonti",
    "Periodontics": "Periodonti",
    "IMPLANTOLOGY": "İmplantoloji",
    "Implantology": "İmplantoloji",
    "PROSTHETICS": "Protez",
    "Prosthetics": "Protez",
    "ORTHODONTICS": "Ortodonti",
    "Orthodontics": "Ortodonti",
    "ENDODONTICS": "Endodonti",
    "Endodontics": "Endodonti",
    "PROCESSING": "İşlem",
    "Processing": "İşlem",
    # UI elements
    "Your Cart": "Sepetim",
    "Menu": "Menü",
    "Filter Products": "Ürünleri Filtrele",
    "Filters": "Filtreler",
    "Quickview": "Hızlı Bakış",
    "Add to Cart": "Sepete Ekle",
    "View Cart": "Sepete Git",
    "Continue Shopping": "Alışverişe Devam",
    "Out of Stock": "Stokta Yok",
    "In Stock": "Stokta",
    "Wishlist": "İstek Listesi",
    "Compare": "Karşılaştır",
    "Sort By": "Sırala",
    "Search": "Ara",
    "Subcategories": "Alt Kategoriler",
    "Apply": "Uygula",
    "Reset": "Sıfırla",
    "Close": "Kapat",
    "Submit": "Gönder",
    "Manufacturer": "Marka",
    "Manufacturers": "Markalar",
    "Price": "Fiyat",
    "Rating": "Değerlendirme",
    "Read More": "Devamını Oku",
    "Show More": "Daha Fazla Göster",
    "Show Less": "Daha Az Göster",
    "More Details": "Daha Fazla Detay",
    "Load Previous Products": "Önceki Ürünleri Yükle",
    "Load Next Products": "Sonraki Ürünleri Yükle",
    "Loading...": "Yükleniyor...",
    "Loading": "Yükleniyor",
    "You have reached the end of the list.": "Listenin sonuna ulaştınız.",
}


def fetch_all_modules():
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        "mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        "-e \"SELECT module_id, module_data FROM oc_journal3_module ORDER BY module_id;\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    if r.returncode != 0:
        raise RuntimeError(r.stderr.decode())
    out = r.stdout.decode("utf-8")
    modules = {}
    # Each row is: module_id\tmodule_data\n  but module_data may itself contain tabs
    # MySQL --raw returns columns separated by tab; rows by newline.
    # However module_data is JSON (no real newlines typically). Use simple split.
    for line in out.split("\n"):
        if not line.strip():
            continue
        tab = line.find("\t")
        if tab < 0:
            continue
        mid = line[:tab]
        data = line[tab+1:]
        try:
            mid_int = int(mid)
        except ValueError:
            continue
        modules[mid_int] = data
    return modules


def translate_dict(obj, counter):
    if isinstance(obj, dict):
        # If looks like a {lang_1, lang_2} text dict, translate values
        if "lang_1" in obj or "lang_2" in obj:
            for lk in ("lang_1", "lang_2"):
                val = obj.get(lk)
                if isinstance(val, str) and val in TRANSLATE:
                    obj[lk] = TRANSLATE[val]
                    counter[0] += 1
        # Recurse
        for k, v in obj.items():
            translate_dict(v, counter)
    elif isinstance(obj, list):
        for item in obj:
            translate_dict(item, counter)


def main():
    modules = fetch_all_modules()
    print(f"Loaded {len(modules)} modules")

    sql_parts = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    total_changes = 0
    changed_modules = 0

    for mid, raw in modules.items():
        try:
            data = json.loads(raw)
        except json.JSONDecodeError as e:
            print(f"  WARN: module {mid} JSON parse failed: {e}")
            continue
        counter = [0]
        translate_dict(data, counter)
        if counter[0] > 0:
            new_json = json.dumps(data, ensure_ascii=True, separators=(",", ":"))
            escaped = new_json.replace("\\", "\\\\").replace("'", "''")
            sql_parts.append(
                f"UPDATE oc_journal3_module SET module_data='{escaped}' WHERE module_id={mid};"
            )
            total_changes += counter[0]
            changed_modules += 1
            print(f"  module {mid}: {counter[0]} translations")

    sql_parts.append("COMMIT;")

    if changed_modules == 0:
        print("Nothing to update.")
        return

    sql = "\n".join(sql_parts) + "\n"
    out = os.path.join(HERE, "update-all-modules.sql")
    with open(out, "w") as f:
        f.write(sql)
    print(f"\nTotal: {total_changes} translations across {changed_modules} modules")
    print(f"SQL: {out} ({len(sql)} bytes)")

    cmd = (
        f"docker compose -f {DC} exec -T mysql "
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {out}"
    )
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        raise SystemExit(1)
    print("Applied OK")

    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )


if __name__ == "__main__":
    main()
