<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ListPageLayout, ListState, ActionButton } from '$lib/components/layout';
	import { Home, MapPin, Plus, Users } from 'lucide-svelte';

	interface Farm {
		id: number;
		name: string;
		location: string | null;
		role: string;
	}

	let farms = $state.raw<Farm[]>([]);
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

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadFarms();
		}
	});

	function navigateToNewFarm() {
		goto('/app/farms/new');
	}

	function navigateToEditFarm(id: number) {
		goto(`/app/farms/${id}/edit`);
	}

	function navigateToMembers(id: number) {
		goto(`/app/farms/${id}/members`);
	}

	const roleStyles: Record<string, string> = {
		owner: 'bg-primary-container/10 text-primary-container',
		manager: 'bg-secondary/10 text-secondary',
		worker: 'bg-surface-container text-on-surface-variant'
	};
</script>

<svelte:head>
	<title>My Farms – Deshi Fishery</title>
</svelte:head>

<ListPageLayout
	title="My Farms"
	description="Manage your fisheries and team members"
>
	{#snippet action()}
		<ActionButton onclick={navigateToNewFarm} data-testid="add-farm-button">
			<Plus size={20} aria-hidden="true" />
			Add Farm
		</ActionButton>
	{/snippet}

	<ListState
		{loading}
		{error}
		empty={!loading && farms.length === 0}
		emptyTitle="No farms yet"
		emptyDescription="Create your first farm to start tracking ponds, stock, sales, and expenses."
	>
		{#snippet emptyIcon()}
			<div
				class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant"
			>
				<Home size={28} aria-hidden="true" />
			</div>
		{/snippet}
		{#snippet emptyAction()}
			<ActionButton onclick={navigateToNewFarm}>
				<Plus size={20} aria-hidden="true" />
				Create Your First Farm
			</ActionButton>
		{/snippet}

		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each farms as farm (farm.id)}
				<div
					class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6 transition-shadow duration-200 hover:shadow-md"
				>
					<div class="mb-3 flex items-start justify-between">
						<div class="flex items-center gap-3">
							<div
								class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-container/10 text-primary-container"
							>
								<Home size={22} aria-hidden="true" />
							</div>
							<h2 class="text-lg font-semibold text-on-surface">{farm.name}</h2>
						</div>
						<span
							class="rounded-full px-2.5 py-1 text-xs font-medium capitalize {roleStyles[farm.role] || roleStyles.worker}"
						>
							{farm.role}
						</span>
					</div>

					{#if farm.location}
						<p class="mb-4 flex items-center gap-1.5 text-sm text-on-surface-variant">
							<MapPin size={14} aria-hidden="true" />
							{farm.location}
						</p>
					{:else}
						<p class="mb-4 flex items-center gap-1.5 text-sm text-on-surface-variant/60">
							<MapPin size={14} aria-hidden="true" />
							No location set
						</p>
					{/if}

					<div class="flex flex-col gap-2 sm:flex-row">
						<button
							onclick={() => navigateToEditFarm(farm.id)}
							class="inline-flex min-h-[44px] items-center justify-center gap-1.5 rounded-lg border border-outline-variant px-4 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container"
						>
							Edit
						</button>
						<button
							onclick={() => navigateToMembers(farm.id)}
							class="inline-flex min-h-[44px] items-center justify-center gap-1.5 rounded-lg border border-outline-variant px-4 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container"
						>
							<Users size={16} aria-hidden="true" />
							Members
						</button>
					</div>
				</div>
			{/each}
		</div>
	</ListState>
</ListPageLayout>
