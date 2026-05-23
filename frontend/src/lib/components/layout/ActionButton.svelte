<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		loading?: boolean;
		disabled?: boolean;
		variant?: 'primary' | 'secondary' | 'danger';
		onclick?: (e: MouseEvent) => void;
		type?: 'submit' | 'button';
		class?: string;
		// Allow data-testid and other HTML button attributes
		[key: string]: unknown;
	}

	let {
		children,
		loading = false,
		disabled = false,
		variant = 'primary',
		onclick,
		type = 'submit',
		class: className = '',
		...rest
	}: Props = $props();

	const base =
		'inline-flex h-14 min-h-[56px] items-center justify-center rounded-lg px-6 font-medium transition-all duration-150 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50';

	const variants = {
		primary: 'bg-primary-container text-white hover:brightness-110',
		secondary: 'border border-outline-variant text-on-surface hover:bg-surface-container',
		danger: 'border border-error/30 text-error hover:bg-error-container'
	};
</script>

<button {type} {disabled} {onclick} class="{base} {variants[variant]} {className}" {...rest}>
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
			<span>Saving...</span>
	{:else}
		{@render children()}
	{/if}
</button>
