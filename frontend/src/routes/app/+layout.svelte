<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { authStore } from '$lib/stores/auth.svelte';
	import { onMount } from 'svelte';
	import { AppNavbar, AppFooter } from '$lib/components/layout';
	import { themeStore, langStore } from '$lib/stores/ui.svelte';

	let { children } = $props();

	onMount(() => {
		themeStore.init();
		langStore.init();
		authStore.init().then(() => {
			// Redirect unauthenticated users to login
			if (!authStore.isAuthenticated && $page.url.pathname !== '/app/login' && $page.url.pathname !== '/app/register') {
				goto('/app/login');
			}
			// Redirect authenticated users away from login/register
			if (authStore.isAuthenticated && ($page.url.pathname === '/app/login' || $page.url.pathname === '/app/register')) {
				if (authStore.requiresOnboarding) {
					goto('/app/onboarding');
				} else {
					goto('/app/dashboard');
				}
			}
			// Redirect to onboarding if required
			if (authStore.isAuthenticated && authStore.requiresOnboarding && $page.url.pathname !== '/app/onboarding') {
				goto('/app/onboarding');
			}
			// Redirect away from onboarding if not required
			if (authStore.isAuthenticated && !authStore.requiresOnboarding && $page.url.pathname === '/app/onboarding') {
				goto('/app/dashboard');
			}
		});
	});
</script>

<div class="flex min-h-screen flex-col bg-surface text-on-surface dark:bg-surface-dark dark:text-on-background">
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
