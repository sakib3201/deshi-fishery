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
				goto('/app/dashboard');
			}
		});
	});
</script>

<div class="min-h-screen bg-surface text-slate-900">
	{@render children()}
</div>
