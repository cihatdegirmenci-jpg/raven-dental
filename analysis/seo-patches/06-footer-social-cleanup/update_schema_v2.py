#!/usr/bin/env python3
"""
v2: properly handle newlines. The original DB stored real newlines (0x0A).
mysql -N output escapes them as literal '\n' text. We must convert back to
real newlines before storing.
"""
import os
import re
import subprocess

DB_USER = "root"
DB_PASS = "rootpw"
DB_NAME = "ravenden_1"
DC = "/Users/ipci/raven-dental/docker-compose.yml"

SOCIAL_URLS = [
    "https://www.facebook.com/dentmadikal.co/",
    "https://www.instagram.com/raven.dental/",
    "https://www.instagram.com/ravendisdeposu/",
]

HERE = os.path.dirname(os.path.abspath(__file__))


def fetch_setting_raw():
    """Use --raw to get unescaped output (preserves newlines)."""
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        f"mysql --default-character-set=utf8mb4 -u{DB_USER} -p{DB_PASS} {DB_NAME} -N -B --raw "
        f"-e \"SELECT setting_value FROM oc_journal3_setting "
        f"WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    if r.returncode != 0:
        raise RuntimeError(r.stderr.decode())
    val = r.stdout.decode("utf-8")
    # mysql appends a trailing newline; strip exactly one
    if val.endswith("\n"):
        val = val[:-1]
    return val


def main():
    current = fetch_setting_raw()
    print(f"Fetched {len(current)} bytes; newlines: {current.count(chr(10))}")

    with open(os.path.join(HERE, "before-custom-code-header-raw.txt"), "w") as f:
        f.write(current)

    # If already has sameAs, skip
    if '"sameAs"' not in current:
        marker = '"Implantology", "Surgery"]'
        sameas_block = (
            marker
            + ',\n  "sameAs": [\n    "'
            + '",\n    "'.join(SOCIAL_URLS)
            + '"\n  ]'
        )
        if marker not in current:
            raise RuntimeError("knowsAbout marker not found")
        current = current.replace(marker, sameas_block, 1)

    # Drop "English" from availableLanguage
    current = current.replace('["Turkish", "English"]', '["Turkish"]')

    with open(os.path.join(HERE, "after-custom-code-header-raw.txt"), "w") as f:
        f.write(current)

    # SQL: only escape single quotes and backslashes (none expected here)
    escaped = current.replace("\\", "\\\\").replace("'", "''")
    sql = (
        "SET NAMES utf8mb4;\n"
        "START TRANSACTION;\n"
        "UPDATE oc_journal3_setting SET setting_value='"
        + escaped
        + "' WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\n"
        "COMMIT;\n"
    )
    with open(os.path.join(HERE, "update_schema_v2.sql"), "w") as f:
        f.write(sql)
    print(f"SQL written: update_schema_v2.sql ({len(sql)} bytes)")
    print(f"Final value: {len(current)} chars, sameAs: {'sameAs' in current}")


if __name__ == "__main__":
    main()
