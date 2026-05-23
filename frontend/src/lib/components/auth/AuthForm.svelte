<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		loading?: boolean;
		onsubmit?: (e: SubmitEvent) => void | Promise<void>;
	}

	let { children, loading = false, onsubmit }: Props = $props();
</script>

<form
	aria-busy={loading}
	class="space-y-4"
	{onsubmit}
>
	{@render children()}
</form>
