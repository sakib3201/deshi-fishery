<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ArrowLeft } from 'lucide-svelte';

	let pondNumber = $state('');
	let size = $state('');
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
			const response = await api.post('/ponds', {
				pond_number: pondNumber.trim(),
				size: size ? parseFloat(size) : null
			});
			if (response.success) {
				goto('/app/ponds');
			}
		} catch (err) {
			console.error('Pond creation error:', err);
			error = err instanceof Error ? err.message : 'Failed to create pond';
		} finally {
			loading = false;
		}
	}

	function goBack() {
		goto('/app/ponds');
	}
</script>

<svelte:head>
	<title>New Pond – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<button
		onclick={goBack}
		class="text-on-surface-variant hover:text-on-surface mb-6 inline-flex min-h-[44px] items-center gap-1.5 text-sm font-medium transition-colors"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Ponds
	</button>

	<h1 class="text-on-surface mb-6 text-2xl font-bold sm:text-3xl">Add New Pond</h1>

	<form
		class="bg-surface-bright border-outline-variant/30 space-y-5 rounded-xl border p-6 sm:p-8"
		onsubmit={handleSubmit}
	>
		<div>
			<label for="pond_number" class="text-on-surface mb-1.5 block text-sm font-medium">
				Pond Number *
			</label>
			<input
				id="pond_number"
				type="text"
				bind:value={pondNumber}
				required
				class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
				placeholder="e.g., Pond 1"
			/>
		</div>

		<div>
			<label for="size" class="text-on-surface mb-1.5 block text-sm font-medium">
				Size (acres)
			</label>
			<input
				id="size"
				type="number"
				step="0.01"
				min="0"
				bind:value={size}
				class="dark:bg-surface-container border-outline-variant focus:border-secondary focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50 min-h-[56px] w-full rounded-lg border bg-white px-4 py-3 focus:ring-2 focus:outline-none"
				placeholder="e.g., 0.5"
			/>
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
				disabled={loading || !pondNumber.trim()}
				class="bg-primary-container inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg px-6 font-medium text-white transition-all duration-150 hover:brightness-110 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
			>
				{loading ? 'Creating...' : 'Create Pond'}
			</button>
		</div>
	</form>
</div>
