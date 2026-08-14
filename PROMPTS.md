 Тікет 1: Промокоди

Промт 1 — Міграції та моделі
Запит: "Створи в Laravel проєкті міграції та моделі для системи промокодів: ..."
Результат: створено міграції promo_codes, promo_code_usages, моделі PromoCode, 
PromoCodeUsage, enum PromoCodeUsageStatus, зв'язки між моделями. Без правок.

Промт 2 — Контролер claim
Запит: "Створи в Laravel API endpoint POST /api/promo/claim: ..."
Результат: створено PromoClaimController, ClaimPromoCodeRequest, маршрут з 
auth:sanctum, встановлено Laravel Sanctum, бізнес-логіка в DB::transaction з 
lockForUpdate() для захисту від race condition. Автотести пройшли. Без правок.

 Промт 3 — Ендпоінт історії
Запит: "Створи в Laravel API endpoint GET /api/promo/history: ..."
Результат: створено PromoHistoryController, пагінація, фільтр за статусом. 
Без правок.

Промт 4 — Фронтенд компоненти
Запит: "Створи Vue-компонент PromoClaim.vue для форми введення промокоду: ..."
Результат: створено PromoClaim.vue та PromoHistory.vue.
Правка: білд (npm run build) видавав помилки неправильних шляхів імпорту 
(from '/bootstrap' та './components/PromoHistory.vue'). Виправлено вручну: 
змінено на '../bootstrap' та './PromoHistory.vue'.