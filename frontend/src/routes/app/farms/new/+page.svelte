<script lang="ts">
	import { goto } from '$app/navigation';
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
				goto('/app/farms');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create farm';
		} finally {
			loading = false;
		}
	}

	function goBack() {
		goto('/app/farms');
	}
</script>

<svelte:head>
	<title>New Farm — Deshi Fishery</title>
</svelte:head>

<div class="max-w-2xl mx-auto px-4 py-8">
	<button onclick={goBack} class="text-slate-600 hover:text-slate-800 mb-4">← Back to Farms</button>

	<h1 class="text-2xl font-bold mb-6">Create New Farm</h1>

	<form onsubmit={handleSubmit} class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
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

		<div class="flex gap-3 pt-2">
			<button
				type="button"
				onclick={goBack}
				class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-50 transition-colors"
			>
				Cancel
			</button>
			<button
				type="submit"
				disabled={loading || !name}
				class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
			>
				{loading ? 'Creating...' : 'Create Farm'}
			</button>
		</div>
	</form>
</div>
