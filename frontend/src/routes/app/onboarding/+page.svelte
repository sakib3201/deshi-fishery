<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';
	import { api } from '$lib/api/client';

	let name = $state('');
	let location = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: Event) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			const response = await api.post('/farms', { name, location });
			if (response.success) {
				// Update user data after farm creation
				await authStore.init();
				goto('/app/dashboard');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create farm';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Create Your Farm — Deshi Fishery</title>
</svelte:head>

<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
	<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
		<h1 class="text-2xl font-bold text-center mb-2">Welcome to Deshi Fishery</h1>
		<p class="text-slate-600 text-center mb-6">Create your first farm to get started</p>

		<form onsubmit={handleSubmit} class="space-y-4">
			<div>
				<label for="name" class="block text-sm font-medium text-slate-700 mb-1">Farm Name *</label>
				<input
					id="name"
					type="text"
					bind:value={name}
					required
					class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
					placeholder="e.g., My Fish Farm"
				/>
			</div>

			<div>
				<label for="location" class="block text-sm font-medium text-slate-700 mb-1">Location</label>
				<input
					id="location"
					type="text"
					bind:value={location}
					class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
					placeholder="e.g., Rajshahi"
				/>
			</div>

			{#if error}
				<p class="text-red-600 text-sm">{error}</p>
			{/if}

			<button
				type="submit"
				disabled={loading || !name}
				class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
			>
				{loading ? 'Creating...' : 'Create Farm'}
			</button>
		</form>
	</div>
</div>
