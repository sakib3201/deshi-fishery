<script lang="ts">
	import '../app.css';
	import { page } from '$app/stores';

	let { children } = $props();
</script>

<svelte:head>
	<title>Deshi Fishery</title>
	<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
</svelte:head>

<div class="min-h-screen bg-surface text-on-surface">
	{@render children()}
</div>
