/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;
import 'bootstrap';
import '../../node_modules/popper.js/dist/popper.min.js';
import '../../node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js';
import '../../node_modules/bootstrap-select/dist/js/bootstrap-select.js';


// import 'datatables.net';
// import 'jszip';
// import 'pdfmake';
// import * as pdfFonts from 'pdfmake/build/vfs_fonts';
// pdfMake.vfs = pdfFonts.pdfMake.vfs;
// import 'datatables.net-dt';
// import 'datatables.net-autofill-dt';
// import 'datatables.net-buttons-dt';
// import 'datatables.net-buttons/js/buttons.colVis.js';
// import 'datatables.net-buttons/js/buttons.flash.js';
// import 'datatables.net-buttons/js/buttons.html5.js';
// import 'datatables.net-buttons/js/buttons.print.js';
// import 'datatables.net-colreorder-dt';
// import 'datatables.net-fixedcolumns-dt';
// import 'datatables.net-fixedheader-dt';
// import 'datatables.net-keytable-dt';
// import 'datatables.net-responsive-dt';
// import 'datatables.net-rowgroup-dt';
// import 'datatables.net-rowreorder-dt';
// import 'datatables.net-scroller-dt';
// import 'datatables.net-select-dt';

// require('selectize');

import bsCustomFileInput from 'bs-custom-file-input';
$(document).ready(function () {
    bsCustomFileInput.init()
})

import moment from 'moment';
window.moment = moment;

$('.prevent-multiple-submits').on('submit', function(){
    $('.btn-submit').attr('disabled','true');
})


// window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });
