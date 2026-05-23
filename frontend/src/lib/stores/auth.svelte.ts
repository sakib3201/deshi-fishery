import { browser } from '$app/environment';
import { auth, type Farm } from '$lib/api/auth';
import { api } from '$lib/api/client';

const TOKEN_KEY = 'df_access_token';
const FARM_ID_KEY = 'df_current_farm_id';

interface User {
	id: number;
	name: string;
	email: string;
	role: string;
	current_farm_id: number | null;
	farms?: Farm[];
}

const ERROR_MESSAGES: Record<string, string> = {
	'The email has already been taken': 'This email is already registered. Please sign in instead.',
	'The password field confirmation does not match':
		'The two passwords do not match. Please type them again.',
	'Invalid credentials': 'Email or password is incorrect. Please try again.',
	'Network error. Please check your connection.':
		'Could not connect to the server. Please check your internet.',
	'Login failed': 'Unable to sign in. Please check your email and password.',
	'Registration failed': 'Unable to create account. Please try again.'
};

function getUserFriendlyError(err: unknown): string {
	const msg = err instanceof Error ? err.message : String(err);
	return ERROR_MESSAGES[msg] || msg || 'Something went wrong. Please try again.';
}

function readStorage(key: string): string | null {
	return browser ? localStorage.getItem(key) : null;
}

function writeStorage(key: string, value: string | null) {
	if (!browser) return;
	if (value === null) {
		localStorage.removeItem(key);
	} else {
		localStorage.setItem(key, value);
	}
}

function createAuthStore() {
	// Read token/farm once to avoid multiple localStorage hits
	const storedToken = readStorage(TOKEN_KEY);
	const storedFarmId = readStorage(FARM_ID_KEY);

	// Sync API client eagerly so requests work before init() completes
	if (storedToken) api.setToken(storedToken);
	if (storedFarmId) api.setFarmId(storedFarmId);

	let accessToken = $state<string | null>(storedToken);
	let user = $state.raw<User | null>(null);
	let loading = $state(false);
	let error = $state<string | null>(null);
	let requiresOnboarding = $state(false);

	const isAuthenticated = $derived(!!accessToken && !!user);
	const currentFarmId = $derived(user?.current_farm_id ?? (storedFarmId ? parseInt(storedFarmId, 10) : null));
	const farms = $derived(user?.farms ?? []);

	function setToken(token: string) {
		accessToken = token;
		writeStorage(TOKEN_KEY, token);
		api.setToken(token);
	}

	function setFarmId(farmId: number | null) {
		writeStorage(FARM_ID_KEY, farmId ? String(farmId) : null);
		api.setFarmId(farmId ? String(farmId) : null);
	}

	function setUserFromResponse(data: {
		user: User;
		access_token?: string;
		requires_onboarding?: boolean;
	}) {
		user = data.user;
		requiresOnboarding = data.requires_onboarding ?? false;
		if (data.user.current_farm_id) {
			setFarmId(data.user.current_farm_id);
		}
		if (data.access_token) {
			setToken(data.access_token);
		}
	}

	async function login(email: string, password: string) {
		if (browser && !navigator.onLine) {
			error = 'No internet connection. Please check your network and try again.';
			throw new Error('Offline');
		}
		loading = true;
		error = null;
		try {
			const response = await auth.login({ email, password });
			if (response.success) {
				setUserFromResponse(response.data);
			}
		} catch (err) {
			error = getUserFriendlyError(err);
			throw err;
		} finally {
			loading = false;
		}
	}

	async function register(
		name: string,
		email: string,
		password: string,
		passwordConfirmation: string
	) {
		if (browser && !navigator.onLine) {
			error = 'No internet connection. Please check your network and try again.';
			throw new Error('Offline');
		}
		loading = true;
		error = null;
		try {
			const response = await auth.register({
				name,
				email,
				password,
				password_confirmation: passwordConfirmation
			});
			if (response.success) {
				setUserFromResponse(response.data);
			}
		} catch (err) {
			error = getUserFriendlyError(err);
			throw err;
		} finally {
			loading = false;
		}
	}

	function clearAuth() {
		accessToken = null;
		user = null;
		requiresOnboarding = false;
		writeStorage(TOKEN_KEY, null);
		writeStorage(FARM_ID_KEY, null);
		api.setToken(null);
		api.setFarmId(null);
	}

	async function logout() {
		try {
			await auth.logout();
		} catch {
			// Ignore logout errors
		} finally {
			clearAuth();
		}
	}

	async function switchFarm(farmId: number) {
		loading = true;
		error = null;
		try {
			const response = await auth.switchFarm(farmId);
			if (response.success) {
				setUserFromResponse(response.data);
			}
		} catch (err) {
			error = getUserFriendlyError(err);
			throw err;
		} finally {
			loading = false;
		}
	}

	function syncFarmId() {
		const farmId = currentFarmId;
		if (farmId) {
			api.setFarmId(String(farmId));
		}
	}

	async function init() {
		try {
			const response = await auth.me();
			if (response.success) {
				user = response.data;
				if (user.current_farm_id) {
					setFarmId(user.current_farm_id);
				}
			}
		} catch {
			// Token invalid, clear it
			clearAuth();
		}
	}

	return {
		get user() {
			return user;
		},
		get accessToken() {
			return accessToken;
		},
		get loading() {
			return loading;
		},
		get error() {
			return error;
		},
		set error(value: string | null) {
			error = value;
		},
		get isAuthenticated() {
			return isAuthenticated;
		},
		get requiresOnboarding() {
			return requiresOnboarding;
		},
		get currentFarmId() {
			return currentFarmId;
		},
		get farms() {
			return farms;
		},
		login,
		register,
		logout,
		switchFarm,
		init,
		clearAuth,
		syncFarmId
	};
}

export const authStore = createAuthStore();
