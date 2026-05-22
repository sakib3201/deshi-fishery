import { auth, type Farm } from '$lib/api/auth';
import { api } from '$lib/api/client';

interface User {
	id: number;
	name: string;
	email: string;
	role: string;
	current_farm_id: number | null;
	farms?: Farm[];
}

function createAuthStore() {
	let user = $state<User | null>(null);
	let accessToken = $state<string | null>(null);
	let loading = $state(false);
	let error = $state<string | null>(null);
	let requiresOnboarding = $state(false);

	const isAuthenticated = $derived(!!accessToken && !!user);
	const currentFarmId = $derived(user?.current_farm_id ?? null);
	const farms = $derived(user?.farms ?? []);

	function setToken(token: string) {
		accessToken = token;
		localStorage.setItem('access_token', token);
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
		loading = true;
		error = null;
		try {
			const response = await auth.login({ email, password });
			if (response.success) {
				setUserFromResponse(response.data);
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Login failed';
			throw err;
		} finally {
			loading = false;
		}
	}

	async function register(name: string, email: string, password: string, passwordConfirmation: string) {
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
			error = err instanceof Error ? err.message : 'Registration failed';
			throw err;
		} finally {
			loading = false;
		}
	}

	function clearAuth() {
		accessToken = null;
		user = null;
		requiresOnboarding = false;
		localStorage.removeItem('access_token');
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
			error = err instanceof Error ? err.message : 'Failed to switch farm';
			throw err;
		} finally {
			loading = false;
		}
	}

	async function init() {
		const token = localStorage.getItem('access_token');
		if (token) {
			accessToken = token;
			api.setToken(token);
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
