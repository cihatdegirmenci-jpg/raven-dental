#!/usr/bin/env python3
"""
Apply 18 rewritten category descriptions to Docker DB.
Pre-flight check: scan for any third-party brand names that shouldn't be there.
"""
import os
import re
import subprocess
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
DC = "/Users/ipci/raven-dental/docker-compose.yml"

# Banned: third-party manufacturer / product brand names
BANNED_BRANDS = [
    "Mani", "Hu-Friedy", "Dentsply", "Maillefer",
    "NSK", "W&H", "W&amp;H", "Bien-Air", "Bien Air", "KaVo",
    "Brasseler", "Komet", "FKG", "VDW", "Dentaurum", "GAC",
    "Hanau", "Whip Mix", "KerrHawe", "Kerr Hawe",
    "Damon",
    # Specific product names
    "ProTaper", "WaveOne", "Reciproc", "Hyflex", "X-Smart", "X-Smart Plus",
    "Ti-Max", "Synea", "ChiroPro", "Implantmed", "Surgic Pro",
    "Volvere", "Elcomed", "Endo Mate", "ProMate", "Endo Touch",
    "MASTERtorque", "EXPERTtorque", "INTRAmatic", "INTRAcompact",
    # Hat tips / branding
    "Tip 1 Aerator", "Tip 2 Aerator",
]


def load_descriptions():
    sys.path.insert(0, HERE)
    from descriptions import DESCRIPTIONS
    return DESCRIPTIONS


def preflight(descs):
    bad = []
    for cid, text in descs.items():
        for brand in BANNED_BRANDS:
            # Word-boundary'ish; check case-insensitive
            if re.search(r"\b" + re.escape(brand) + r"\b", text, flags=re.IGNORECASE):
                bad.append((cid, brand))
    if bad:
        print("PREFLIGHT FAIL — banned brand mentions found:")
        for cid, brand in bad:
            print(f"  cat {cid}: {brand}")
        sys.exit(1)
    print(f"Preflight OK — no banned brand names in {len(descs)} categories")


def build_sql(descs):
    parts = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    for cid, text in sorted(descs.items()):
        # Escape: \ → \\, ' → ''
        escaped = text.replace("\\", "\\\\").replace("'", "''")
        parts.append(
            f"UPDATE oc_category_description SET description='{escaped}' "
            f"WHERE language_id=2 AND category_id={cid};"
        )
    parts.append("COMMIT;")
    return "\n".join(parts) + "\n"


def apply_sql(path):
    cmd = (
        f"docker compose -f {DC} exec -T mysql "
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {path}"
    )
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        raise SystemExit(1)
    print("Applied OK")


def verify(descs):
    # Clear cache
    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )
    # Fetch each category page, look for banned brands
    fails = []
    ok = 0
    for cid in sorted(descs.keys()):
        cmd = [
            "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
            f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
            f"-e \"SELECT description FROM oc_category_description "
            f"WHERE language_id=2 AND category_id={cid};\""
        ]
        r = subprocess.run(cmd, capture_output=True, text=False)
        stored = r.stdout.decode("utf-8").rstrip("\n")
        if stored.rstrip() == descs[cid].rstrip():
            ok += 1
        else:
            fails.append((cid, len(stored), len(descs[cid])))
    print(f"Verify: {ok}/{len(descs)} exact match in DB")
    if fails:
        for f in fails:
            print(f"  MISMATCH cat={f[0]}: stored {f[1]} chars vs source {f[2]} chars")


def main():
    descs = load_descriptions()
    preflight(descs)
    sql = build_sql(descs)
    out = os.path.join(HERE, "update.sql")
    with open(out, "w") as f:
        f.write(sql)
    print(f"SQL: {out} ({len(sql)} bytes)")
    apply_sql(out)
    verify(descs)


if __name__ == "__main__":
    main()
