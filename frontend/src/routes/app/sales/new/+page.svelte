<script lang="ts">
	import { goto } from '$app/navigation';
	import { api, sales } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, FormLayout } from '$lib/components/layout';
	import { Receipt } from 'lucide-svelte';

	const fishTypeOptions = [
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
	let saleType = $state('wholesale');
	let date = $state('');
	let fishType = $state('');
	let avgFishWeightG = $state('');
	let quantityKg = $state('');
	let ratePerKg = $state('');
	let customerName = $state('');
	let notes = $state('');
	let loading = $state(false);
	let error = $state('');

	let totalAmount = $derived(
		quantityKg && ratePerKg ? (parseFloat(quantityKg) * parseFloat(ratePerKg)).toFixed(2) : '0.00'
	);

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
			const response = await sales.create({
				pond_id: parseInt(pondId, 10),
				// eslint-disable-next-line @typescript-eslint/no-explicit-any
			sale_type: saleType as any,
				date: date,
				fish_type: fishType.trim(),
				avg_fish_weight_g: parseFloat(avgFishWeightG),
				quantity_kg: parseFloat(quantityKg),
				rate_per_kg: parseFloat(ratePerKg),
				customer_name: customerName.trim() || null,
				notes: notes.trim() || null
			});
			if (response.success) {
				goto('/app/sales');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create sale';
		} finally {
			loading = false;
		}
	}

	const today = new Date().toISOString().split('T')[0];
</script>

<svelte:head>
	<title>New Sale – Deshi Fishery</title>
</svelte:head>

<PageHeader title="Record Sale" backHref="/app/sales" backLabel="Back to Sales">
	<FormLayout
		handleSubmit={handleSubmit}
		{loading}
		{error}
		backHref="/app/sales"
		backLabel="Cancel"
		submitLabel="Record Sale"
	>
		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
				<label for="sale_type" class="mb-1.5 block text-sm font-medium text-on-surface">
					Sale Type *
				</label>
				<select
					id="sale_type"
					required
					bind:value={saleType}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				>
					<option value="wholesale">Wholesale</option>
					<option value="retail">Retail</option>
				</select>
			</div>
		</div>

		<div>
			<label for="date" class="mb-1.5 block text-sm font-medium text-on-surface">
				Sale Date *
			</label>
			<input
				id="date"
				type="date"
				required
				max={today}
				bind:value={date}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
			/>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="fish_type" class="mb-1.5 block text-sm font-medium text-on-surface">
					Fish Type *
				</label>
				<select
					id="fish_type"
					required
					bind:value={fishType}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				>
					<option value="" disabled selected>Select fish type</option>
					{#each fishTypeOptions as ft}
						<option value={ft}>{ft}</option>
					{/each}
				</select>
			</div>

			<div>
				<label for="avg_weight" class="mb-1.5 block text-sm font-medium text-on-surface">
					Avg Fish Weight (g) *
				</label>
				<input
					id="avg_weight"
					type="number"
					step="0.01"
					min="0.01"
					required
					bind:value={avgFishWeightG}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 250"
				/>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
			<div>
				<label for="quantity" class="mb-1.5 block text-sm font-medium text-on-surface">
					Quantity (kg) *
				</label>
				<input
					id="quantity"
					type="number"
					step="0.01"
					min="0.01"
					required
					bind:value={quantityKg}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 100"
				/>
			</div>

			<div>
				<label for="rate" class="mb-1.5 block text-sm font-medium text-on-surface">
					Rate per kg (৳) *
				</label>
				<input
					id="rate"
					type="number"
					step="0.01"
					min="0.01"
					required
					bind:value={ratePerKg}
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
					placeholder="e.g., 250"
				/>
			</div>
		</div>

		<div class="rounded-lg bg-surface-container/50 p-4">
			<p class="mb-1 text-sm text-on-surface-variant">Total Amount</p>
			<p class="text-2xl font-bold text-on-surface">৳{totalAmount}</p>
		</div>

		<div>
			<label for="customer_name" class="mb-1.5 block text-sm font-medium text-on-surface">
				Customer Name
			</label>
			<input
				id="customer_name"
				type="text"
				bind:value={customerName}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="e.g., Ali Bhai"
			/>
		</div>

		<div>
			<label for="notes" class="mb-1.5 block text-sm font-medium text-on-surface">Notes</label>
			<textarea
				id="notes"
				rows="3"
				bind:value={notes}
				class="w-full resize-none rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="Optional notes about this sale"
			></textarea>
		</div>
	</FormLayout>
</PageHeader>
