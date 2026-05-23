<script lang="ts">
	import { Loader2 } from 'lucide-svelte';

	interface Props {
		loading?: boolean;
		children?: import('svelte').Snippet;
		type?: 'submit' | 'button';
		disabled?: boolean;
		variant?: 'primary' | 'secondary' | 'danger';
		onclick?: (e: MouseEvent) => void;
	}

	let {
		loading = false,
		children,
		type = 'submit',
		disabled = false,
		variant = 'primary',
		onclick,
		...rest
	}: Props & Record<string, unknown> = $props();

	const variantClasses = {
		primary: 'bg-primary-container text-white hover:brightness-110',
		secondary: 'bg-surface-container text-on-surface hover:bg-surface-container-high',
		danger: 'bg-error text-white hover:brightness-110'
	};
</script>

<button
	{type}
	{disabled}
	{onclick}
	{...rest}
	class="h-14 w-full rounded-lg font-bold transition-[colors,transform] duration-150 active:scale-[0.98] disabled:opacity-60 flex items-center justify-center gap-2 {variantClasses[variant]}"
>
	{#if loading}
		<Loader2 size={20} class="animate-spin" aria-hidden="true" />
	{/if}
	{#if children}
		{@render children()}
	{/if}
</button>
