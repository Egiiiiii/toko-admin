import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// INI BAGIAN PENTINGNYA:
// Pastikan Base URL mengambil dari VITE_API_BASE_URL
window.axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'https://api.roomify3.my.id'; 

// Cek kredensial (penting jika domain frontend beda dengan backend)
window.axios.defaults.withCredentials = true;