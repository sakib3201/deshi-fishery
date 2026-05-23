<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';

	let pondId = $state(0);
	let pondNumber = $state('');
	let size = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

	async function loadPond() {
		try {
			const response = await api.get(`/ponds/${pondId}`);
			if (response.success) {
				pondNumber = response.data.pond_number;
				size = response.data.size ? String(response.data.size) : '';
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load pond';
		} finally {
			initialLoading = false;
		}
	}

	$effect(() => {
		const idParam = page.params.id;
		if (idParam) {
			pondId = parseInt(idParam, 10);
			loadPond();
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			const response = await api.patch(`/ponds/${pondId}`, {
				pond_number: pondNumber.trim(),
				size: size ? parseFloat(size) : null
			});
			if (response.success) {
				goto('/app/ponds');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to update pond';
		} finally {
			loading = false;
		}
	}

	async function handleDelete() {
		if (!confirm('Are you sure you want to delete this pond?')) {
			return;
		}

		try {
			await api.delete(`/ponds/${pondId}`);
			goto('/app/ponds');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete pond';
		}
	}

	function goBack() {
		goto('/app/ponds');
	}
</script>

<svelte:head>
	<title>Edit Pond — Deshi Fishery</title>
</svelte:head>

<div class="max-w-2xl mx-auto px-4 py-8">
	<button onclick={goBack} class="text-slate-600 hover:text-slate-800 mb-4">← Back to Ponds</button>

	<h1 class="text-2xl font-bold mb-6">Edit Pond</h1>

	{#if initialLoading}
		<p class="text-slate-600">Loading pond...</p>
	{:else}
		<form onsubmit={handleSubmit} class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
			<div>
				<label for="pond_number" class="block text-sm font-medium text-slate-700 mb-1">Pond Number *</label>
				<input
					id="pond_number"
					type="text"
					bind:value={pondNumber}
					required
					class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
					{loading ? 'Saving...' : 'Save Changes'}
				</button>
				<button
					type="button"
					onclick={handleDelete}
					class="ml-auto text-red-600 hover:text-red-800 px-4 py-2 border border-red-200 rounded-md hover:bg-red-50 transition-colors"
				>
					Delete Pond
				</button>
			</div>
		</form>
	{/if}
</div>
