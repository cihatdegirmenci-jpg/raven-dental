#!/usr/bin/env python3
"""
Footer social + payment icon cleanup.

Module 61 (Social Icons) — from 3 items (FB#, TW#, IG#) → 3 items:
  - Facebook → https://www.facebook.com/dentmadikal.co/
  - Instagram (raven.dental) → https://www.instagram.com/raven.dental/
  - Instagram (ravendisdeposu) → https://www.instagram.com/ravendisdeposu/
  (Twitter removed — no account)

Module 228 (Payment Icons) — from 6 items → 2 items:
  - Visa, Mastercard kept
  - Amex, Discover, Paypal, Stripe removed (not supported by QNB Pay TR)

Input:  before-module-{61,228}.json
Output: after-module-{61,228}.json + update.sql
"""
import copy
import json
import os
import uuid

HERE = os.path.dirname(os.path.abspath(__file__))


def load(name):
    with open(os.path.join(HERE, name)) as f:
        return json.load(f)


def save(name, obj):
    with open(os.path.join(HERE, name), "w") as f:
        json.dump(obj, f, ensure_ascii=True, separators=(",", ":"))


def transform_social(data):
    items = data["items"]
    facebook = items[0]
    instagram = items[2]
    assert facebook["name"] == "Facebook"
    assert instagram["name"] == "Instagram"

    # Facebook → real URL, _blank, noopener
    facebook["link"]["url"] = "https://www.facebook.com/dentmadikal.co/"
    facebook["link"]["target"] = "true"  # Journal3 stores "true" = _blank
    facebook["link"]["rel"] = "noopener"

    # Instagram (raven.dental) — repurpose existing item
    instagram["name"] = "Instagram - Raven Dental"
    instagram["title"]["lang_1"] = "Instagram - Raven Dental"
    instagram["link"]["url"] = "https://www.instagram.com/raven.dental/"
    instagram["link"]["target"] = "true"
    instagram["link"]["rel"] = "noopener"

    # New: Instagram (ravendisdeposu) — clone the instagram item
    instagram2 = copy.deepcopy(instagram)
    instagram2["id"] = str(uuid.uuid4())
    instagram2["name"] = "Instagram - Raven Diş Deposu"
    instagram2["title"]["lang_1"] = "Instagram - Raven Diş Deposu"
    instagram2["link"]["url"] = "https://www.instagram.com/ravendisdeposu/"

    # Final order: Facebook, Instagram (raven.dental), Instagram (ravendisdeposu)
    data["items"] = [facebook, instagram, instagram2]
    return data


def transform_payments(data):
    items = data["items"]
    visa = items[0]
    mastercard = items[1]
    assert visa["name"] == "Visa"
    assert mastercard["name"] == "Mastercard"
    data["items"] = [visa, mastercard]
    return data


def main():
    social_before = load("before-module-61-social.json")
    payments_before = load("before-module-228-payments.json")

    social_after = transform_social(copy.deepcopy(social_before))
    payments_after = transform_payments(copy.deepcopy(payments_before))

    save("after-module-61-social.json", social_after)
    save("after-module-228-payments.json", payments_after)

    # Compose update.sql (escape single quotes for SQL)
    def esc(s):
        return s.replace("\\", "\\\\").replace("'", "''")

    sql_lines = [
        "-- Footer social + payment icon cleanup",
        "-- Module 61 (Social): FB real URL, IG raven.dental, IG ravendisdeposu (Twitter removed)",
        "-- Module 228 (Payments): Visa, MC only (Amex/Discover/Paypal/Stripe removed)",
        "",
        "START TRANSACTION;",
        "",
        f"UPDATE oc_journal3_module SET module_data='{esc(json.dumps(social_after, ensure_ascii=True, separators=(',', ':')))}' WHERE module_id=61;",
        "",
        f"UPDATE oc_journal3_module SET module_data='{esc(json.dumps(payments_after, ensure_ascii=True, separators=(',', ':')))}' WHERE module_id=228;",
        "",
        "COMMIT;",
        "",
    ]
    with open(os.path.join(HERE, "update.sql"), "w") as f:
        f.write("\n".join(sql_lines))

    print(f"Social: {len(social_before['items'])} items → {len(social_after['items'])} items")
    print(f"Payments: {len(payments_before['items'])} items → {len(payments_after['items'])} items")
    print(f"SQL: {os.path.join(HERE, 'update.sql')}")


if __name__ == "__main__":
    main()
