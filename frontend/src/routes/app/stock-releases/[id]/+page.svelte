<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import {
		ArrowLeft,
		Calendar,
		Fish,
		Scale,
		Banknote,
		Loader2,
		Trash2,
		Pencil
	} from 'lucide-svelte';

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

	let release = $state<Release | null>(null);
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

	function goBack() {
		goto('/app/stock-releases');
	}

	function navigateToEdit() {
		if (release) {
			goto(`/app/stock-releases/${release.id}/edit`);
		}
	}

	async function handleDelete() {
		if (!release) return;

		if (!confirm(`Delete this ${release.species} release? This cannot be undone.`)) {
			return;
		}

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

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<button
		onclick={goBack}
		class="text-on-surface-variant hover:text-on-surface mb-6 inline-flex min-h-[44px] items-center gap-1.5 text-sm font-medium transition-colors"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Stock Releases
	</button>

	{#if loading}
		<div class="bg-surface-bright border-outline-variant/30 animate-pulse rounded-xl border p-8">
			<div class="bg-surface-container mb-4 h-8 w-1/2 rounded"></div>
			<div class="bg-surface-container mb-6 h-4 w-1/3 rounded"></div>
			<div class="space-y-3">
				<div class="bg-surface-container h-4 w-full rounded"></div>
				<div class="bg-surface-container h-4 w-3/4 rounded"></div>
				<div class="bg-surface-container h-4 w-1/2 rounded"></div>
			</div>
		</div>
	{:else if error}
		<div class="bg-error-container border-error/20 rounded-xl border p-4" role="alert">
			<p class="text-error font-medium">{error}</p>
			<button
				onclick={goBack}
				class="border-outline-variant text-on-surface hover:bg-surface-container mt-3 inline-flex h-11 items-center justify-center rounded-lg border px-4 font-medium transition-colors"
			>
				Go Back
			</button>
		</div>
	{:else if release}
		<div class="bg-surface-bright border-outline-variant/30 rounded-xl border p-6 sm:p-8">
			<div class="mb-6 flex items-start justify-between">
				<div>
					<div class="mb-2 flex items-center gap-2">
						<div
							class="bg-primary-container/10 text-primary-container flex h-10 w-10 items-center justify-center rounded-xl"
						>
							<Fish size={20} aria-hidden="true" />
						</div>
						<h1 class="text-on-surface text-2xl font-bold">{release.species}</h1>
					</div>
					<p class="text-on-surface-variant">
						Released into {release.pond?.pond_number || `Pond #${release.pond_id}`}
					</p>
				</div>
				<div class="flex gap-2">
					<button
						onclick={navigateToEdit}
						class="border-outline-variant text-on-surface hover:bg-surface-container inline-flex h-11 items-center justify-center gap-1.5 rounded-lg border px-4 text-sm font-medium transition-colors"
					>
						<Pencil size={16} aria-hidden="true" />
						Edit
					</button>
					<button
						onclick={handleDelete}
						disabled={deleteLoading}
						class="border-error/30 text-error hover:bg-error-container inline-flex h-11 items-center justify-center gap-1.5 rounded-lg border px-4 text-sm font-medium transition-colors disabled:opacity-50"
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
				<div class="bg-surface-container/50 rounded-lg p-4">
					<p class="text-on-surface-variant mb-1 text-sm">Quantity</p>
					<p class="text-on-surface text-xl font-bold">{formatNumber(release.quantity)} fish</p>
				</div>
				<div class="bg-surface-container/50 rounded-lg p-4">
					<p class="text-on-surface-variant mb-1 text-sm">Average Weight</p>
					<p class="text-on-surface text-xl font-bold">{release.avg_weight_gram} g</p>
				</div>
				<div class="bg-surface-container/50 rounded-lg p-4">
					<p class="text-on-surface-variant mb-1 text-sm">Total Cost</p>
					<p class="text-on-surface text-xl font-bold">৳{formatNumber(release.cost_bdt)}</p>
				</div>
				<div class="bg-surface-container/50 rounded-lg p-4">
					<p class="text-on-surface-variant mb-1 text-sm">Release Date</p>
					<p class="text-on-surface flex items-center gap-1.5 text-xl font-bold">
						<Calendar size={18} aria-hidden="true" />
						{formatDate(release.release_date)}
					</p>
				</div>
			</div>

			{#if release.notes}
				<div class="border-outline-variant/20 border-t pt-4">
					<p class="text-on-surface-variant mb-1 text-sm">Notes</p>
					<p class="text-on-surface whitespace-pre-wrap">{release.notes}</p>
				</div>
			{/if}

			<div class="border-outline-variant/20 mt-4 border-t pt-4">
				<p class="text-on-surface-variant text-xs">
					Recorded on {formatDt(new Date(release.created_at), langStore.lang)}
				</p>
			</div>
		</div>
	{/if}
</div>
