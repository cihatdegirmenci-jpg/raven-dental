# Raven Dental — Dokümantasyon İndeksi

> Tüm proje dokümanları bu klasördedir. Yeni session'da ilk iş `/CLAUDE.md` + bu indeks + `00-QUICK-CONTEXT.md` okumaktır.

## Dokümanlar

| Dosya | İçerik | Ne Zaman Oku |
|---|---|---|
| [00-QUICK-CONTEXT.md](./00-QUICK-CONTEXT.md) | Nerede kaldık, son durum özeti | **HER session başında** |
| [01-PROJECT-OVERVIEW.md](./01-PROJECT-OVERVIEW.md) | Site profili, iş tanımı, hedef pazar | Projeye ilk başlarken |
| [02-ARCHITECTURE.md](./02-ARCHITECTURE.md) | OpenCart + Journal3 + hosting topology | Mimari kararlar |
| [03-DATABASE-SCHEMA.md](./03-DATABASE-SCHEMA.md) | DB yapısı, kritik tablolar, sorgular | DB değişikliği yaparken |
| [04-THEME-STRUCTURE.md](./04-THEME-STRUCTURE.md) | Journal3 internals, twig templates, OCMOD | Tema dosyası düzenlerken |
| [05-SEO-STATUS.md](./05-SEO-STATUS.md) | Mevcut SEO durumu (bu oturum sonrası) | SEO işi yaparken |
| [06-SECURITY-STATUS.md](./06-SECURITY-STATUS.md) | Güvenlik analizi + açık kalan riskler | Güvenlik kararı |
| [07-PERFORMANCE.md](./07-PERFORMANCE.md) | Performans baseline + optimizasyon planı | Hız iyileştirmesi |
| [08-CHANGES-MADE.md](./08-CHANGES-MADE.md) | **Bu oturumda yapılan TÜM değişiklikler** | Geçmişe bakarken / debugging |
| [09-LESSONS-LEARNED.md](./09-LESSONS-LEARNED.md) | **Hatalar ve dersler — HER ZAMAN REFERANS** | Her session'da |
| [10-WORKING-RULES.md](./10-WORKING-RULES.md) | Güvenli üretim çalışma akışı | Her değişiklikten önce |
| [11-MIGRATION-PLAN.md](./11-MIGRATION-PLAN.md) | NetInternet KVM VPS'e taşıma planı | VPS hazır olduğunda |
| [12-ROADMAP.md](./12-ROADMAP.md) | Yapılacaklar (checkbox'lı, öncelikli) | **HER session'da** |
| [13-COMPETITOR-ANALYSIS.md](./13-COMPETITOR-ANALYSIS.md) | TR diş hekimliği aleti rakipleri | İçerik/SEO strateji |
| [14-CONTENT-PLAN.md](./14-CONTENT-PLAN.md) | Meta texts, blog plan, içerik takvimi | İçerik üretimi |

## Hızlı Erişim

- **Yeni session başında:** `00-QUICK-CONTEXT.md` + `09-LESSONS-LEARNED.md` + `12-ROADMAP.md`
- **Üretime dokunmadan önce:** `10-WORKING-RULES.md` + ilgili spesifik doc
- **Hata yaparsam:** `09-LESSONS-LEARNED.md`'ye ekle
- **Değişiklik yaptığımda:** `08-CHANGES-MADE.md`'ye yaz + ilgili spesifik doc'u güncelle

## Doc Yazma Kuralları

1. Markdown, Türkçe
2. Header'lar `## Kısa Başlık` formatında
3. Tablolar bol bol — okuması kolay
4. **Tek doğru bilgi kaynağı:** her şey bir doc'ta, tekrar yok
5. Aktif/güncel: ölen TODO'lar silinir, tarihler güncellenir
6. Çapraz referans: `[ilgili doc](./XX-ad.md#bölüm)`

## Klasör Yapısı (Repository)

```
raven-dental/
├── CLAUDE.md           ← Çalışma kuralları (kök)
├── README.md           ← Proje genel açıklama
├── .gitignore
├── code/               ← OpenCart kodu (kopya)
├── db/                 ← DB dump'ları (.sql)
├── docs/               ← BU klasör
├── analysis/           ← Üretilen raporlar
└── migration-plan/     ← VPS taşıma scriptleri
```
