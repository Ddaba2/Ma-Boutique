// Chart.js embarqué en local (dashboard + rapport de performances), exposé en
// global car les vues Blade instancient `new Chart(...)` directement dans des
// scripts inline plutôt que via un import — voir resources/css/vendor.css
// pour la raison de ne plus dépendre d'un CDN.
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;
