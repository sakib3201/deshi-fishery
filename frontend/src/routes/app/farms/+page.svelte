<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';
	import { api } from '$lib/api/client';

	interface Farm {
		id: number;
		name: string;
		location: string | null;
		role: string;
	}

	let farms = $state<Farm[]>([]);
	let loading = $state(true);
	let error = $state('');

	async function loadFarms() {
		try {
			const response = await api.get('/farms');
			if (response.success) {
				farms = response.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load farms';
		} finally {
			loading = false;
		}
	}

	loadFarms();

	function navigateToNewFarm() {
		goto('/app/farms/new');
	}

	function navigateToEditFarm(id: number) {
		goto(`/app/farms/${id}/edit`);
	}

	function navigateToMembers(id: number) {
		goto(`/app/farms/${id}/members`);
	}
</script>

<svelte:head>
	<title>My Farms — Deshi Fishery</title>
</svelte:head>

<div class="max-w-4xl mx-auto px-4 py-8">
	<div class="flex justify-between items-center mb-6">
		<h1 class="text-2xl font-bold">My Farms</h1>
		<button
			onclick={navigateToNewFarm}
			class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
		>
			+ Add Farm
		</button>
	</div>

	{#if loading}
		<p class="text-slate-600">Loading farms...</p>
	{:else if error}
		<p class="text-red-600" role="alert">{error}</p>
	{:else if farms.length === 0}
		<div class="text-center py-12 bg-slate-50 rounded-lg">
			<p class="text-slate-600 mb-4">You don't have any farms yet.</p>
			<button
				onclick={navigateToNewFarm}
				class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
			>
				Create Your First Farm
			</button>
		</div>
	{:else}
		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each farms as farm (farm.id)}
				<div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
					<div class="flex justify-between items-start mb-2">
						<h2 class="text-lg font-semibold">{farm.name}</h2>
						<span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">
							{farm.role}
						</span>
					</div>
					{#if farm.location}
						<p class="text-slate-600 text-sm mb-4">{farm.location}</p>
					{/if}
					<div class="flex gap-2">
						<button
							onclick={() => navigateToEditFarm(farm.id)}
							class="text-sm text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-200 rounded-md hover:bg-blue-50 transition-colors"
						>
							Edit
						</button>
						<button
							onclick={() => navigateToMembers(farm.id)}
							class="text-sm text-slate-600 hover:text-slate-800 px-3 py-1 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors"
						>
							Members
						</button>
					</div>
				</div>
			{/each}
		</div>
	{/if}
</div>
