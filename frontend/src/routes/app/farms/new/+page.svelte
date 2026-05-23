<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { PageHeader, FormLayout } from '$lib/components/layout';

	let name = $state('');
	let location = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';
		try {
			const response = await api.post('/farms', { name, location });
			if (response.success) {
				goto('/app/farms');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create farm';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>New Farm – Deshi Fishery</title>
</svelte:head>

<PageHeader title="Create New Farm" backHref="/app/farms" backLabel="Back to Farms">
	<FormLayout
		handleSubmit={handleSubmit}
		{loading}
		{error}
		backHref="/app/farms"
		backLabel="Cancel"
		submitLabel="Create Farm"
		submitDisabled={!name.trim()}
	>
		<div>
			<label for="name" class="mb-1.5 block text-sm font-medium text-on-surface">
				Farm Name *
			</label>
			<input
				id="name"
				type="text"
				bind:value={name}
				required
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="e.g., My Fish Farm"
			/>
		</div>

		<div>
			<label for="location" class="mb-1.5 block text-sm font-medium text-on-surface">
				Location
			</label>
			<input
				id="location"
				type="text"
				bind:value={location}
				class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				placeholder="e.g., Rajshahi"
			/>
		</div>
	</FormLayout>
</PageHeader>
