window._ = require('lodash');
import '@coreui/coreui';
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    window.dt = require( 'datatables.net-bs4' )( window, $ );
} catch (e) {}


