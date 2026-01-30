import './stimulus_bootstrap.js';
// Import CSS Folder
import './css/app.css';
import './css/color.css';
import './css/style.css';

// Import Vendor CSS
import './vendor/font-awesome/css/all.min.css';
import './vendor/bootstrap-icons/bootstrap-icons.min.css';
import './vendor/glightbox-master/dist/css/glightbox.min.css';
import './vendor/overlayscrollbars/css/overlayscrollbars.min.css';
import './vendor/choices/styles/choices.min.css';
import './vendor/plyr/dist/plyr.min.css';
import './vendor/tiny-slider/tiny-slider.css';

// Import JS Folder
import './js/functions.js';
import './js/notyf.js';
import './js/password-toggle.js';

// Import Vendor JS
import './vendor/bootstrap/dist/js/bootstrap.min.js';
import './vendor/flatpickr/dist/flatpickr.js';
import './vendor/glightbox-master/dist/js/glightbox.min.js';
import './vendor/OverlayScrollbars-master/js/OverlayScrollbars.min.js';
import './vendor/choices/choices.index.js';
import './vendor/colors/colors.index.js';
import './vendor/plyr/plyr.js';
import './vendor/tiny-slider/tiny-slider.js';
import './vendor/notyf/notyf.index.js';
import './vendor/chart.js/auto.js';
import './vendor/chart.js/helpers.js';
import './vendor/chartjs-plugin-zoom/chartjs-plugin-zoom.index.js';

// assets/app.js
import zoomPlugin from 'chartjs-plugin-zoom';

// register globally for all charts
document.addEventListener('chartjs:init', function (event) {
    const Chart = event.detail.Chart;
    Chart.register(zoomPlugin);
});
