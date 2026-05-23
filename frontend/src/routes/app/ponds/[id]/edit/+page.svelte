<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, FormCard, ActionButton } from '$lib/components/layout';

	let pondId = $state(0);
	let pondNumber = $state('');
	let size = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

	async function loadPond() {
		try {
			const response = await api.get(`/ponds/${pondId}`);
			if (response.success) {
				pondNumber = response.data.pond_number;
				size = response.data.size ? String(response.data.size) : '';
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load pond';
		} finally {
			initialLoading = false;
		}
	}

	$effect(() => {
		const idParam = page.params.id;
		if (idParam && authStore.isAuthenticated && authStore.currentFarmId) {
			pondId = parseInt(idParam, 10);
			loadPond();
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';
		try {
			const response = await api.patch(`/ponds/${pondId}`, {
				pond_number: pondNumber.trim(),
				size: size ? parseFloat(size) : null
			});
			if (response.success) {
				goto('/app/ponds');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to update pond';
		} finally {
			loading = false;
		}
	}

	async function handleDelete() {
		if (!confirm('Are you sure you want to delete this pond?')) return;
		try {
			await api.delete(`/ponds/${pondId}`);
			goto('/app/ponds');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete pond';
		}
	}
</script>

<svelte:head>
	<title>Edit Pond – Deshi Fishery</title>
</svelte:head>

<PageHeader title="Edit Pond" backHref="/app/ponds" backLabel="Back to Ponds">
	{#if initialLoading}
		<div class="flex items-center gap-2 py-8 text-on-surface-variant">
			<div
				class="h-5 w-5 animate-spin rounded-full border-2 border-on-surface-variant/30 border-t-primary-container"
			></div>
			Loading pond...
		</div>
	{:else}
		<FormCard handleSubmit={handleSubmit}>
			<div>
				<label for="pond_number" class="mb-1.5 block text-sm font-medium text-on-surface">
					Pond Number *
				</label>
				<input
					id="pond_number"
					type="text"
					bind:value={pondNumber}
					required
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
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
					class="min-h-[56px] w-full rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
				/>
			</div>

			{#if error}
				<div class="rounded-lg border border-error/20 bg-error-container p-3" role="alert">
					<p class="text-sm font-medium text-error">{error}</p>
				</div>
			{/if}

			<div class="flex flex-col gap-3 pt-2 sm:flex-row">
				<ActionButton variant="secondary" onclick={() => goto('/app/ponds')} type="button">
					Cancel
				</ActionButton>
				<ActionButton
					loading={loading}
					disabled={!pondNumber.trim()}
				>
					Save Changes
				</ActionButton>
				<ActionButton
					variant="danger"
					onclick={handleDelete}
					type="button"
					class="sm:ml-auto"
				>
					Delete Pond
				</ActionButton>
			</div>
		</FormCard>
	{/if}
</PageHeader>
