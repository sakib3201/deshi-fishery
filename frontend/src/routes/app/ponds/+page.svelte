<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ListPageLayout, ListState, ActionButton } from '$lib/components/layout';
	import { Droplets, Ruler, Plus } from 'lucide-svelte';

	interface Pond {
		id: number;
		pond_number: string;
		size: number | null;
	}

	let ponds = $state.raw<Pond[]>([]);
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

<ListPageLayout
	title="Ponds"
	description="Manage your farm ponds and track stock"
>
	{#snippet action()}
		<ActionButton onclick={navigateToNewPond}>
			<Plus size={20} aria-hidden="true" />
			Add Pond
		</ActionButton>
	{/snippet}

	<ListState
		{loading}
		{error}
		empty={!loading && ponds.length === 0}
		emptyTitle="No ponds yet"
		emptyDescription="Add your first pond to start tracking stock, feed, and harvest data."
	>
		{#snippet emptyIcon()}
			<div
				class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant"
			>
				<Droplets size={28} aria-hidden="true" />
			</div>
		{/snippet}
		{#snippet emptyAction()}
			<ActionButton onclick={navigateToNewPond}>
				<Plus size={20} aria-hidden="true" />
				Add Your First Pond
			</ActionButton>
		{/snippet}

		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each ponds as pond (pond.id)}
				<div
					class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6 transition-shadow duration-200 hover:shadow-md"
				>
					<div class="mb-3 flex items-start justify-between">
						<div class="flex items-center gap-3">
							<div
								class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent-sky text-accent-sky-text"
							>
								<Droplets size={22} aria-hidden="true" />
							</div>
							<h2 class="text-lg font-semibold text-on-surface">{pond.pond_number}</h2>
						</div>
					</div>

					{#if pond.size}
						<p class="mb-4 flex items-center gap-1.5 text-sm text-on-surface-variant">
							<Ruler size={14} aria-hidden="true" />
							{pond.size} acres
						</p>
					{:else}
						<p class="mb-4 flex items-center gap-1.5 text-sm text-on-surface-variant/60">
							<Ruler size={14} aria-hidden="true" />
							No size specified
						</p>
					{/if}

					<div class="flex flex-col gap-2 sm:flex-row">
						<button
							onclick={() => navigateToEditPond(pond.id)}
							data-testid="pond-edit-{pond.id}"
							class="inline-flex min-h-[44px] items-center justify-center gap-1.5 rounded-lg border border-outline-variant px-4 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container"
						>
							Edit
						</button>
						<button
							onclick={() => handleDelete(pond.id, pond.pond_number)}
							data-testid="pond-delete-{pond.id}"
							class="inline-flex min-h-[44px] items-center justify-center gap-1.5 rounded-lg border border-error/30 px-4 text-sm font-medium text-error transition-colors hover:bg-error-container"
						>
							Delete
						</button>
					</div>
				</div>
			{/each}
		</div>
	</ListState>
</ListPageLayout>
