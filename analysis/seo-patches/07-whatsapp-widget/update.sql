SET NAMES utf8mb4;
START TRANSACTION;
UPDATE oc_journal3_setting SET setting_value='<!-- WhatsApp Business floating widget — Raven Dental -->
<a href="https://wa.me/905528530399?text=Merhaba%2C%20%C3%BCr%C3%BCnleriniz%20hakk%C4%B1nda%20bilgi%20almak%20istiyorum."
   target="_blank"
   rel="noopener"
   aria-label="WhatsApp ile iletişim — Raven Dental"
   class="raven-wa-fab"
   id="raven-wa-fab">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="#fff" aria-hidden="true" focusable="false">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.611-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
  </svg>
  <span class="raven-wa-fab__pulse" aria-hidden="true"></span>
</a>
<style>
.raven-wa-fab{position:fixed;right:18px;bottom:18px;width:56px;height:56px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(37,211,102,.35),0 2px 6px rgba(0,0,0,.15);z-index:9998;text-decoration:none;transition:transform .15s ease,box-shadow .15s ease}
.raven-wa-fab:hover,.raven-wa-fab:focus-visible{transform:scale(1.06);box-shadow:0 6px 22px rgba(37,211,102,.5),0 3px 10px rgba(0,0,0,.2);outline:none}
.raven-wa-fab:focus-visible{outline:3px solid #128C7E;outline-offset:2px}
.raven-wa-fab__pulse{position:absolute;inset:0;border-radius:50%;background:#25D366;opacity:.5;animation:raven-wa-pulse 2s ease-out infinite;pointer-events:none;z-index:-1}
@keyframes raven-wa-pulse{0%{transform:scale(1);opacity:.5}100%{transform:scale(1.6);opacity:0}}
@media (prefers-reduced-motion:reduce){.raven-wa-fab__pulse{animation:none;display:none}.raven-wa-fab{transition:none}}
@media (max-width:480px){.raven-wa-fab{width:52px;height:52px;right:14px;bottom:14px}.raven-wa-fab svg{width:28px;height:28px}}
</style>' WHERE setting_group='custom_code' AND setting_name='customCodeFooter';
COMMIT;
