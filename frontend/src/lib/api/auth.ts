import { api } from './client';

export interface LoginCredentials {
	email: string;
	password: string;
}

export interface RegisterData extends LoginCredentials {
	name: string;
	password_confirmation: string;
}

export interface Farm {
	id: number;
	name: string;
	location: string | null;
	role: string;
}

export interface AuthResponse {
	success: boolean;
	data: {
		access_token: string;
		refresh_token: string;
		user: {
			id: number;
			name: string;
			email: string;
			role: string;
			current_farm_id: number | null;
		};
		requires_onboarding: boolean;
	};
}

export interface MeResponse {
	success: boolean;
	data: {
		id: number;
		name: string;
		email: string;
		role: string;
		current_farm_id: number | null;
		farms: Farm[];
	};
}

export interface SwitchFarmResponse {
	success: boolean;
	data: {
		access_token: string;
		refresh_token: string;
		user: {
			id: number;
			name: string;
			email: string;
			role: string;
			current_farm_id: number | null;
		};
	};
}

export const auth = {
	login: (credentials: LoginCredentials) =>
		api.post('/auth/login', credentials) as Promise<AuthResponse>,
	register: (data: RegisterData) =>
		api.post('/auth/register', data) as Promise<AuthResponse>,
	logout: () => api.post('/auth/logout', {}),
	me: () => api.get('/auth/me') as Promise<MeResponse>,
	refresh: () => api.post('/auth/refresh', {}),
	switchFarm: (farmId: number) =>
		api.patch('/users/current-farm', { farm_id: farmId }) as Promise<SwitchFarmResponse>
};
