import './bootstrap';
import './dashboard';
import './notifications';

import "flyonui/flyonui"
import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';

import Swal from 'sweetalert2';

// Expose globals for use in Blade templates
window.SignaturePad = SignaturePad;
window.Swal = Swal;

window.Alpine = Alpine;

Alpine.start();
