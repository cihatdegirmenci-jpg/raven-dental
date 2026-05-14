#!/usr/bin/env python3
"""
Apply WhatsApp widget to customCodeFooter (Journal3 setting).
Idempotent: removes any previous raven-wa-fab block, then injects fresh.
"""
import os
import re
import subprocess

HERE = os.path.dirname(os.path.abspath(__file__))
DC = "/Users/ipci/raven-dental/docker-compose.yml"
MARKER_START = "<!-- WhatsApp Business floating widget — Raven Dental -->"
MARKER_END = "</style>"


def fetch_current():
    cmd = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        "mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        "-e \"SELECT setting_value FROM oc_journal3_setting "
        "WHERE setting_group='custom_code' AND setting_name='customCodeFooter';\""
    ]
    r = subprocess.run(cmd, capture_output=True, text=False)
    if r.returncode != 0:
        raise RuntimeError(r.stderr.decode())
    val = r.stdout.decode("utf-8")
    if val.endswith("\n"):
        val = val[:-1]
    return val


def main():
    with open(os.path.join(HERE, "widget.html")) as f:
        widget = f.read().rstrip("\n")

    current = fetch_current()
    print(f"customCodeFooter before: {len(current)} bytes")

    # Remove previous block if present (idempotent re-runs)
    if MARKER_START in current:
        # delete from MARKER_START up to and including the first </style>
        pattern = re.escape(MARKER_START) + r".*?" + re.escape(MARKER_END)
        current = re.sub(pattern, "", current, count=1, flags=re.DOTALL).rstrip()

    new_value = (current + "\n\n" + widget) if current else widget

    # SQL — escape ' → '' and \ → \\
    escaped = new_value.replace("\\", "\\\\").replace("'", "''")
    sql = (
        "SET NAMES utf8mb4;\n"
        "START TRANSACTION;\n"
        "UPDATE oc_journal3_setting SET setting_value='"
        + escaped
        + "' WHERE setting_group='custom_code' AND setting_name='customCodeFooter';\n"
        "COMMIT;\n"
    )
    out = os.path.join(HERE, "update.sql")
    with open(out, "w") as f:
        f.write(sql)
    print(f"SQL written: {out} ({len(sql)} bytes)")

    cmd = (
        f"docker compose -f {DC} exec -T mysql "
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {out}"
    )
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        raise SystemExit(1)
    print("Applied OK")

    # Clear cache and fetch
    subprocess.run(
        ["docker", "compose", "-f", DC, "exec", "-T", "apache", "sh", "-c",
         "find /var/www/storage/cache -type f -delete"],
        check=False,
    )
    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    has_widget = "raven-wa-fab" in r.stdout
    print(f"Widget in rendered HTML: {has_widget}")


if __name__ == "__main__":
    main()
