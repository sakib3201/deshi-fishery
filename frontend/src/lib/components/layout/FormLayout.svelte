<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		loading?: boolean;
		error?: string;
		handleSubmit: (e: SubmitEvent) => void | Promise<void>;
		backHref: string;
		backLabel: string;
		submitLabel: string;
		submitDisabled?: boolean;
		class?: string;
	}

	let {
		children,
		loading = false,
		error = '',
		handleSubmit,
		backHref,
		backLabel,
		submitLabel,
		submitDisabled = false,
		class: className = ''
	}: Props = $props();
</script>

<form
	class="space-y-5 rounded-xl border border-outline-variant/30 bg-surface-bright p-6 sm:p-8 {className}"
	onsubmit={(e: SubmitEvent) => { e.preventDefault(); handleSubmit(e); }}
>
	{@render children()}

	{#if error}
		<div class="rounded-lg border border-error/20 bg-error-container p-3" role="alert">
			<p class="text-sm font-medium text-error">{error}</p>
		</div>
	{/if}

	<div class="flex flex-col gap-3 pt-2 sm:flex-row">
		<a
			href={backHref}
			class="inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg border border-outline-variant px-6 font-medium text-on-surface transition-colors hover:bg-surface-container"
		>
			{backLabel}
		</a>
		<button
			type="submit"
			disabled={loading || submitDisabled}
			class="inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg bg-primary-container px-6 font-medium text-white transition-all duration-150 hover:brightness-110 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
		>
			{#if loading}
				<svg
					class="mr-2 h-5 w-5 animate-spin"
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 24 24"
					aria-hidden="true"
				>
					<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
					<path
						class="opacity-75"
						fill="currentColor"
						d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
					/>
				</svg>
					Saving...
				{:else}
					{submitLabel}
				{/if}
			</button>
		</div>
</form>
