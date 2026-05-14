#!/usr/bin/env python3
"""
journal3.php productStat() label'larını TR hardcode et.
JSON Settings + Variable sistemi karmaşık; en stabil yol budur.
"""
import os
import re

PHP_FILE = "/Users/ipci/raven-dental/code/system/library/journal3.php"

# (setting_name → Turkish label)
LABEL_MAP = {
    "productPageStyleProductManufacturerText": "Marka",
    "productPageStyleProductModelText": "Model",
    "productPageStyleProductSKUText": "Stok Kodu",
    "productPageStyleProductUPCText": "UPC",
    "productPageStyleProductEANText": "EAN",
    "productPageStyleProductJANText": "JAN",
    "productPageStyleProductISBNText": "ISBN",
    "productPageStyleProductMPNText": "MPN",
    "productPageStyleProductLocationText": "Konum",
    "productPageStyleProductWeightText": "Ağırlık",
    "productPageStyleProductDimensionText": "Ölçüler",
    "productPageStyleProductRewardText": "Ödül Puanı",
    "productPageStyleProductPointsText": "Puan",
    "productPageStyleProductStockText": "Stok",
    "productPageStyleProductInStockText": "Stokta",
    "productPageStyleProductViewsText": "Görüntülenme",
    "productPageStyleProductSoldText": "Satılan",
    "productPageStyleProductCountdownText": "Kampanya bitimine",
    # Quickview variants if they reference same key (they do — same settings)
}


def main():
    with open(PHP_FILE, "r", encoding="utf-8") as f:
        src = f.read()

    changes = 0
    for setting, tr in LABEL_MAP.items():
        # Match: $label = $this->settings->get('SETTING');
        # Replace with: $label = $this->settings->get('SETTING') ?: 'TR';
        pattern = re.compile(
            r"\$label\s*=\s*\$this->settings->get\('" + re.escape(setting) + r"'\);"
        )
        replacement = f"$label = '{tr}'; // [i18n-tr] forced from settings->get('{setting}')"
        new_src, n = pattern.subn(replacement, src)
        if n > 0:
            src = new_src
            changes += n
            print(f"{setting} → '{tr}' ({n} match)")

    if changes == 0:
        print("NO CHANGES — patterns didn't match. Already patched?")
        return

    with open(PHP_FILE, "w", encoding="utf-8") as f:
        f.write(src)
    print(f"\nTotal: {changes} replacements")


if __name__ == "__main__":
    main()
