#!/usr/bin/env python3
"""
Restore customCodeHeader from before-* backup, then apply sameAs + Turkish-only update.
The backup file (before-custom-code-header.txt) was dumped with `mysql -N` which
escapes newlines as '\n' text. Reverse that escaping to reconstruct original bytes.
"""
import os
import subprocess

HERE = os.path.dirname(os.path.abspath(__file__))
DC = "/Users/ipci/raven-dental/docker-compose.yml"

SOCIAL_URLS = [
    "https://www.facebook.com/dentmadikal.co/",
    "https://www.instagram.com/raven.dental/",
    "https://www.instagram.com/ravendisdeposu/",
]


def reverse_mysql_n_escape(s: str) -> str:
    """Reverse mysql -N escaping. Order matters: handle \\ last to avoid double-processing."""
    out = []
    i = 0
    while i < len(s):
        if s[i] == "\\" and i + 1 < len(s):
            c = s[i + 1]
            if c == "n":
                out.append("\n")
                i += 2
                continue
            elif c == "t":
                out.append("\t")
                i += 2
                continue
            elif c == "r":
                out.append("\r")
                i += 2
                continue
            elif c == "0":
                out.append("\0")
                i += 2
                continue
            elif c == "\\":
                out.append("\\")
                i += 2
                continue
        out.append(s[i])
        i += 1
    return "".join(out)


def main():
    with open(os.path.join(HERE, "before-custom-code-header.txt")) as f:
        escaped = f.read()
    if escaped.endswith("\n"):
        escaped = escaped[:-1]  # strip trailing newline from CLI dump

    original = reverse_mysql_n_escape(escaped)
    print(f"Original reconstructed: {len(original)} chars, newlines={original.count(chr(10))}")

    with open(os.path.join(HERE, "before-custom-code-header-raw.txt"), "w") as f:
        f.write(original)

    # Modify: add sameAs, drop English
    current = original
    if '"sameAs"' not in current:
        marker = '"Implantology", "Surgery"]'
        sameas_block = (
            marker
            + ',\n  "sameAs": [\n    "'
            + '",\n    "'.join(SOCIAL_URLS)
            + '"\n  ]'
        )
        if marker not in current:
            raise RuntimeError("knowsAbout marker not found in original")
        current = current.replace(marker, sameas_block, 1)
    current = current.replace('["Turkish", "English"]', '["Turkish"]')

    with open(os.path.join(HERE, "after-custom-code-header-raw.txt"), "w") as f:
        f.write(current)

    # SQL escape: only ' → '' and \ → \\
    escaped_sql = current.replace("\\", "\\\\").replace("'", "''")
    sql = (
        "SET NAMES utf8mb4;\n"
        "START TRANSACTION;\n"
        "UPDATE oc_journal3_setting SET setting_value='"
        + escaped_sql
        + "' WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\n"
        "COMMIT;\n"
    )
    out_sql = os.path.join(HERE, "update_schema_final.sql")
    with open(out_sql, "w") as f:
        f.write(sql)
    print(f"SQL: {out_sql} ({len(sql)} bytes)")

    # Apply
    print("Applying via docker compose mysql ...")
    cmd = (
        f"docker compose -f {DC} exec -T mysql "
        f"mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 < {out_sql}"
    )
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if r.returncode != 0:
        print("STDERR:", r.stderr)
        raise SystemExit(1)
    print("Applied OK")

    # Verify
    cmd2 = [
        "docker", "compose", "-f", DC, "exec", "-T", "mysql", "sh", "-c",
        "mysql --default-character-set=utf8mb4 -uroot -prootpw ravenden_1 -N -B --raw "
        "-e \"SELECT LENGTH(setting_value), setting_value LIKE '%sameAs%' "
        "FROM oc_journal3_setting WHERE setting_group='custom_code' AND setting_name='customCodeHeader';\""
    ]
    r = subprocess.run(cmd2, capture_output=True, text=True)
    print("Verify:", r.stdout.strip())


if __name__ == "__main__":
    main()
