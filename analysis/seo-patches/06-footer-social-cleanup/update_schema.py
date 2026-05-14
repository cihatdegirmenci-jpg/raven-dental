#!/usr/bin/env python3
"""
Update Organization schema in journal3 customCodeHeader:
  - Add sameAs array with social URLs
  - Drop "English" from availableLanguage (site is TR-only per recent commit)
"""
import subprocess
import json
import re
import os

DB_USER = "root"
DB_PASS = "rootpw"
DB_NAME = "ravenden_1"

SOCIAL_URLS = [
    "https://www.facebook.com/dentmadikal.co/",
    "https://www.instagram.com/raven.dental/",
    "https://www.instagram.com/ravendisdeposu/",
]


def fetch_setting():
    cmd = [
        "docker", "compose", "-f", "/Users/ipci/raven-dental/docker-compose.yml",
        "exec", "-T", "mysql", "sh", "-c",
        f"mysql -u{DB_USER} -p{DB_PASS} {DB_NAME} --default-character-set=utf8mb4 -N -B "
        f"-e \"SELECT setting_value FROM oc_journal3_setting WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=True)
    if r.returncode != 0:
        raise RuntimeError(r.stderr)
    return r.stdout.strip()


def write_setting(new_value):
    # save backup + write SQL
    here = os.path.dirname(os.path.abspath(__file__))
    sql = "UPDATE oc_journal3_setting SET setting_value=%s WHERE setting_group='custom_code' AND setting_name='customCodeHeader';"
    with open(os.path.join(here, "update_schema.sql"), "w") as f:
        # Use repr-style single-quote escape
        escaped = new_value.replace("\\", "\\\\").replace("'", "''")
        f.write("START TRANSACTION;\n")
        f.write(sql.replace("%s", "'" + escaped + "'") + "\n")
        f.write("COMMIT;\n")
    return os.path.join(here, "update_schema.sql")


def main():
    current = fetch_setting()
    print(f"Fetched {len(current)} bytes")

    here = os.path.dirname(os.path.abspath(__file__))
    with open(os.path.join(here, "before-custom-code-header.txt"), "w") as f:
        f.write(current)

    # The setting_value as stored uses literal "\n" sequences (2-char) which MySQL
    # CLI returns as is. Locate the JSON-LD block and edit it.

    # Add sameAs after knowsAbout array
    # The schema's knowsAbout line ends with: ]\n}\n</script>
    # We need to insert sameAs BEFORE the closing }
    if '"sameAs"' in current:
        print("sameAs already present — skipping addition")
    else:
        # Use plain str.replace to avoid re.sub interpreting "\n" as newline in replacement
        marker = '"Implantology", "Surgery"]'
        sameas_block = (
            marker
            + ',\\n  "sameAs": [\\n    "'
            + '",\\n    "'.join(SOCIAL_URLS)
            + '"\\n  ]'
        )
        if marker not in current:
            raise RuntimeError("knowsAbout marker not found")
        current = current.replace(marker, sameas_block, 1)

    # Drop "English" from availableLanguage
    current = current.replace('["Turkish", "English"]', '["Turkish"]')

    with open(os.path.join(here, "after-custom-code-header.txt"), "w") as f:
        f.write(current)

    sql_path = write_setting(current)
    print(f"SQL written: {sql_path}")
    print(f"Length: {len(current)} bytes")


if __name__ == "__main__":
    main()
