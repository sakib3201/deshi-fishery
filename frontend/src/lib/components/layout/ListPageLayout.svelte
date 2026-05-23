<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		title: string;
		description?: string;
		action?: Snippet;
	}

	let { children, title, description, action }: Props = $props();
</script>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10">
	<!-- Header -->
	<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-on-surface sm:text-3xl">{title}</h1>
			{#if description}
				<p class="mt-1 text-on-surface-variant">{description}</p>
			{/if}
		</div>
		{#if action}
			<div>{@render action()}</div>
		{/if}
	</div>

	{@render children()}
</div>
