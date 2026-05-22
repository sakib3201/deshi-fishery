<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	onMount(() => {
		if (!authStore.isAuthenticated) {
			goto('/app/login');
		}
	});

	function handleLogout() {
		authStore.logout();
		goto('/app/login');
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
			</div>
			<button
				onclick={handleLogout}
				class="h-12 rounded-lg bg-error px-6 font-medium text-white transition-colors hover:bg-error/90"
			>
				Logout
			</button>
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
