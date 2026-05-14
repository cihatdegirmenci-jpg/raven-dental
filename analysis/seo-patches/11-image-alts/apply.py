#!/usr/bin/env python3
"""
Boş alt text'leri Türkçe açıklayıcı metinle doldur.
SEO + a11y için kritik.
"""
import json
import os
import subprocess

DC = "/Users/ipci/raven-dental/docker-compose.yml"
HERE = os.path.dirname(os.path.abspath(__file__))

# Module ID → list of (index_path, alt_text)
# index_path: dotted path like "items.0" or "items.0.items.1"
ALT_PATCHES = {
    # Module 26: Slider Top Home (2 ana slider görseli)
    26: {
        "items.0": "Raven Dental — Profesyonel Diş Hekimliği Aletleri",
        "items.1": "Raven Dental — Üretici Doğrudan Klinik Aletleri",
    },
    # Module 98: Fashion Banner (raven2 logo)
    98: {
        "items.0": "Raven Dental Logo",
    },
    # Module 201: Specials Banner (raven1 logo)
    201: {
        "items.0": "Raven Dental Logo",
    },
    # Module 259: Banners Top Home (2 banner — implantoloji + muayene)
    259: {
        "items.0": "İmplantoloji Aletleri — Raven Dental",
        "items.1": "Muayene Aletleri — Raven Dental",
    },
    # Module 286: New Banners — 4 kategori banner
    # item[0] → cat 64 İmplantoloji, [1] → 62 Cerrahi, [2] → 59 Diagnostik, [3] → 61 Çekim
    286: {
        "items.0": "İmplantoloji Aletleri Kategorisi — Raven Dental",
        "items.1": "Cerrahi Aletleri Kategorisi — Raven Dental",
        "items.2": "Diagnostik Aletleri Kategorisi — Raven Dental",
        "items.3": "Çekim Aletleri Kategorisi — Raven Dental",
    },
}


def fetch_module(mid):
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        f"-e \"SELECT module_data FROM oc_journal3_module WHERE module_id={mid};\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    val = r.stdout.decode("utf-8").rstrip("\n")
    return val


def set_path(obj, path, alt_text):
    """Walk path like 'items.0' and set obj.alt to {lang_1: alt, lang_2: alt}."""
    parts = path.split(".")
    cur = obj
    for p in parts:
        if p.isdigit():
            cur = cur[int(p)]
        else:
            cur = cur[p]
    # cur is now the target item (dict with 'alt' field)
    if "alt" not in cur:
        raise KeyError(f"path {path} does not have 'alt' field")
    cur["alt"] = {"lang_1": alt_text, "lang_2": alt_text}


def main():
    sql_parts = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    total = 0
    for mid, patches in ALT_PATCHES.items():
        raw = fetch_module(mid)
        data = json.loads(raw)
        for path, alt in patches.items():
            try:
                set_path(data, path, alt)
                total += 1
            except (KeyError, IndexError) as e:
                print(f"  FAIL mid={mid} path={path}: {e}")
        new_json = json.dumps(data, ensure_ascii=True, separators=(",", ":"))
        escaped = new_json.replace("\\", "\\\\").replace("'", "''")
        sql_parts.append(
            f"UPDATE oc_journal3_module SET module_data='{escaped}' WHERE module_id={mid};"
        )
        print(f"  module {mid}: {len(patches)} alt patches")

    sql_parts.append("COMMIT;")
    out = os.path.join(HERE, "update.sql")
    with open(out, "w") as f:
        f.write("\n".join(sql_parts) + "\n")
    print(f"\nTotal: {total} alts. SQL: {out}")

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

    # Verify
    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    import re
    imgs = re.findall(r'<img[^>]+>', r.stdout)
    empty = 0
    for tag in imgs:
        alt = re.search(r'\salt="([^"]*)"', tag)
        if alt and alt.group(1).strip() == "":
            empty += 1
    print(f"Empty alts remaining on home: {empty}")


if __name__ == "__main__":
    main()
