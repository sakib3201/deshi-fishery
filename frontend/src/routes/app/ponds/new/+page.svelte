<script lang="ts">
	import { goto } from '$app/navigation';
	import { api } from '$lib/api/client';
	import { ArrowLeft } from 'lucide-svelte';

	let pondNumber = $state('');
	let size = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		loading = true;
		error = '';

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
		class="inline-flex items-center gap-1.5 text-sm font-medium text-on-surface-variant hover:text-on-surface transition-colors mb-6 min-h-[44px]"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Ponds
	</button>

	<h1 class="text-2xl sm:text-3xl font-bold text-on-surface mb-6">Add New Pond</h1>

	<form
		class="space-y-5 bg-surface-bright border border-outline-variant/30 rounded-xl p-6 sm:p-8"
		onsubmit={handleSubmit}
	>
		<div>
			<label for="pond_number" class="block text-sm font-medium text-on-surface mb-1.5">
				Pond Number *
			</label>
			<input
				id="pond_number"
				type="text"
				bind:value={pondNumber}
				required
				class="w-full px-4 py-3 min-h-[56px] bg-white dark:bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-secondary focus:ring-2 focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50"
				placeholder="e.g., Pond 1"
			/>
		</div>

		<div>
			<label for="size" class="block text-sm font-medium text-on-surface mb-1.5">
				Size (acres)
			</label>
			<input
				id="size"
				type="number"
				step="0.01"
				min="0"
				bind:value={size}
				class="w-full px-4 py-3 min-h-[56px] bg-white dark:bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-secondary focus:ring-2 focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50"
				placeholder="e.g., 0.5"
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
				disabled={loading || !pondNumber.trim()}
				class="inline-flex items-center justify-center h-14 px-6 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed min-h-[56px]"
			>
				{loading ? 'Creating...' : 'Create Pond'}
			</button>
		</div>
	</form>
</div>
