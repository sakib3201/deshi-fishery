import { auth, type Farm } from '$lib/api/auth';
import { api } from '$lib/api/client';

const TOKEN_KEY = 'df_access_token';

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
	'The password field confirmation does not match': 'The two passwords do not match. Please type them again.',
	'Invalid credentials': 'Email or password is incorrect. Please try again.',
	'Network error. Please check your connection.': 'Could not connect to the server. Please check your internet.',
	'Login failed': 'Unable to sign in. Please check your email and password.',
	'Registration failed': 'Unable to create account. Please try again.',
};

function getUserFriendlyError(err: unknown): string {
	const msg = err instanceof Error ? err.message : String(err);
	return ERROR_MESSAGES[msg] || msg || 'Something went wrong. Please try again.';
}

function createAuthStore() {
	// Initialize from localStorage (SSR-safe)
	let accessToken = $state<string | null>(
		typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null
	);

	// Sync API client on init (using untracked initial value)
	const initialToken = typeof window !== 'undefined' ? localStorage.getItem(TOKEN_KEY) : null;
	if (initialToken) {
		api.setToken(initialToken);
	}

	let user = $state<User | null>(null);
	let loading = $state(false);
	let error = $state<string | null>(null);
	let requiresOnboarding = $state(false);

	const isAuthenticated = $derived(!!accessToken && !!user);
	const currentFarmId = $derived(user?.current_farm_id ?? null);
	const farms = $derived(user?.farms ?? []);

	function setToken(token: string) {
		accessToken = token;
		if (typeof window !== 'undefined') {
			localStorage.setItem(TOKEN_KEY, token);
		}
		api.setToken(token);
	}

	function setUserFromResponse(data: { user: User; access_token?: string; requires_onboarding?: boolean }) {
		user = data.user;
		requiresOnboarding = data.requires_onboarding ?? false;
		if (data.user.current_farm_id) {
			api.setFarmId(String(data.user.current_farm_id));
		}
		if (data.access_token) {
			setToken(data.access_token);
		}
	}

	async function login(email: string, password: string) {
		if (!navigator.onLine) {
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

	async function register(name: string, email: string, password: string, passwordConfirmation: string) {
		if (!navigator.onLine) {
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
		if (typeof window !== 'undefined') {
			localStorage.removeItem(TOKEN_KEY);
		}
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

	async function init() {
		try {
			const response = await auth.me();
			if (response.success) {
				user = response.data;
				if (user.current_farm_id) {
					api.setFarmId(String(user.current_farm_id));
				}
			}
		} catch {
			// Token invalid, clear it
			clearAuth();
		}
	}

	return {
		get user() { return user; },
		get accessToken() { return accessToken; },
		get loading() { return loading; },
		get error() { return error; },
		get isAuthenticated() { return isAuthenticated; },
		get requiresOnboarding() { return requiresOnboarding; },
		get currentFarmId() { return currentFarmId; },
		get farms() { return farms; },
		login,
		register,
		logout,
		switchFarm,
		init,
		clearAuth
	};
}

export const authStore = createAuthStore();
