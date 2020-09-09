import '../css/default.scss';
import toastr from 'toastr';

const routes = require('../../../../../public/js/fos_js_routes.json');
import Routing from '../../../../../public/bundles/fosjsrouting/js/router.min.js';

Routing.setRoutingData(routes);

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }