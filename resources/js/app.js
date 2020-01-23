window._ = require('lodash');
import '@coreui/coreui';
import 'select2';                       // globally assign select2 fn to $ element
import 'select2/dist/css/select2.min.css';  // optional if you have css loader
import 'select2-bootstrap-theme/dist/select2-bootstrap.min.css';  // optional if you have css loader
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    window.dt = require( 'datatables.net-bs4' )( window, $ );
} catch (e) {}


