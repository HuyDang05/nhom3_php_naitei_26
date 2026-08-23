import apiClient from './client';
import { rememberCitizenSession } from './auth';

let cachedProfile = null;
let cachedAt = 0;
let inflightProfile = null;
const CACHE_TTL_MS = 30000;

export async function fetchCitizenProfile({ force = false } = {}) {
    if (!force && cachedProfile && Date.now() - cachedAt < CACHE_TTL_MS) {
        return { data: cachedProfile };
    }

    if (inflightProfile) {
        return inflightProfile;
    }

    inflightProfile = apiClient.get('/me').then(({ data }) => {
        cachedProfile = data.data;
        cachedAt = Date.now();
        rememberCitizenSession(cachedProfile);
        return data;
    }).finally(() => {
        inflightProfile = null;
    });

    return inflightProfile;
}

export function clearCitizenProfileCache() {
    cachedProfile = null;
    cachedAt = 0;
    inflightProfile = null;
}

export async function updateCitizenProfile(payload) {
    const { data } = await apiClient.patch('/me', payload);

    rememberCitizenSession(data.data);

    return data;
}
