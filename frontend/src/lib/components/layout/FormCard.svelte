<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		handleSubmit: (e: SubmitEvent) => void | Promise<void>;
		class?: string;
	}

	let { children, handleSubmit, class: className = '' }: Props = $props();
</script>

<form
	class="space-y-5 rounded-xl border border-outline-variant/30 bg-surface-bright p-6 sm:p-8 {className}"
	onsubmit={(e: SubmitEvent) => { e.preventDefault(); handleSubmit(e); }}
>
	{@render children()}
</form>
