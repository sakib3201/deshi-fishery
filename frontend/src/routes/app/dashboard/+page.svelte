<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import {
		Droplets,
		Fish,
		TrendingUp,
		DollarSign,
		ChevronDown,
		ChevronUp,
		Waves
	} from 'lucide-svelte';

	let showFarmSwitcher = $state(false);

	async function handleFarmSwitch(farmId: number) {
		showFarmSwitcher = false;
		try {
			await authStore.switchFarm(farmId);
			window.location.reload();
		} catch (err) {
			console.error('Failed to switch farm:', err);
		}
	}

	function navigateToFarms() {
		showFarmSwitcher = false;
		goto('/app/farms');
	}

	function navigateTo(path: string) {
		goto(path);
	}

	const quickActions = [
		{ label: 'Add Pond', href: '/app/ponds/new', icon: Droplets, color: 'bg-accent-sky text-accent-sky-text' },
		{ label: 'Record Sale', href: '#', icon: DollarSign, color: 'bg-accent-emerald text-accent-emerald-text', disabled: true },
		{ label: 'Log Feed', href: '#', icon: Fish, color: 'bg-accent-amber text-accent-amber-text', disabled: true },
		{ label: 'Add Expense', href: '#', icon: TrendingUp, color: 'bg-accent-rose text-accent-rose-text', disabled: true },
	];

	const stats = [
		{ label: 'Total Ponds', value: '0', icon: Droplets, iconBg: 'bg-accent-sky text-accent-sky-text' },
		{ label: 'Total Stock', value: '0 kg', icon: Fish, iconBg: 'bg-accent-emerald text-accent-emerald-text' },
		{ label: "This Month's Sales", value: '\u09F3 0', icon: DollarSign, iconBg: 'bg-accent-amber text-accent-amber-text' },
	];

	let currentFarmName = $derived(
		authStore.farms.find((f) => f.id === authStore.currentFarmId)?.name || 'Select Farm'
	);
</script>

<svelte:head>
	<title>Dashboard – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10">
	<!-- Header -->
	<div class="mb-8">
		<h1 class="mb-2 text-2xl font-bold text-on-surface sm:text-3xl">Dashboard</h1>
		<p class="text-on-surface-variant">Welcome back, {authStore.user?.name || 'User'}</p>

		<!-- Farm Switcher -->
		{#if authStore.currentFarmId}
			<div class="relative mt-3 inline-block" data-testid="farm-switcher">
				<button
					onclick={() => (showFarmSwitcher = !showFarmSwitcher)}
					class="flex min-h-[44px] items-center gap-2 rounded-lg bg-surface-container px-4 py-2.5 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container-high"
					aria-label="Current farm: {currentFarmName}. Click to switch farms."
					aria-expanded={showFarmSwitcher}
					aria-haspopup="listbox"
				>
					<Waves size={18} class="text-primary-container" aria-hidden="true" />
					<span class="font-medium" data-testid="current-farm-name">{currentFarmName}</span>
					{#if showFarmSwitcher}
						<ChevronUp size={16} aria-hidden="true" />
					{:else}
						<ChevronDown size={16} aria-hidden="true" />
					{/if}
				</button>

				{#if showFarmSwitcher}
					<div
						class="absolute top-full left-0 z-dropdown mt-2 w-64 rounded-xl border border-outline-variant/30 bg-surface-bright py-1.5 shadow-elevated"
						role="listbox"
						aria-label="Select a farm"
					>
						<div class="py-1">
							{#each authStore.farms as farm (farm.id)}
								<button
									onclick={() => handleFarmSwitch(farm.id)}
									class="flex min-h-[44px] w-full items-center justify-between px-4 py-2.5 text-left text-sm text-on-surface transition-colors hover:bg-surface-container"
									role="option"
									aria-selected={farm.id === authStore.currentFarmId}
									data-testid="farm-option-{farm.id}"
								>
									<span>{farm.name}</span>
									{#if farm.id === authStore.currentFarmId}
										<span class="text-xs font-medium text-primary-container" aria-label="Selected"
											>✓</span
										>
									{/if}
								</button>
							{/each}
						</div>
						<div class="border-t border-outline-variant/20 pt-1">
							<a
								href="/app/farms"
								class="block min-h-[44px] px-4 py-2.5 text-sm font-medium text-primary-container transition-colors hover:bg-surface-container"
								onclick={() => (showFarmSwitcher = false)}
							>
								Manage Farms →
							</a>
						</div>
					</div>
				{/if}
			</div>
		{:else}
			<div data-testid="farm-switcher" data-no-farm="true" class="mt-3">
				<span class="text-sm text-on-surface-variant">No farm selected</span>
			</div>
		{/if}
	</div>

	<!-- Stats Grid -->
	<div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
		{#each stats as stat}
			<div
				class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6 transition-shadow duration-200 hover:shadow-md"
			>
				<div class="mb-4 flex items-start justify-between">
					<div class="flex h-11 w-11 items-center justify-center rounded-xl {stat.iconBg}">
						<stat.icon size={22} aria-hidden="true" />
					</div>
				</div>
				<p class="mb-1 text-sm font-medium text-on-surface-variant">{stat.label}</p>
				<p class="text-3xl font-bold tracking-tight text-on-surface">{stat.value}</p>
			</div>
		{/each}
	</div>

	<!-- Quick Actions -->
	<div class="mb-10">
		<h2 class="mb-4 text-xl font-bold text-on-surface">Quick Actions</h2>
		<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
			{#each quickActions as action}
				<button
					onclick={() => !action.disabled && navigateTo(action.href)}
					disabled={action.disabled}
					class="flex min-h-[120px] flex-col items-center justify-center gap-3 rounded-xl border border-outline-variant/30 bg-surface-bright p-5 transition-all duration-200 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:shadow-none"
				>
					<div class="flex h-12 w-12 items-center justify-center rounded-xl {action.color}">
						<action.icon size={24} aria-hidden="true" />
					</div>
					<span class="text-sm font-medium text-on-surface">{action.label}</span>
				</button>
			{/each}
		</div>
	</div>

	<!-- Recent Activity Placeholder -->
	<div class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6">
		<h2 class="mb-4 text-xl font-bold text-on-surface">Recent Activity</h2>
		<div class="py-10 text-center">
			<div class="mb-3 flex justify-center">
				<div
					class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-container text-on-surface-variant"
				>
					<TrendingUp size={24} aria-hidden="true" />
				</div>
			</div>
			<p class="text-on-surface-variant">No recent activity to show.</p>
			<p class="mt-1 text-sm text-on-surface-variant/70">
				Record sales, feed, or expenses to see them here.
			</p>
		</div>
	</div>
</div>
