#!/usr/bin/env python3
"""
Journal3 default değerlerini Türkçeye çevir (data/settings/common/product_page.json + quickview.json).

Bu InputLang ayarları DB'de override yok (skin variable yapısı karmaşık),
o yüzden defaults dosyasını doğrudan düzenliyoruz.
"""
import json
import os
import re
import shutil
import subprocess

DC = "/Users/ipci/raven-dental/docker-compose.yml"

TRANSLATIONS = {
    "ProductManufacturerText": "Marka",
    "ProductModelText": "Model",
    "ProductSKUText": "Stok Kodu",
    "ProductUPCText": "UPC",
    "ProductEANText": "EAN",
    "ProductJANText": "JAN",
    "ProductISBNText": "ISBN",
    "ProductMPNText": "MPN",
    "ProductLocationText": "Konum",
    "ProductWeightText": "Ağırlık",
    "ProductDimensionText": "Ölçüler",
    "ProductRewardText": "Ödül Puanı",
    "ProductPointsText": "Puan",
    "ProductStockText": "Stok",
    "ProductInStockText": "Stokta",
    "ViewsText": "Görüntülenme:",
    "SoldText": "Satılan:",
    "CountdownText": "Kampanya bitimine:",
}


def patch_file_in_container(container_path):
    """Use Python in container to in-place edit JSON file."""
    # Build a heredoc Python script that loads JSON, mutates, saves
    tr_json = json.dumps(TRANSLATIONS, ensure_ascii=True)
    script = f"""
import json
TRANSLATIONS = {tr_json}
path = '{container_path}'
with open(path, 'r', encoding='utf-8') as f:
    data = json.load(f)
changed = []
for key, tr in TRANSLATIONS.items():
    if key in data and isinstance(data[key], dict) and data[key].get('type') == 'InputLang':
        if data[key].get('value') != tr:
            data[key]['value'] = tr
            changed.append(key)
with open(path, 'w', encoding='utf-8') as f:
    json.dump(data, f, ensure_ascii=False, indent=2)
print(f'Changed {{len(changed)}} keys in {{path}}: {{changed}}')
"""
    cmd = ["docker", "compose", "-f", DC, "exec", "-T", "apache", "python3", "-c", script]
    r = subprocess.run(cmd, capture_output=True, text=True)
    if r.returncode != 0:
        # python3 might not be installed; fallback to in-container sed via python via apache
        print("Python3 in container failed, falling back to host-side edit + cp")
        return None
    print(r.stdout)
    return True


def host_side_patch():
    """Edit on host then docker cp into container."""
    paths = [
        "/Users/ipci/raven-dental/code/system/library/journal3/data/settings/common/product_page.json",
        "/Users/ipci/raven-dental/code/system/library/journal3/data/settings/common/quickview.json",
    ]
    for p in paths:
        if not os.path.exists(p):
            continue
        # backup
        bak = p + ".bak-i18n"
        if not os.path.exists(bak):
            shutil.copy2(p, bak)
        with open(p, 'r', encoding='utf-8') as f:
            data = json.load(f)
        changed = []
        for key, tr in TRANSLATIONS.items():
            if key in data and isinstance(data[key], dict) and data[key].get('type') == 'InputLang':
                if data[key].get('value') != tr:
                    data[key]['value'] = tr
                    changed.append(key)
        with open(p, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"{os.path.basename(p)}: {len(changed)} keys changed: {changed}")


def main():
    host_side_patch()
    # Code dir is bind-mounted into container, so no copy needed
    # Clear cache
    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )
    print("Cache cleared")

    # Verify
    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    print(f"Home — Brand: {r.stdout.count('>Brand:<')} hits, Marka: {r.stdout.count('>Marka:<')} hits")
    r2 = subprocess.run(["curl", "-s", "http://localhost:8000/el-aletleri"], capture_output=True, text=True)
    print(f"Cat — In Stock: {r2.stdout.count('In Stock')} hits, Stokta: {r2.stdout.count('Stokta')} hits")


if __name__ == "__main__":
    main()
