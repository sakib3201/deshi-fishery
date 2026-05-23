<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		title: string;
		subtitle?: string;
		backHref: string;
		backLabel: string;
	}

	let { children, title, subtitle, backHref, backLabel }: Props = $props();
</script>

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<a
		href={backHref}
		class="mb-6 inline-flex min-h-[44px] items-center gap-1.5 text-sm font-medium text-on-surface-variant transition-colors hover:text-on-surface"
	>
		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
		{backLabel}
	</a>

	<h1 class="mb-6 text-2xl font-bold text-on-surface sm:text-3xl">{title}</h1>
	{#if subtitle}
		<p class="mb-6 text-on-surface-variant">{subtitle}</p>
	{/if}

	{@render children()}
</div>
