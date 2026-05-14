#!/usr/bin/env python3
"""
Token argumanını al, customCodeHeader'a Google Search Console meta tag inject et.

Kullanım:
  python3 insert_meta.py "ABC123_TOKEN_HERE"
"""
import os
import re
import subprocess
import sys

DC = "/Users/ipci/raven-dental/docker-compose.yml"
HERE = os.path.dirname(os.path.abspath(__file__))


def fetch_current():
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        "mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        "-e \"SELECT setting_value FROM oc_journal3_setting "
        "WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    val = r.stdout.decode("utf-8").rstrip("\n")
    return val


def main():
    if len(sys.argv) < 2:
        print("Usage: python3 insert_meta.py TOKEN")
        sys.exit(1)
    token = sys.argv[1].strip()
    if not re.match(r'^[A-Za-z0-9_\-]+$', token):
        print("Token format suspicious. Aborting.")
        sys.exit(1)

    current = fetch_current()
    meta_tag = f'<meta name="google-site-verification" content="{token}" />'

    # Insert at the very top of customCodeHeader (above the SEO boost block)
    if 'google-site-verification' in current:
        # Replace existing
        current = re.sub(
            r'<meta name="google-site-verification"[^>]*/>',
            meta_tag,
            current,
        )
    else:
        current = meta_tag + "\n" + current

    escaped = current.replace("\\", "\\\\").replace("'", "''")
    sql = (
        "SET NAMES utf8mb4;\n"
        "START TRANSACTION;\n"
        f"UPDATE oc_journal3_setting SET setting_value='{escaped}' "
        f"WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\n"
        "COMMIT;\n"
    )
    out = os.path.join(HERE, "update.sql")
    with open(out, "w") as f:
        f.write(sql)
    print(f"SQL written: {out}")

    cmd = f"docker compose -f {DC} exec -T mysql mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {out}"
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        sys.exit(1)
    print("Applied OK")

    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )

    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    found = 'google-site-verification' in r.stdout and token in r.stdout
    print(f"Meta tag in HTML: {found}")


if __name__ == "__main__":
    main()
