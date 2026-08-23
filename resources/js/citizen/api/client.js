import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    withXSRFToken: true,
});

let csrfInflight = null;
let csrfLastFetchedAt = 0;
const CSRF_TTL_MS = 10 * 60 * 1000;

export async function initializeCsrfProtection() {
    if (csrfLastFetchedAt && Date.now() - csrfLastFetchedAt < CSRF_TTL_MS) {
        return;
    }

    if (csrfInflight) {
        await csrfInflight;
        return;
    }

    csrfInflight = axios.get('/sanctum/csrf-cookie', {
        withCredentials: true,
        withXSRFToken: true,
    }).then(() => {
        csrfLastFetchedAt = Date.now();
    }).finally(() => {
        csrfInflight = null;
    });

    await csrfInflight;
}

export default apiClient;
