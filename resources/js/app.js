import './bootstrap';

import Alpine from 'alpinejs';
import axios from 'axios';
window.Alpine = Alpine;

Alpine.start();


// Configuración global de Axios
axios.defaults.baseURL = window.location.origin; // Puedes configurar tu URL base si lo necesitas
window.axios = axios;
