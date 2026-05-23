<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ArrowLeft } from 'lucide-svelte';

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
		if (!confirm('Are you sure you want to delete this farm? This action cannot be undone.')) {
			return;
		}

		try {
			await api.delete(`/farms/${farmId}`);
			goto('/app/farms');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete farm';
		}
	}

	function goBack() {
		goto('/app/farms');
	}
</script>

<svelte:head>
	<title>Edit Farm – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<button
		onclick={goBack}
		class="inline-flex items-center gap-1.5 text-sm font-medium text-on-surface-variant hover:text-on-surface transition-colors mb-6 min-h-[44px]"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Farms
	</button>

	<h1 class="text-2xl sm:text-3xl font-bold text-on-surface mb-6">Edit Farm</h1>

	{#if initialLoading}
		<div class="flex items-center gap-2 text-on-surface-variant py-8">
			<div class="h-5 w-5 border-2 border-on-surface-variant/30 border-t-primary-container rounded-full animate-spin"></div>
			Loading farm...
		</div>
	{:else}
		<form
			class="space-y-5 bg-surface-bright border border-outline-variant/30 rounded-xl p-6 sm:p-8"
			onsubmit={handleSubmit}
		>
			<div>
				<label for="name" class="block text-sm font-medium text-on-surface mb-1.5">
					Farm Name *
				</label>
				<input
					id="name"
					type="text"
					bind:value={name}
					required
					class="w-full px-4 py-3 min-h-[56px] bg-white dark:bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-secondary focus:ring-2 focus:ring-mist-light text-on-surface"
				/>
			</div>

			<div>
				<label for="location" class="block text-sm font-medium text-on-surface mb-1.5">
					Location
				</label>
				<input
					id="location"
					type="text"
					bind:value={location}
					class="w-full px-4 py-3 min-h-[56px] bg-white dark:bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-secondary focus:ring-2 focus:ring-mist-light text-on-surface"
				/>
			</div>

			{#if error}
				<div class="rounded-lg bg-error-container border border-error/20 p-3" role="alert">
					<p class="text-error text-sm font-medium">{error}</p>
				</div>
			{/if}

			<div class="flex flex-col sm:flex-row gap-3 pt-2">
				<button
					type="button"
					onclick={goBack}
					class="inline-flex items-center justify-center h-14 px-6 rounded-lg border border-outline-variant text-on-surface font-medium hover:bg-surface-container transition-colors min-h-[56px]"
				>
					Cancel
				</button>
				<button
					type="submit"
					disabled={loading || !name.trim()}
					class="inline-flex items-center justify-center h-14 px-6 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed min-h-[56px]"
				>
					{loading ? 'Saving...' : 'Save Changes'}
				</button>
				<button
					type="button"
					onclick={handleDelete}
					class="sm:ml-auto inline-flex items-center justify-center h-14 px-6 rounded-lg border border-error/30 text-error font-medium hover:bg-error-container transition-colors min-h-[56px]"
				>
					Delete Farm
				</button>
			</div>
		</form>
	{/if}
</div>
