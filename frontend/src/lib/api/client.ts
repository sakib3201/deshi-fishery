const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

let currentToken: string | null = null;

export function setToken(token: string | null) {
	currentToken = token;
}

async function fetchApi(endpoint: string, options: RequestInit = {}) {
	const url = `${API_BASE_URL}${endpoint}`;
	const headers = new Headers(options.headers);

	if (!headers.has('Content-Type') && options.body) {
		headers.set('Content-Type', 'application/json');
	}

	if (currentToken) {
		headers.set('Authorization', `Bearer ${currentToken}`);
	}

	const farmId = localStorage.getItem('current_farm_id');
	if (farmId) {
		headers.set('X-Farm-ID', farmId);
	}

	const response = await fetch(url, { ...options, headers });

	if (!response.ok) {
		const error = await response.json().catch(() => ({ error: { message: 'Unknown error' } }));
		throw new Error(error.error?.message || `HTTP ${response.status}`);
	}

	return response.json();
}

export const api = {
	setToken,
	get: (endpoint: string) => fetchApi(endpoint, { method: 'GET' }),
	post: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'POST', body: JSON.stringify(body) }),
	put: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'PUT', body: JSON.stringify(body) }),
	patch: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'PATCH', body: JSON.stringify(body) }),
	delete: (endpoint: string) => fetchApi(endpoint, { method: 'DELETE' })
};
