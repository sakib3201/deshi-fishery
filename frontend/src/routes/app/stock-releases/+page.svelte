<script lang="ts">
	import { goto } from '$app/navigation';
	import { stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import { PackagePlus, Calendar, Fish, ArrowRight, Loader2 } from 'lucide-svelte';

	interface Release {
		id: number;
		pond_id: number;
		species: string;
		quantity: number;
		avg_weight_gram: number;
		cost_bdt: number;
		release_date: string;
		pond?: { pond_number: string };
	}

	let releases = $state<Release[]>([]);
	let loading = $state(true);
	let error = $state('');

	async function loadReleases() {
		try {
			const response = await stockReleases.list();
			if (response.success) {
				releases = response.data.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load stock releases';
		} finally {
			loading = false;
		}
	}

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadReleases();
		}
	});

	function navigateToNew() {
		goto('/app/stock-releases/new');
	}

	function navigateToDetail(id: number) {
		goto(`/app/stock-releases/${id}`);
	}

	function formatDate(dateStr: string): string {
		return formatDt(new Date(dateStr), langStore.lang);
	}

	function formatNumber(num: number): string {
		return formatNum(num, langStore.lang);
	}
</script>

<svelte:head>
	<title>Stock Releases – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10">
	<!-- Header -->
	<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
		<div>
			<h1 class="text-on-surface text-2xl font-bold sm:text-3xl">Stock Releases</h1>
			<p class="text-on-surface-variant mt-1">Track fish fry releases into your ponds</p>
		</div>
		<button
			onclick={navigateToNew}
			class="bg-primary-container inline-flex h-14 min-h-[56px] items-center justify-center gap-2 rounded-lg px-6 font-medium text-white transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
		>
			<PackagePlus size={20} aria-hidden="true" />
			Record Release
		</button>
	</div>

	<!-- Error -->
	{#if error}
		<div
			class="bg-error-container border-error/20 mb-6 rounded-xl border p-4"
			role="alert"
			aria-live="polite"
		>
			<p class="text-error font-medium">{error}</p>
		</div>
	{/if}

	<!-- Loading -->
	{#if loading}
		<div class="bg-surface-bright border-outline-variant/20 overflow-hidden rounded-xl border">
			<div class="animate-pulse">
				{#each [1, 2, 3] as _}
					<div class="border-outline-variant/10 flex items-center gap-4 border-b p-4">
						<div class="bg-surface-container h-10 w-10 shrink-0 rounded-lg"></div>
						<div class="flex-1 space-y-2">
							<div class="bg-surface-container h-4 w-1/3 rounded"></div>
							<div class="bg-surface-container h-3 w-1/4 rounded"></div>
						</div>
						<div class="bg-surface-container h-8 w-20 rounded"></div>
					</div>
				{/each}
			</div>
		</div>
	{:else if releases.length === 0}
		<!-- Empty State -->
		<div class="bg-surface-bright border-outline-variant/30 rounded-xl border p-10 text-center">
			<div class="mb-4 flex justify-center">
				<div
					class="bg-surface-container text-on-surface-variant flex h-14 w-14 items-center justify-center rounded-2xl"
				>
					<Fish size={28} aria-hidden="true" />
				</div>
			</div>
			<h2 class="text-on-surface mb-2 text-xl font-bold">No stock releases yet</h2>
			<p class="text-on-surface-variant mx-auto mb-6 max-w-md">
				Record your first fry release to start tracking stock levels and growth.
			</p>
			<button
				onclick={navigateToNew}
				class="bg-primary-container inline-flex h-14 min-h-[56px] items-center justify-center gap-2 rounded-lg px-8 font-medium text-white transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
			>
				<PackagePlus size={20} aria-hidden="true" />
				Record First Release
			</button>
		</div>
	{:else}
		<div class="bg-surface-bright border-outline-variant/30 overflow-hidden rounded-xl border">
			<table class="w-full text-left">
				<thead class="bg-surface-container/50 border-outline-variant/20 border-b">
					<tr>
						<th class="text-on-surface-variant px-4 py-3 text-sm font-semibold">Pond</th>
						<th class="text-on-surface-variant px-4 py-3 text-sm font-semibold">Species</th>
						<th class="text-on-surface-variant px-4 py-3 text-right text-sm font-semibold"
							>Quantity</th
						>
						<th class="text-on-surface-variant px-4 py-3 text-sm font-semibold">Date</th>
						<th class="text-on-surface-variant px-4 py-3 text-right text-sm font-semibold"
							>Cost (৳)</th
						>
						<th class="text-on-surface-variant px-4 py-3 text-sm font-semibold"></th>
					</tr>
				</thead>
				<tbody>
					{#each releases as release (release.id)}
						<tr
							class="border-outline-variant/10 hover:bg-surface-container/30 border-b transition-colors"
						>
							<td class="text-on-surface px-4 py-3 text-sm font-medium">
								{release.pond?.pond_number || `Pond #${release.pond_id}`}
							</td>
							<td class="text-on-surface px-4 py-3 text-sm">{release.species}</td>
							<td class="text-on-surface px-4 py-3 text-right text-sm font-medium">
								{formatNumber(release.quantity)}
							</td>
							<td class="text-on-surface-variant px-4 py-3 text-sm">
								<div class="flex items-center gap-1.5">
									<Calendar size={14} aria-hidden="true" />
									{formatDate(release.release_date)}
								</div>
							</td>
							<td class="text-on-surface px-4 py-3 text-right text-sm">
								{formatNumber(release.cost_bdt)}
							</td>
							<td class="px-4 py-3 text-right">
								<button
									onclick={() => navigateToDetail(release.id)}
									class="text-primary-container hover:text-primary inline-flex items-center gap-1 text-sm font-medium transition-colors"
								>
									View
									<ArrowRight size={14} aria-hidden="true" />
								</button>
							</td>
						</tr>
					{/each}
				</tbody>
			</table>
		</div>
	{/if}
</div>
