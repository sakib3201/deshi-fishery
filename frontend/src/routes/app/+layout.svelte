<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { authStore } from '$lib/stores/auth.svelte';
	import { onMount } from 'svelte';

	let { children } = $props();

	onMount(() => {
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

<div class="min-h-screen bg-surface text-on-surface dark:bg-surface-dark dark:text-on-background">
	{@render children()}
</div>
