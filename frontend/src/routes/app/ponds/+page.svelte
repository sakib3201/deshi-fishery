<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';

	interface Pond {
		id: number;
		pond_number: string;
		size: number | null;
	}

	let ponds = $state<Pond[]>([]);
	let loading = $state(true);
	let error = $state('');

	async function loadPonds() {
		try {
			const response = await api.get('/ponds');
			if (response.success) {
				ponds = response.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load ponds';
		} finally {
			loading = false;
		}
	}

	loadPonds();

	function navigateToNewPond() {
		goto('/app/ponds/new');
	}

	function navigateToEditPond(id: number) {
		goto(`/app/ponds/${id}/edit`);
	}

	async function handleDelete(id: number, pondNumber: string) {
		if (!confirm(`Are you sure you want to delete ${pondNumber}?`)) {
			return;
		}

		try {
			await api.delete(`/ponds/${id}`);
			ponds = ponds.filter((p) => p.id !== id);
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete pond';
		}
	}
</script>

<svelte:head>
	<title>Ponds — Deshi Fishery</title>
</svelte:head>

<div class="max-w-4xl mx-auto px-4 py-8">
	<div class="flex justify-between items-center mb-6">
		<h1 class="text-2xl font-bold">Ponds</h1>
		<button
			onclick={navigateToNewPond}
			class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
		>
			+ Add Pond
		</button>
	</div>

	{#if loading}
		<p class="text-slate-600">Loading ponds...</p>
	{:else if error}
		<p class="text-red-600" role="alert">{error}</p>
	{:else if ponds.length === 0}
		<div class="text-center py-12 bg-slate-50 rounded-lg">
			<p class="text-slate-600 mb-4">No ponds found for this farm.</p>
			<button
				onclick={navigateToNewPond}
				class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
			>
				Add Your First Pond
			</button>
		</div>
	{:else}
		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each ponds as pond (pond.id)}
				<div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
					<div class="flex justify-between items-start mb-2">
						<h2 class="text-lg font-semibold">{pond.pond_number}</h2>
					</div>
					{#if pond.size}
						<p class="text-slate-600 text-sm mb-4">{pond.size} acres</p>
					{:else}
						<p class="text-slate-400 text-sm mb-4">No size specified</p>
					{/if}
					<div class="flex gap-2">
						<button
							onclick={() => navigateToEditPond(pond.id)}
							class="text-sm text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-200 rounded-md hover:bg-blue-50 transition-colors"
						>
							Edit
						</button>
						<button
							onclick={() => handleDelete(pond.id, pond.pond_number)}
							class="text-sm text-red-600 hover:text-red-800 px-3 py-1 border border-red-200 rounded-md hover:bg-red-50 transition-colors"
						>
							Delete
						</button>
					</div>
				</div>
			{/each}
		</div>
	{/if}
</div>
