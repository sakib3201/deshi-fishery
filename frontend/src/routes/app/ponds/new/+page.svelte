<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';

	let pondNumber = $state('');
	let size = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			const response = await api.post('/ponds', {
				pond_number: pondNumber.trim(),
				size: size ? parseFloat(size) : null
			});
			if (response.success) {
				goto('/app/ponds');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create pond';
		} finally {
			loading = false;
		}
	}

	function goBack() {
		goto('/app/ponds');
	}
</script>

<svelte:head>
	<title>New Pond — Deshi Fishery</title>
</svelte:head>

<div class="max-w-2xl mx-auto px-4 py-8">
	<button onclick={goBack} class="text-slate-600 hover:text-slate-800 mb-4">← Back to Ponds</button>

	<h1 class="text-2xl font-bold mb-6">Add New Pond</h1>

	<form onsubmit={handleSubmit} class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
		<div>
			<label for="pond_number" class="block text-sm font-medium text-slate-700 mb-1">Pond Number *</label>
			<input
				id="pond_number"
				type="text"
				bind:value={pondNumber}
				required
				class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
				placeholder="e.g., Pond 1"
			/>
		</div>

		<div>
			<label for="size" class="block text-sm font-medium text-slate-700 mb-1">Size (acres)</label>
			<input
				id="size"
				type="number"
				step="0.01"
				min="0"
				bind:value={size}
				class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
				placeholder="e.g., 0.5"
			/>
		</div>

		{#if error}
			<p class="text-red-600 text-sm" role="alert">{error}</p>
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
				disabled={loading || !pondNumber.trim()}
				class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
			>
				{loading ? 'Creating...' : 'Create Pond'}
			</button>
		</div>
	</form>
</div>
