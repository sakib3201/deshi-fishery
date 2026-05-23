<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { authStore } from '$lib/stores/auth.svelte';
	import { AppNavbar, AppFooter } from '$lib/components/layout';
	import { themeStore, langStore } from '$lib/stores/ui.svelte';

	let { children } = $props();

	// Initialize stores once on first client render
	let initialized = $state(false);
	$effect(() => {
		if (initialized) return;
		initialized = true;
		themeStore.init();
		langStore.init();
		authStore.init();
	});

	// Reactive auth redirects — run whenever auth state or pathname changes
	$effect(() => {
		const pathname = page.url.pathname;
		const isAuth = authStore.isAuthenticated;
		const needsOnboarding = authStore.requiresOnboarding;

		if (!isAuth && pathname !== '/app/login' && pathname !== '/app/register') {
			goto('/app/login');
		}
		if (isAuth && (pathname === '/app/login' || pathname === '/app/register')) {
			goto(needsOnboarding ? '/app/onboarding' : '/app/dashboard');
		}
		if (isAuth && needsOnboarding && pathname !== '/app/onboarding') {
			goto('/app/onboarding');
		}
		if (isAuth && !needsOnboarding && pathname === '/app/onboarding') {
			goto('/app/dashboard');
		}
	});
</script>

<div class="flex min-h-screen flex-col bg-surface text-on-surface">
	{#if authStore.isAuthenticated}
		<AppNavbar />
	{/if}

	<main id="main-content" class="flex-1">
		{@render children()}
	</main>

	{#if authStore.isAuthenticated}
		<AppFooter />
	{/if}
</div>
