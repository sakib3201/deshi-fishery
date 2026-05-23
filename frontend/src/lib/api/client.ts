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

	const farmId = api.getFarmId();
	if (farmId) {
		headers.set('X-Farm-ID', farmId);
	}

	const controller = new AbortController();
	const timeoutId = setTimeout(() => controller.abort(), 10000);

	try {
		const response = await fetch(url, { ...options, headers, signal: controller.signal });

		if (!response.ok) {
			const error = await response.json().catch(() => ({ error: { message: 'Unknown error' } }));
			throw new Error(error.error?.message || `HTTP ${response.status}`);
		}

		// Handle 204 No Content — don't try to parse JSON body
		if (response.status === 204) {
			return { success: true };
		}

		return response.json();
	} catch (err) {
		if (err instanceof TypeError) {
			throw new Error('Network error. Please check your connection.');
		}
		if ((err as Error).name === 'AbortError') {
			throw new Error('Request timed out. Please try again.');
		}
		throw err;
	} finally {
		clearTimeout(timeoutId);
	}
}

let currentFarmId: string | null = null;

export function setFarmId(farmId: string | null) {
	currentFarmId = farmId;
}

export function getFarmId(): string | null {
	return currentFarmId;
}

export const api = {
	setToken,
	setFarmId,
	getFarmId,
	get: (endpoint: string) => fetchApi(endpoint, { method: 'GET' }),
	post: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'POST', body: JSON.stringify(body) }),
	put: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'PUT', body: JSON.stringify(body) }),
	patch: (endpoint: string, body: unknown) =>
		fetchApi(endpoint, { method: 'PATCH', body: JSON.stringify(body) }),
	delete: (endpoint: string) => fetchApi(endpoint, { method: 'DELETE' })
};

export const stockReleases = {
	list: (params?: { pond_id?: number; species?: string; cursor?: string; per_page?: number }) => {
		const searchParams = new URLSearchParams();
		if (params?.pond_id) searchParams.set('pond_id', String(params.pond_id));
		if (params?.species) searchParams.set('species', params.species);
		if (params?.cursor) searchParams.set('cursor', params.cursor);
		if (params?.per_page) searchParams.set('per_page', String(params.per_page));
		const query = searchParams.toString();
		return api.get(`/stock-releases${query ? `?${query}` : ''}`) as Promise<{
			success: boolean;
			data: {
				data: import('./auth').StockRelease[];
				next_cursor: string | null;
			};
		}>;
	},
	create: (
		data: Omit<
			import('./auth').StockRelease,
			'id' | 'created_at' | 'updated_at' | 'farm_id' | 'created_by'
		>
	) =>
		api.post('/stock-releases', data) as Promise<{
			success: boolean;
			data: import('./auth').StockRelease;
		}>,
	get: (id: number) =>
		api.get(`/stock-releases/${id}`) as Promise<{
			success: boolean;
			data: import('./auth').StockRelease;
		}>,
	update: (
		id: number,
		data: Partial<
			Omit<
				import('./auth').StockRelease,
				'id' | 'created_at' | 'updated_at' | 'farm_id' | 'created_by'
			>
		>
	) =>
		api.patch(`/stock-releases/${id}`, data) as Promise<{
			success: boolean;
			data: import('./auth').StockRelease;
		}>,
	delete: (id: number) => api.delete(`/stock-releases/${id}`) as Promise<{ success: boolean }>
};
