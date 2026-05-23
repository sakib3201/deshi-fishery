<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { Droplets, Ruler, Plus, Loader2 } from 'lucide-svelte';

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

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadPonds();
		}
	});

	function navigateToNewPond() {
		goto('/app/ponds/new');
	}

	function navigateToEditPond(id: number) {
		goto(`/app/ponds/${id}/edit`);
	}

	async function handleDelete(id: number, pondNumber: string) {
		if (!confirm(`Delete "${pondNumber}"? All stock history for this pond will be lost. This cannot be undone.`)) {
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
	<title>Ponds – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-7xl px-4 sm:px-6 py-8 sm:py-10">
	<!-- Header -->
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
		<div>
			<h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Ponds</h1>
			<p class="text-on-surface-variant mt-1">Manage your farm ponds and track stock</p>
		</div>
		<button
			onclick={navigateToNewPond}
			class="inline-flex items-center justify-center gap-2 h-14 px-6 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] min-h-[56px]"
		>
			<Plus size={20} aria-hidden="true" />
			Add Pond
		</button>
	</div>

	<!-- Error -->
	{#if error}
		<div class="mb-6 rounded-xl bg-error-container border border-error/20 p-4" role="alert" aria-live="polite">
			<p class="text-error font-medium">{error}</p>
		</div>
	{/if}

	<!-- Loading -->
	{#if loading}
		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each [1, 2, 3] as _}
				<div class="bg-surface-bright border border-outline-variant/20 rounded-xl p-6 animate-pulse">
					<div class="flex items-center gap-3 mb-4">
						<div class="h-11 w-11 rounded-xl bg-surface-container"></div>
						<div class="h-5 bg-surface-container rounded w-3/4"></div>
					</div>
					<div class="h-4 bg-surface-container rounded w-1/2 mb-4"></div>
					<div class="flex gap-2">
						<div class="h-10 bg-surface-container rounded w-16"></div>
						<div class="h-10 bg-surface-container rounded w-16"></div>
					</div>
				</div>
			{/each}
		</div>
	{:else if ponds.length === 0}
		<!-- Empty State -->
		<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-10 text-center">
			<div class="flex justify-center mb-4">
				<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant">
					<Droplets size={28} aria-hidden="true" />
				</div>
			</div>
			<h2 class="text-xl font-bold text-on-surface mb-2">No ponds yet</h2>
			<p class="text-on-surface-variant mb-6 max-w-md mx-auto">Add your first pond to start tracking stock, feed, and harvest data.</p>
			<button
				onclick={navigateToNewPond}
				class="inline-flex items-center justify-center gap-2 h-14 px-8 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] min-h-[56px]"
			>
				<Plus size={20} aria-hidden="true" />
				Add Your First Pond
			</button>
		</div>
	{:else}
		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each ponds as pond (pond.id)}
				<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-6 hover:shadow-md transition-shadow duration-200">
					<div class="flex items-start justify-between mb-3">
						<div class="flex items-center gap-3">
							<div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-sky text-accent-sky-text shrink-0">
								<Droplets size={22} aria-hidden="true" />
							</div>
							<h2 class="text-lg font-semibold text-on-surface">{pond.pond_number}</h2>
						</div>
					</div>

					{#if pond.size}
						<p class="text-sm text-on-surface-variant mb-4 flex items-center gap-1.5">
							<Ruler size={14} aria-hidden="true" />
							{pond.size} acres
						</p>
					{:else}
						<p class="text-sm text-on-surface-variant/60 mb-4 flex items-center gap-1.5">
							<Ruler size={14} aria-hidden="true" />
							No size specified
						</p>
					{/if}

					<div class="flex flex-col sm:flex-row gap-2">
						<button
							onclick={() => navigateToEditPond(pond.id)}
							class="inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg border border-outline-variant text-sm font-medium text-on-surface hover:bg-surface-container transition-colors min-h-[44px]"
						>
							Edit
						</button>
						<button
							onclick={() => handleDelete(pond.id, pond.pond_number)}
							class="inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg border border-error/30 text-sm font-medium text-error hover:bg-error-container transition-colors min-h-[44px]"
						>
							Delete
						</button>
					</div>
				</div>
			{/each}
		</div>
	{/if}
</div>
