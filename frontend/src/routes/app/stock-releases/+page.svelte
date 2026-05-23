<script lang="ts">
	import { goto } from '$app/navigation';
	import { stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import { ListPageLayout, ListState, ActionButton } from '$lib/components/layout';
	import { PackagePlus, Calendar, Fish, ArrowRight } from 'lucide-svelte';

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

	let releases = $state.raw<Release[]>([]);
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

<ListPageLayout
	title="Stock Releases"
	description="Track fish fry releases into your ponds"
>
	{#snippet action()}
		<ActionButton onclick={navigateToNew}>
			<PackagePlus size={20} aria-hidden="true" />
			Record Release
		</ActionButton>
	{/snippet}

	<ListState
		{loading}
		{error}
		empty={!loading && releases.length === 0}
		emptyTitle="No stock releases yet"
		emptyDescription="Record your first fry release to start tracking stock levels and growth."
	>
		{#snippet emptyIcon()}
			<div
				class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant"
			>
				<Fish size={28} aria-hidden="true" />
			</div>
		{/snippet}
		{#snippet emptyAction()}
			<ActionButton onclick={navigateToNew}>
				<PackagePlus size={20} aria-hidden="true" />
				Record First Release
			</ActionButton>
		{/snippet}

		<div class="overflow-hidden rounded-xl border border-outline-variant/30 bg-surface-bright">
			<table class="w-full text-left">
				<thead class="border-b border-outline-variant/20 bg-surface-container/50">
					<tr>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Pond</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Species</th>
						<th class="px-4 py-3 text-right text-sm font-semibold text-on-surface-variant">Quantity</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Date</th>
						<th class="px-4 py-3 text-right text-sm font-semibold text-on-surface-variant">Cost (৳)</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant"></th>
					</tr>
				</thead>
				<tbody>
					{#each releases as release (release.id)}
						<tr class="border-b border-outline-variant/10 transition-colors hover:bg-surface-container/30">
							<td class="px-4 py-3 text-sm font-medium text-on-surface">
								{release.pond?.pond_number || `Pond #${release.pond_id}`}
							</td>
							<td class="px-4 py-3 text-sm text-on-surface">{release.species}</td>
							<td class="px-4 py-3 text-right text-sm font-medium text-on-surface">
								{formatNumber(release.quantity)}
							</td>
							<td class="px-4 py-3 text-sm text-on-surface-variant">
								<div class="flex items-center gap-1.5">
									<Calendar size={14} aria-hidden="true" />
									{formatDate(release.release_date)}
								</div>
							</td>
							<td class="px-4 py-3 text-right text-sm text-on-surface">
								{formatNumber(release.cost_bdt)}
							</td>
							<td class="px-4 py-3 text-right">
								<button
									onclick={() => navigateToDetail(release.id)}
									class="inline-flex items-center gap-1 text-sm font-medium text-primary-container transition-colors hover:text-primary"
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
	</ListState>
</ListPageLayout>
