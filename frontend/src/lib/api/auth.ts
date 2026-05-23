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

export interface Pond {
	id: number;
	farm_id: number;
	pond_number: string;
	size: number;
	current_stock_quantity: number;
	current_stock_weight_kg: number;
	created_at: string;
}

export interface StockRelease {
	id: number;
	farm_id: number;
	pond_id: number;
	species: string;
	quantity: number;
	avg_weight_gram: number;
	cost_bdt: number;
	release_date: string;
	notes: string | null;
	created_by: number;
	created_at: string;
	updated_at: string;
	pond?: Pond;
}

export interface Sale {
	id: number;
	farm_id: number;
	pond_id: number;
	sale_code: string;
	sale_type: 'wholesale' | 'retail';
	date: string;
	fish_type: string;
	avg_fish_weight_g: number;
	quantity_kg: number;
	rate_per_kg: number;
	total_amount: number;
	customer_name: string | null;
	custom_tags: string[] | null;
	payment_status: 'pending' | 'partial' | 'paid';
	amount_paid: number;
	amount_due: number;
	notes: string | null;
	created_by: number;
	created_at: string;
	updated_at: string;
	pond?: Pond;
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
	register: (data: RegisterData) => api.post('/auth/register', data) as Promise<AuthResponse>,
	logout: () => api.post('/auth/logout', {}),
	me: () => api.get('/auth/me') as Promise<MeResponse>,
	refresh: () => api.post('/auth/refresh', {}),
	switchFarm: (farmId: number) =>
		api.patch('/users/current-farm', { farm_id: farmId }) as Promise<SwitchFarmResponse>
};
