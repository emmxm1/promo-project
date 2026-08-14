<script setup>
import { ref } from 'vue';
import axios from '../bootstrap';
import PromoHistory from './PromoHistory.vue';

const props = defineProps({
    token: {
        type: String,
        default: null,
    },
});

const code = ref('');
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const historyRef = ref(null);

function getAuthHeaders() {
    const token = props.token ?? localStorage.getItem('auth_token');

    return token ? { Authorization: `Bearer ${token}` } : {};
}

async function submit() {
    loading.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        const { data } = await axios.post(
            '/api/promo/claim',
            { code: code.value },
            { headers: getAuthHeaders() },
        );

        successMessage.value = `Бонус ${data.bonus_amount} нараховано. Ваш баланс: ${data.balance}`;
        code.value = '';
        historyRef.value?.fetchHistory();
    } catch (error) {
        errorMessage.value = error.response?.data?.message ?? 'Сталася помилка. Спробуйте ще раз.';
        historyRef.value?.fetchHistory();
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="space-y-6">
        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label for="promo-code" class="block text-sm font-medium mb-1">
                    Промокод
                </label>
                <input
                    id="promo-code"
                    v-model="code"
                    type="text"
                    maxlength="12"
                    autocomplete="off"
                    placeholder="Введіть промокод"
                    :disabled="loading"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring disabled:bg-gray-100 disabled:cursor-not-allowed"
                />
            </div>

            <button
                type="submit"
                :disabled="loading || !code.trim()"
                class="inline-flex items-center gap-2 rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span
                    v-if="loading"
                    class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                />
                <span>{{ loading ? 'Завантаження...' : 'Застосувати' }}</span>
            </button>
        </form>

        <p
            v-if="successMessage"
            class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            role="status"
        >
            {{ successMessage }}
        </p>

        <p
            v-if="errorMessage"
            class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
            role="alert"
        >
            {{ errorMessage }}
        </p>

        <PromoHistory ref="historyRef" :token="token" />
    </div>
</template>
