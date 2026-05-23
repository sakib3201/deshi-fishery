<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		label: string;
		value: string;
		icon?: Snippet;
	}

	let { label, value, icon }: Props = $props();
</script>

<div class="rounded-lg bg-surface-container/50 p-4">
	<p class="mb-1 text-sm text-on-surface-variant">{label}</p>
	<p class="flex items-center gap-1.5 text-xl font-bold text-on-surface">
		{#if icon}
			{@render icon()}
		{/if}
		{value}
	</p>
</div>
