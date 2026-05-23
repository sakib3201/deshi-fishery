<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';

	let farmId = $state(0);
	let name = $state('');
	let location = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

	async function loadFarm() {
		try {
			const response = await api.get(`/farms/${farmId}`);
			if (response.success) {
				name = response.data.name;
				location = response.data.location || '';
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load farm';
		} finally {
			initialLoading = false;
		}
	}

	$effect(() => {
		const idParam = page.params.id;
		if (idParam) {
			farmId = parseInt(idParam, 10);
			loadFarm();
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			const response = await api.patch(`/farms/${farmId}`, { name, location });
			if (response.success) {
				goto('/app/farms');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to update farm';
		} finally {
			loading = false;
		}
	}

	async function handleDelete() {
		if (!confirm('Are you sure you want to delete this farm? This action cannot be undone.')) {
			return;
		}

		try {
			await api.delete(`/farms/${farmId}`);
			goto('/app/farms');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete farm';
		}
	}

	function goBack() {
		goto('/app/farms');
	}
</script>

<svelte:head>
	<title>Edit Farm — Deshi Fishery</title>
</svelte:head>

<div class="max-w-2xl mx-auto px-4 py-8">
	<button onclick={goBack} class="text-slate-600 hover:text-slate-800 mb-4">← Back to Farms</button>

	<h1 class="text-2xl font-bold mb-6">Edit Farm</h1>

	{#if initialLoading}
		<p class="text-slate-600">Loading farm...</p>
	{:else}
		<form onsubmit={handleSubmit} class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
			<div>
				<label for="name" class="block text-sm font-medium text-slate-700 mb-1">Farm Name *</label>
				<input
					id="name"
					type="text"
					bind:value={name}
					required
					class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
				/>
			</div>

			<div>
				<label for="location" class="block text-sm font-medium text-slate-700 mb-1">Location</label>
				<input
					id="location"
					type="text"
					bind:value={location}
					class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
					disabled={loading || !name.trim()}
					class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
				>
					{loading ? 'Saving...' : 'Save Changes'}
				</button>
				<button
					type="button"
					onclick={handleDelete}
					class="ml-auto text-red-600 hover:text-red-800 px-4 py-2 border border-red-200 rounded-md hover:bg-red-50 transition-colors"
				>
					Delete Farm
				</button>
			</div>
		</form>
	{/if}
</div>
