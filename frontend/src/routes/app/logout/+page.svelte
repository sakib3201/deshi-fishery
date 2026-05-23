<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	onMount(() => {
		authStore.logout();
		goto('/app/login');
	});
</script>

<div class="flex min-h-screen items-center justify-center bg-surface">
	<p class="text-on-surface-variant">Logging out...</p>
</div>
