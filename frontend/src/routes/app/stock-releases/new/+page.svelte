<script lang="ts">
	import { goto } from '$app/navigation';
	import { api, stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, FormLayout } from '$lib/components/layout';

	const speciesOptions = [
		'Rui',
		'Katla',
		'Mrigal',
		'Tilapia',
		'Pangas',
		'Koi',
		'Silver Carp',
		'Grass Carp'
	];

	let pondId = $state('');
	let species = $state('');
	let quantity = $state('');
	let avgWeightGram = $state('');
	let costBdt = $state('');
	let releaseDate = $state('');
	let notes = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';

		const farmId = authStore.currentFarmId;
		if (!farmId) {
			error = 'Farm context not ready. Please wait a moment and try again.';
			loading = false;
			return;
		}
		api.setFarmId(String(farmId));

		try {
			const response = await stockReleases.create({
				pond_id: parseInt(pondId, 10),
				species: species.trim(),
				quantity: parseInt(quantity, 10),
				avg_weight_gram: parseFloat(avgWeightGram),
				cost_bdt: parseFloat(costBdt),
				release_date: releaseDate,
				notes: notes.trim() || null
			});
			if (response.success) {
				goto('/app/stock-releases');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create stock release';
		} finally {
			loading = false;
		}
	}

	const today = new Date().toISOString().split('T')[0];
</script>

<svelte:head>
	<title>New Stock Release – Deshi Fishery</title>
</svelte:head>

<PageHeader
	title="Record Fry Release"
	backHref="/app/stock-releases"
	backLabel="Back to Stock Releases"
>
	<FormLayout
		handleSubmit={handleSubmit}
		{loading}
		{error}
		backHref="/app/stock-releases"
		backLabel="Cancel"
		submitLabel="Record Release"
	>
		<div>
			<label for="pond_id" class="mb-1.5 block text-sm font-medium text-on-surface">
				Pond ID *
			</label>
			<input
				id="pond_id"
				type="number"
				min="1"
				required
				bind:value={pondId}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="Enter pond ID"
			/>
		</div>

		<div>
			<label for="species" class="mb-1.5 block text-sm font-medium text-on-surface">
				Species *
			</label>
			<select
				id="species"
				required
				bind:value={species}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
			>
				<option value="" disabled selected>Select species</option>
				{#each speciesOptions as s}
					<option value={s}>{s}</option>
				{/each}
			</select>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="quantity" class="mb-1.5 block text-sm font-medium text-on-surface">
					Quantity (fish) *
				</label>
				<input
					id="quantity"
					type="number"
					min="1"
					required
					bind:value={quantity}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 1000"
				/>
			</div>

			<div>
				<label for="avg_weight" class="mb-1.5 block text-sm font-medium text-on-surface">
					Avg Weight (g) *
				</label>
				<input
					id="avg_weight"
					type="number"
					step="0.01"
					min="0.01"
					required
					bind:value={avgWeightGram}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 2.5"
				/>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="cost" class="mb-1.5 block text-sm font-medium text-on-surface">
					Cost (৳) *
				</label>
				<input
					id="cost"
					type="number"
					step="0.01"
					min="0"
					required
					bind:value={costBdt}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 5000"
				/>
			</div>

			<div>
				<label for="release_date" class="mb-1.5 block text-sm font-medium text-on-surface">
					Release Date *
				</label>
				<input
					id="release_date"
					type="date"
					required
					max={today}
					bind:value={releaseDate}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				/>
			</div>
		</div>

		<div>
			<label for="notes" class="mb-1.5 block text-sm font-medium text-on-surface">Notes</label>
			<textarea
				id="notes"
				rows="3"
				bind:value={notes}
				class="w-full resize-none rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="Optional notes about this release"
			></textarea>
		</div>
	</FormLayout>
</PageHeader>
