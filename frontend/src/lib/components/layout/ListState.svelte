<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		loading?: boolean;
		error?: string;
		empty?: boolean;
		emptyIcon?: Snippet;
		emptyTitle?: string;
		emptyDescription?: string;
		emptyAction?: Snippet;
	}

	let {
		children,
		loading = false,
		error = '',
		empty = false,
		emptyIcon,
		emptyTitle = 'No items yet',
		emptyDescription = '',
		emptyAction
	}: Props = $props();
</script>

{#if error}
	<div
		class="mb-6 rounded-xl border border-error/20 bg-error-container p-4"
		role="alert"
		aria-live="polite"
	>
		<p class="font-medium text-error">{error}</p>
	</div>
{/if}

{#if loading}
	<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
		{#each [1, 2, 3] as _}
			<div class="animate-pulse rounded-xl border border-outline-variant/20 bg-surface-bright p-6">
				<div class="mb-4 flex items-center gap-3">
					<div class="h-11 w-11 rounded-xl bg-surface-container"></div>
					<div class="h-5 w-3/4 rounded bg-surface-container"></div>
				</div>
				<div class="mb-4 h-4 w-1/2 rounded bg-surface-container"></div>
				<div class="flex gap-2">
					<div class="h-10 w-16 rounded bg-surface-container"></div>
					<div class="h-10 w-16 rounded bg-surface-container"></div>
				</div>
			</div>
		{/each}
	</div>
{:else if empty}
	<div class="rounded-xl border border-outline-variant/30 bg-surface-bright p-10 text-center">
		<div class="mb-4 flex justify-center">
			{#if emptyIcon}
				{@render emptyIcon()}
			{:else}
				<div
					class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant"
				>
					?
				</div>
			{/if}
		</div>
		<h2 class="mb-2 text-xl font-bold text-on-surface">{emptyTitle}</h2>
		{#if emptyDescription}
			<p class="mx-auto mb-6 max-w-md text-on-surface-variant">{emptyDescription}</p>
		{/if}
		{#if emptyAction}
			{@render emptyAction()}
		{/if}
	</div>
{:else}
	{@render children()}
{/if}
