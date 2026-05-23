<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';
	import { api } from '$lib/api/client';
	import { Home, MapPin, Plus, Loader2, Users } from 'lucide-svelte';

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
		worker: 'bg-surface-container text-on-surface-variant',
	};
</script>

<svelte:head>
	<title>My Farms – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-7xl px-4 sm:px-6 py-8 sm:py-10">
	<!-- Header -->
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
		<div>
			<h1 class="text-2xl sm:text-3xl font-bold text-on-surface">My Farms</h1>
			<p class="text-on-surface-variant mt-1">Manage your fisheries and team members</p>
		</div>
		<button
			onclick={navigateToNewFarm}
			class="inline-flex items-center justify-center gap-2 h-14 px-6 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] min-h-[56px]"
			data-testid="add-farm-button"
		>
			<Plus size={20} aria-hidden="true" />
			Add Farm
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
						<div class="h-10 bg-surface-container rounded w-20"></div>
					</div>
				</div>
			{/each}
		</div>
	{:else if farms.length === 0}
		<!-- Empty State -->
		<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-10 text-center">
			<div class="flex justify-center mb-4">
				<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant">
					<Home size={28} aria-hidden="true" />
				</div>
			</div>
			<h2 class="text-xl font-bold text-on-surface mb-2">No farms yet</h2>
			<p class="text-on-surface-variant mb-6 max-w-md mx-auto">Create your first farm to start tracking ponds, stock, sales, and expenses.</p>
			<button
				onclick={navigateToNewFarm}
				class="inline-flex items-center justify-center gap-2 h-14 px-8 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] min-h-[56px]"
			>
				<Plus size={20} aria-hidden="true" />
				Create Your First Farm
			</button>
		</div>
	{:else}
		<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
			{#each farms as farm (farm.id)}
				<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-6 hover:shadow-md transition-shadow duration-200">
					<div class="flex items-start justify-between mb-3">
						<div class="flex items-center gap-3">
							<div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-container/10 text-primary-container shrink-0">
								<Home size={22} aria-hidden="true" />
							</div>
							<h2 class="text-lg font-semibold text-on-surface">{farm.name}</h2>
						</div>
						<span class="text-xs font-medium px-2.5 py-1 rounded-full capitalize {roleStyles[farm.role] || roleStyles.worker}">
							{farm.role}
						</span>
					</div>

					{#if farm.location}
						<p class="text-sm text-on-surface-variant mb-4 flex items-center gap-1.5">
							<MapPin size={14} aria-hidden="true" />
							{farm.location}
						</p>
					{:else}
						<p class="text-sm text-on-surface-variant/60 mb-4 flex items-center gap-1.5">
							<MapPin size={14} aria-hidden="true" />
							No location set
						</p>
					{/if}

					<div class="flex flex-col sm:flex-row gap-2">
						<button
							onclick={() => navigateToEditFarm(farm.id)}
							class="inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg border border-outline-variant text-sm font-medium text-on-surface hover:bg-surface-container transition-colors min-h-[44px]"
						>
							Edit
						</button>
						<button
							onclick={() => navigateToMembers(farm.id)}
							class="inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg border border-outline-variant text-sm font-medium text-on-surface hover:bg-surface-container transition-colors min-h-[44px]"
						>
							<Users size={16} aria-hidden="true" />
							Members
						</button>
					</div>
				</div>
			{/each}
		</div>
	{/if}
</div>
