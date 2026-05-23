<script lang="ts">
	import { goto } from '$app/navigation';
	import { api, stockReleases } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ArrowLeft, Loader2 } from 'lucide-svelte';

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

		// Ensure farm ID is synced before posting (handles race on hard refresh)
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
			console.error('Stock release creation error:', err);
			error = err instanceof Error ? err.message : 'Failed to create stock release';
		} finally {
			loading = false;
		}
	}

	function goBack() {
		goto('/app/stock-releases');
	}

	const today = new Date().toISOString().split('T')[0];
</script>

<svelte:head>
	<title>New Stock Release – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<button
		onclick={goBack}
		class="text-on-surface-variant hover:text-on-surface mb-6 inline-flex min-h-[44px] items-center gap-1.5 text-sm font-medium transition-colors"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Stock Releases
	</button>

	<h1 class="text-on-surface mb-6 text-2xl font-bold sm:text-3xl">Record Fry Release</h1>

	<form
		class="bg-surface-bright border-outline-variant/30 space-y-5 rounded-xl border p-6 sm:p-8"
		onsubmit={handleSubmit}
	>
		<div>
			<label for="pond_id" class="text-on-surface mb-1.5 block text-sm font-medium">
				Pond ID *
			</label>
			<input
				id="pond_id"
				type="number"
				min="1"
				required
				bind:value={pondId}
				class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
				placeholder="Enter pond ID"
			/>
		</div>

		<div>
			<label for="species" class="text-on-surface mb-1.5 block text-sm font-medium">
				Species *
			</label>
			<select
				id="species"
				required
				bind:value={species}
				class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
			>
				<option value="" disabled selected>Select species</option>
				{#each speciesOptions as s}
					<option value={s}>{s}</option>
				{/each}
			</select>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="quantity" class="text-on-surface mb-1.5 block text-sm font-medium">
					Quantity (fish) *
				</label>
				<input
					id="quantity"
					type="number"
					min="1"
					required
					bind:value={quantity}
					class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
					placeholder="e.g., 1000"
				/>
			</div>

			<div>
				<label for="avg_weight" class="text-on-surface mb-1.5 block text-sm font-medium">
					Avg Weight (g) *
				</label>
				<input
					id="avg_weight"
					type="number"
					step="0.01"
					min="0.01"
					required
					bind:value={avgWeightGram}
					class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
					placeholder="e.g., 2.5"
				/>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="cost" class="text-on-surface mb-1.5 block text-sm font-medium">
					Cost (৳) *
				</label>
				<input
					id="cost"
					type="number"
					step="0.01"
					min="0"
					required
					bind:value={costBdt}
					class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
					placeholder="e.g., 5000"
				/>
			</div>

			<div>
				<label for="release_date" class="text-on-surface mb-1.5 block text-sm font-medium">
					Release Date *
				</label>
				<input
					id="release_date"
					type="date"
					required
					max={today}
					bind:value={releaseDate}
					class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
				/>
			</div>
		</div>

		<div>
			<label for="notes" class="text-on-surface mb-1.5 block text-sm font-medium"> Notes </label>
			<textarea
				id="notes"
				rows="3"
				bind:value={notes}
				class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 w-full resize-none rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
				placeholder="Optional notes about this release"
			></textarea>
		</div>

		{#if error}
			<div class="bg-error-container border-error/20 rounded-lg border p-3" role="alert">
				<p class="text-error text-sm font-medium">{error}</p>
			</div>
		{/if}

		<div class="flex flex-col gap-3 pt-2 sm:flex-row">
			<button
				type="button"
				onclick={goBack}
				class="border-outline-variant text-on-surface hover:bg-surface-container inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg border px-6 font-medium transition-colors"
			>
				Cancel
			</button>
			<button
				type="submit"
				disabled={loading}
				class="bg-primary-container inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg px-6 font-medium text-white transition-all duration-150 hover:brightness-110 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
			>
				{#if loading}
					<Loader2 size={20} class="mr-2 animate-spin" aria-hidden="true" />
					Saving...
				{:else}
					Record Release
				{/if}
			</button>
		</div>
	</form>
</div>
