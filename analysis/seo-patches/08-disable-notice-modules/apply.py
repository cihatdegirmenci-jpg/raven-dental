#!/usr/bin/env python3
"""
Disable Journal3 modules:
  - 56  → Header Notice (top promotional bar)
  - 137 → Notification Module (cookie consent at bottom)

Sets general.status.status = "false" in module_data JSON.
Backups stored as before-module-<id>.json
"""
import json
import os
import subprocess

HERE = os.path.dirname(os.path.abspath(__file__))
DC = "/Users/ipci/raven-dental/docker-compose.yml"
MODULE_IDS = [56, 137]


def fetch(mid):
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


def main():
    sql_parts = ["SET NAMES utf8mb4;", "START TRANSACTION;"]
    for mid in MODULE_IDS:
        raw = fetch(mid)
        with open(os.path.join(HERE, f"before-module-{mid}.json"), "w") as f:
            f.write(raw)
        data = json.loads(raw)
        assert data["general"]["status"]["status"] == "true", f"module {mid} already disabled?"
        data["general"]["status"]["status"] = "false"
        new_json = json.dumps(data, ensure_ascii=True, separators=(",", ":"))
        with open(os.path.join(HERE, f"after-module-{mid}.json"), "w") as f:
            f.write(new_json)
        escaped = new_json.replace("\\", "\\\\").replace("'", "''")
        sql_parts.append(
            f"UPDATE oc_journal3_module SET module_data='{escaped}' WHERE module_id={mid};"
        )
    sql_parts.append("COMMIT;")
    sql = "\n".join(sql_parts) + "\n"

    out = os.path.join(HERE, "update.sql")
    with open(out, "w") as f:
        f.write(sql)
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

    r = subprocess.run(["curl", "-s", "http://localhost:8000/"], capture_output=True, text=True)
    hn = r.stdout.count("module-header_notice-56")
    cn = r.stdout.count("module-notification-137")
    print(f"Render check — header_notice-56: {hn} hits, notification-137: {cn} hits")


if __name__ == "__main__":
    main()
