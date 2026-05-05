import './bootstrap';
import './dashboard';

import "flyonui/flyonui"
import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';

// Expose SignaturePad globally for use in Blade templates
window.SignaturePad = SignaturePad;

window.Alpine = Alpine;

Alpine.start();
