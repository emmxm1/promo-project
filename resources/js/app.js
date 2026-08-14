import './bootstrap';
import { createApp } from 'vue';
import PromoClaim from './components/PromoClaim.vue';

const appElement = document.getElementById('app');

if (appElement) {
    createApp(PromoClaim, {
        token: appElement.dataset.token || null,
    }).mount(appElement);
}
