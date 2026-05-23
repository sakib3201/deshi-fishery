<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, FormCard, ActionButton } from '$lib/components/layout';

	let farmId = $state(0);
	let name = $state('');
	let location = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

	async function loadFarm() {
		try {
			const response = await api.get(`/farms/${farmId}`);
			if (response.success) {
				name = response.data.name;
				location = response.data.location || '';
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load farm';
		} finally {
			initialLoading = false;
		}
	}

	$effect(() => {
		const idParam = page.params.id;
		if (idParam && authStore.isAuthenticated && authStore.currentFarmId) {
			farmId = parseInt(idParam, 10);
			loadFarm();
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';
		try {
			const response = await api.patch(`/farms/${farmId}`, { name, location });
			if (response.success) {
				goto('/app/farms');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to update farm';
		} finally {
			loading = false;
		}
	}

	async function handleDelete() {
		if (!confirm('Are you sure you want to delete this farm? This action cannot be undone.')) return;
		try {
			await api.delete(`/farms/${farmId}`);
			goto('/app/farms');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete farm';
		}
	}
</script>

<svelte:head>
	<title>Edit Farm – Deshi Fishery</title>
</svelte:head>

<PageHeader title="Edit Farm" backHref="/app/farms" backLabel="Back to Farms">
	{#if initialLoading}
		<div class="flex items-center gap-2 py-8 text-on-surface-variant">
			<div
				class="h-5 w-5 animate-spin rounded-full border-2 border-on-surface-variant/30 border-t-primary-container"
			></div>
			Loading farm...
		</div>
	{:else}
		<FormCard handleSubmit={handleSubmit}>
			<div>
				<label for="name" class="mb-1.5 block text-sm font-medium text-on-surface">
					Farm Name *
				</label>
				<input
					id="name"
					type="text"
					bind:value={name}
					required
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
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
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				/>
			</div>

			{#if error}
				<div class="rounded-lg border border-error/20 bg-error-container p-3" role="alert">
					<p class="text-sm font-medium text-error">{error}</p>
				</div>
			{/if}

			<div class="flex flex-col gap-3 pt-2 sm:flex-row">
				<ActionButton variant="secondary" onclick={() => goto('/app/farms')} type="button">
					Cancel
				</ActionButton>
				<ActionButton loading={loading} disabled={!name.trim()}>
					Save Changes
				</ActionButton>
				<ActionButton variant="danger" onclick={handleDelete} type="button" class="sm:ml-auto">
					Delete Farm
				</ActionButton>
			</div>
		</FormCard>
	{/if}
</PageHeader>
