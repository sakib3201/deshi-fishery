import { auth } from '$lib/api/auth';
import { api } from '$lib/api/client';

interface User {
	id: number;
	name: string;
	email: string;
	role: string;
	current_farm_id: number | null;
}

function createAuthStore() {
	let user = $state<User | null>(null);
	let accessToken = $state<string | null>(null);
	let loading = $state(false);
	let error = $state<string | null>(null);

	const isAuthenticated = $derived(!!accessToken && !!user);

	async function login(email: string, password: string) {
		loading = true;
		error = null;
		try {
			const response = await auth.login({ email, password });
			if (response.success) {
				accessToken = response.data.access_token;
				user = response.data.user;
				localStorage.setItem('access_token', accessToken);
				api.setToken(accessToken);
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
				accessToken = response.data.access_token;
				user = response.data.user;
				localStorage.setItem('access_token', accessToken);
				api.setToken(accessToken);
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
		localStorage.removeItem('access_token');
		api.setToken(null);
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

	async function init() {
		const token = localStorage.getItem('access_token');
		if (token) {
			accessToken = token;
			api.setToken(token);
			try {
				const response = await auth.me();
				if (response.success) {
					user = response.data;
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
		login,
		register,
		logout,
		init,
		clearAuth
	};
}

export const authStore = createAuthStore();
