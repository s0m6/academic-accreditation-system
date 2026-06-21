import './bootstrap';
import './dashboard';
import './notifications';

import "flyonui/flyonui"
import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';

import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

// Expose globals for use in Blade templates
window.SignaturePad = SignaturePad;
window.Swal = Swal;
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
