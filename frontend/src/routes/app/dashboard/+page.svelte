<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	let showFarmSwitcher = $state(false);

	onMount(() => {
		if (!authStore.isAuthenticated) {
			goto('/app/login');
		}
	});

	function handleLogout() {
		authStore.logout();
		goto('/app/login');
	}

	async function handleFarmSwitch(farmId: number) {
		showFarmSwitcher = false;
		try {
			await authStore.switchFarm(farmId);
			// Refresh page to reload data for new farm
			window.location.reload();
		} catch (err) {
			console.error('Failed to switch farm:', err);
		}
	}

	function navigateToFarms() {
		goto('/app/farms');
	}
</script>

<svelte:head>
	<title>Dashboard - Deshi Fishery</title>
</svelte:head>

<div class="min-h-screen bg-surface p-6">
	<div class="mx-auto max-w-7xl">
		<div class="mb-8 flex items-center justify-between">
			<div>
				<h1 class="text-3xl font-bold text-primary-container">Dashboard</h1>
				<p class="text-on-surface-variant">Welcome back, {authStore.user?.name || 'User'}</p>
				{#if authStore.currentFarmId}
					<div class="relative mt-2">
						<button
							onclick={() => showFarmSwitcher = !showFarmSwitcher}
							class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800"
						>
							<span class="font-medium">
								{authStore.farms.find(f => f.id === authStore.currentFarmId)?.name || 'Select Farm'}
							</span>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
							</svg>
						</button>

						{#if showFarmSwitcher}
							<div class="absolute top-full left-0 mt-1 w-64 bg-white border border-slate-200 rounded-lg shadow-lg z-50">
								<div class="py-1">
									{#each authStore.farms as farm}
										<button
											onclick={() => handleFarmSwitch(farm.id)}
											class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between"
										>
											<span class="text-sm">{farm.name}</span>
											{#if farm.id === authStore.currentFarmId}
												<span class="text-xs text-blue-600">✓</span>
											{/if}
										</button>
									{/each}
								</div>
								<div class="border-t border-slate-100 py-1">
									<button
										onclick={() => { showFarmSwitcher = false; navigateToFarms(); }}
										class="w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-slate-50"
									>
										Manage Farms →
									</button>
								</div>
							</div>
						{/if}
					</div>
				{/if}
			</div>
			<div class="flex gap-3">
			<button
				onclick={() => goto('/app/ponds')}
				class="h-12 rounded-lg bg-slate-100 px-6 font-medium text-slate-700 transition-colors hover:bg-slate-200"
			>
				Ponds
			</button>
			<button
				onclick={navigateToFarms}
				class="h-12 rounded-lg bg-slate-100 px-6 font-medium text-slate-700 transition-colors hover:bg-slate-200"
			>
				Farms
			</button>
				<button
					onclick={handleLogout}
					class="h-12 rounded-lg bg-error px-6 font-medium text-white transition-colors hover:bg-error/90"
				>
					Logout
				</button>
			</div>
		</div>

		<div class="grid gap-6 md:grid-cols-3">
			<div class="rounded-2xl bg-white p-6 shadow-sm">
				<h3 class="text-lg font-medium text-on-surface-variant">Total Ponds</h3>
				<p class="mt-2 text-3xl font-bold text-primary-container">0</p>
			</div>
			<div class="rounded-2xl bg-white p-6 shadow-sm">
				<h3 class="text-lg font-medium text-on-surface-variant">Total Stock</h3>
				<p class="mt-2 text-3xl font-bold text-primary-container">0 kg</p>
			</div>
			<div class="rounded-2xl bg-white p-6 shadow-sm">
				<h3 class="text-lg font-medium text-on-surface-variant">This Month's Sales</h3>
				<p class="mt-2 text-3xl font-bold text-primary-container">৳ 0</p>
			</div>
		</div>
	</div>
</div>
