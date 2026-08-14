<script setup>
import { onMounted, ref, watch } from 'vue';
import axios from '../bootstrap';

const props = defineProps({
    token: {
        type: String,
        default: null,
    },
});

const items = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const statusFilter = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

function getAuthHeaders() {
    const token = props.token ?? localStorage.getItem('auth_token');

    return token ? { Authorization: `Bearer ${token}` } : {};
}

function formatDate(value) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString('uk-UA');
}

function formatAmount(value) {
    return value !== null && value !== undefined ? value : '—';
}

function statusLabel(status) {
    return status === 'success' ? 'Успішно' : 'Відхилено';
}

function statusClass(status) {
    return status === 'success'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
}

async function fetchHistory(page = 1) {
    loading.value = true;
    errorMessage.value = '';

    try {
        const params = { page };

        if (statusFilter.value) {
            params.status = statusFilter.value;
        }

        const { data } = await axios.get('/api/promo/history', {
            params,
            headers: getAuthHeaders(),
        });

        items.value = data.data;
        currentPage.value = data.meta.current_page;
        lastPage.value = data.meta.last_page;
    } catch (error) {
        errorMessage.value = error.response?.data?.message ?? 'Не вдалося завантажити історію.';
        items.value = [];
    } finally {
        loading.value = false;
    }
}

function goToPage(page) {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }

    fetchHistory(page);
}

watch(statusFilter, () => {
    fetchHistory(1);
});

onMounted(() => {
    fetchHistory();
});

defineExpose({ fetchHistory });
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold">Історія промокодів</h2>

            <select
                v-model="statusFilter"
                :disabled="loading"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring disabled:bg-gray-100"
            >
                <option value="">Усі статуси</option>
                <option value="success">Успішно</option>
                <option value="rejected">Відхилено</option>
            </select>
        </div>

        <p v-if="loading" class="text-sm text-gray-500">Завантаження...</p>

        <p
            v-if="errorMessage"
            class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
            role="alert"
        >
            {{ errorMessage }}
        </p>

        <div v-if="!loading && !errorMessage && items.length === 0" class="text-sm text-gray-500">
            Історія порожня.
        </div>

        <div v-if="items.length > 0" class="overflow-hidden rounded-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Код</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Дата</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Сума</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Статус</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(item, index) in items" :key="`${item.code}-${item.applied_at}-${index}`">
                        <td class="px-4 py-3 font-medium">{{ item.code }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ formatDate(item.applied_at) }}</td>
                        <td class="px-4 py-3">{{ formatAmount(item.bonus_amount) }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="statusClass(item.status)"
                            >
                                {{ statusLabel(item.status) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="lastPage > 1"
            class="flex items-center justify-between gap-3 text-sm"
        >
            <button
                type="button"
                :disabled="loading || currentPage <= 1"
                class="rounded-md border border-gray-300 px-3 py-1.5 disabled:cursor-not-allowed disabled:opacity-50"
                @click="goToPage(currentPage - 1)"
            >
                Назад
            </button>

            <span class="text-gray-600">
                Сторінка {{ currentPage }} з {{ lastPage }}
            </span>

            <button
                type="button"
                :disabled="loading || currentPage >= lastPage"
                class="rounded-md border border-gray-300 px-3 py-1.5 disabled:cursor-not-allowed disabled:opacity-50"
                @click="goToPage(currentPage + 1)"
            >
                Далі
            </button>
        </div>
    </div>
</template>
