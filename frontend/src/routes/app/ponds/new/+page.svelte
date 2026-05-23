<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, FormLayout } from '$lib/components/layout';

	let pondNumber = $state('');
	let size = $state('');
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
			const response = await api.post('/ponds', {
				pond_number: pondNumber.trim(),
				size: size ? parseFloat(size) : null
			});
			if (response.success) {
				goto('/app/ponds');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create pond';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>New Pond – Deshi Fishery</title>
</svelte:head>

<PageHeader title="Add New Pond" backHref="/app/ponds" backLabel="Back to Ponds">
	<FormLayout
		handleSubmit={handleSubmit}
		{loading}
		{error}
		backHref="/app/ponds"
		backLabel="Cancel"
		submitLabel="Create Pond"
		submitDisabled={!pondNumber.trim()}
	>
		<div>
			<label for="pond_number" class="mb-1.5 block text-sm font-medium text-on-surface">
				Pond Number *
			</label>
			<input
				id="pond_number"
				type="text"
				bind:value={pondNumber}
				required
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="e.g., Pond 1"
			/>
		</div>

		<div>
			<label for="size" class="mb-1.5 block text-sm font-medium text-on-surface">
				Size (acres)
			</label>
			<input
				id="size"
				type="number"
				step="0.01"
				min="0"
				bind:value={size}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="e.g., 0.5"
			/>
		</div>
	</FormLayout>
</PageHeader>
