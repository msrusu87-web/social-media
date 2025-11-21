import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';

const app = createApp({});
const pinia = createPinia();

app.use(pinia);
app.use(router);

app.mount('#app');
