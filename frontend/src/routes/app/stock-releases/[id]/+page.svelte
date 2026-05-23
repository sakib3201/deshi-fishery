<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import { PageHeader, DataCard } from '$lib/components/layout';
	import { Calendar, Fish, Loader2, Trash2, Pencil } from 'lucide-svelte';

	interface Release {
		id: number;
		pond_id: number;
		species: string;
		quantity: number;
		avg_weight_gram: number;
		cost_bdt: number;
		release_date: string;
		notes: string | null;
		created_at: string;
		pond?: { pond_number: string };
	}

	let release = $state.raw<Release | null>(null);
	let loading = $state(true);
	let error = $state('');
	let deleteLoading = $state(false);

	async function loadRelease() {
		const idParam = page.params.id;
		if (!idParam) {
			error = 'Invalid release ID';
			loading = false;
			return;
		}
		const id = parseInt(idParam, 10);
		if (!id) {
			error = 'Invalid release ID';
			loading = false;
			return;
		}
		try {
			const response = await stockReleases.get(id);
			if (response.success) {
				release = response.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load stock release';
		} finally {
			loading = false;
		}
	}

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadRelease();
		}
	});

	function navigateToEdit() {
		if (release) {
			goto(`/app/stock-releases/${release.id}/edit`);
		}
	}

	async function handleDelete() {
		if (!release) return;
		if (!confirm(`Delete this ${release.species} release? This cannot be undone.`)) return;
		deleteLoading = true;
		try {
			await stockReleases.delete(release.id);
			goto('/app/stock-releases');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete release';
			deleteLoading = false;
		}
	}

	function formatDate(dateStr: string): string {
		return formatDt(new Date(dateStr), langStore.lang);
	}

	function formatNumber(num: number): string {
		return formatNum(num, langStore.lang);
	}
</script>

<svelte:head>
	<title>{release ? `${release.species} Release` : 'Stock Release'} – Deshi Fishery</title>
</svelte:head>

<PageHeader
	title={release ? `${release.species} Release` : 'Stock Release'}
	backHref="/app/stock-releases"
	backLabel="Back to Stock Releases"
>
	{#if loading}
		<div class="animate-pulse rounded-xl border border-outline-variant/30 bg-surface-bright p-8">
			<div class="mb-4 h-8 w-1/2 rounded bg-surface-container"></div>
			<div class="mb-6 h-4 w-1/3 rounded bg-surface-container"></div>
			<div class="space-y-3">
				<div class="h-4 w-full rounded bg-surface-container"></div>
				<div class="h-4 w-3/4 rounded bg-surface-container"></div>
				<div class="h-4 w-1/2 rounded bg-surface-container"></div>
			</div>
		</div>
	{:else if error}
		<div class="rounded-xl border border-error/20 bg-error-container p-4" role="alert">
			<p class="font-medium text-error">{error}</p>
			<a
				href="/app/stock-releases"
				class="mt-3 inline-flex h-11 items-center justify-center rounded-lg border border-outline-variant px-4 font-medium text-on-surface transition-colors hover:bg-surface-container"
			>
				Go Back
			</a>
		</div>
	{:else if release}
		<div class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6 sm:p-8">
			<div class="mb-6 flex items-start justify-between">
				<div>
					<div class="mb-2 flex items-center gap-2">
						<div
							class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container/10 text-primary-container"
						>
							<Fish size={20} aria-hidden="true" />
						</div>
						<h1 class="text-2xl font-bold text-on-surface">{release.species}</h1>
					</div>
					<p class="text-on-surface-variant">
						Released into {release.pond?.pond_number || `Pond #${release.pond_id}`}
					</p>
				</div>
				<div class="flex gap-2">
					<button
						onclick={navigateToEdit}
						class="inline-flex h-11 items-center justify-center gap-1.5 rounded-lg border border-outline-variant px-4 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container"
					>
						<Pencil size={16} aria-hidden="true" />
						Edit
					</button>
					<button
						onclick={handleDelete}
						disabled={deleteLoading}
						class="inline-flex h-11 items-center justify-center gap-1.5 rounded-lg border border-error/30 px-4 text-sm font-medium text-error transition-colors hover:bg-error-container disabled:opacity-50"
					>
						{#if deleteLoading}
							<Loader2 size={16} class="animate-spin" aria-hidden="true" />
						{:else}
							<Trash2 size={16} aria-hidden="true" />
						{/if}
						Delete
					</button>
				</div>
			</div>

			<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
				<DataCard label="Quantity" value={`${formatNumber(release.quantity)} fish`} />
				<DataCard label="Average Weight" value={`${release.avg_weight_gram} g`} />
				<DataCard label="Total Cost" value={`৳${formatNumber(release.cost_bdt)}`} />
				<DataCard label="Release Date" value={formatDate(release.release_date)}>
					{#snippet icon()}<Calendar size={18} aria-hidden="true" />{/snippet}
				</DataCard>
			</div>

			{#if release.notes}
				<div class="border-t border-outline-variant/20 pt-4">
					<p class="mb-1 text-sm text-on-surface-variant">Notes</p>
					<p class="whitespace-pre-wrap text-on-surface">{release.notes}</p>
				</div>
			{/if}

			<div class="mt-4 border-t border-outline-variant/20 pt-4">
				<p class="text-xs text-on-surface-variant">
					Recorded on {formatDt(new Date(release.created_at), langStore.lang)}
				</p>
			</div>
		</div>
	{/if}
</PageHeader>
