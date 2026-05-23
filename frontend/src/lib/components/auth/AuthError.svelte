<script lang="ts">
	import { AlertCircle } from 'lucide-svelte';

	interface Props {
		message: string;
		id?: string;
	}

	let { message, id = 'form-error' }: Props = $props();
</script>

<div
	{id}
	role="alert"
	aria-live="polite"
	tabindex="-1"
	class="rounded-lg bg-error/10 border border-error/20 p-3 flex items-start gap-2"
>
	<AlertCircle size={16} class="text-error mt-0.5 flex-shrink-0" aria-hidden="true" />
	<span class="text-base font-medium text-error">{message}</span>
</div>
