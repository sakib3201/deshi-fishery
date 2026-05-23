<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		children: Snippet;
		title: string;
		subtitle: string;
	}

	let { children, title, subtitle }: Props = $props();
</script>

<a
	href="#main-content"
	class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white
         focus:text-primary-container focus:p-4 focus:rounded-lg"
>
	Skip to form
</a>

<div class="flex min-h-[100dvh] items-start justify-center bg-surface px-4 pt-6 pb-safe">
	<main id="main-content" class="w-full max-w-md space-y-6 rounded-2xl bg-white p-6 sm:p-8 shadow-lg">
		<div class="text-center">
			<h1 class="text-2xl font-bold text-primary-container">{title}</h1>
			<p class="mt-2 text-on-surface-variant">{subtitle}</p>
		</div>

		{@render children()}
	</main>
</div>
