// JsBarcode embarqué en local (pages code-barres), exposé en global car les
// vues Blade appellent `JsBarcode(...)` directement dans des scripts inline —
// voir resources/css/vendor.css pour la raison de ne plus dépendre d'un CDN.
import JsBarcode from 'jsbarcode';

window.JsBarcode = JsBarcode;
