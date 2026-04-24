import './bootstrap';
import 'bootstrap';
import $ from 'jquery';
import select2 from 'select2';
import Highcharts from 'highcharts';
import Swal from 'sweetalert2';

// Penting: Agar Select2 mengenali jQuery
select2();

// Masukkan ke global window agar script inline di Blade tetap jalan
window.$ = window.jQuery = $;
window.Highcharts = Highcharts;
window.Swal = Swal;

$(document).ready(function () {
    // Inisialisasi global jika diperlukan
    $('.select2').select2({
        theme: 'bootstrap-5'
    });
});