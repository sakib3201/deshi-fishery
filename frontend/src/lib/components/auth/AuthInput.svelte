<script lang="ts">
	import { Eye, EyeOff } from 'lucide-svelte';

	interface Props {
		id: string;
		name: string;
		label: string;
		type?: 'text' | 'email' | 'password';
		value?: string;
		placeholder?: string;
		required?: boolean;
		autocomplete?: HTMLInputElement['autocomplete'];
		disabled?: boolean;
		autocapitalize?: 'off' | 'none' | 'on' | 'sentences' | 'words' | 'characters';
		autocorrect?: 'on' | 'off';
		inputmode?: 'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search';
		error?: string | null;
		oninput?: (e: Event) => void;
		onchange?: (e: Event) => void;
	}

	let {
		id,
		name,
		label,
		type = undefined,
		value = $bindable(''),
		placeholder = '',
		required = false,
		autocomplete = 'off',
		disabled = false,
		autocapitalize = undefined,
		autocorrect = undefined,
		inputmode = undefined,
		error = null,
		oninput,
		onchange
	}: Props = $props();

	let showPassword = $state(false);
	const isPassword = $derived(name === 'password' || name === 'password_confirmation' || name.includes('password'));
</script>

<div>
	<label for={id} class="mb-2 block text-sm font-medium text-on-surface">{label}</label>
	<div class={isPassword ? 'relative' : ''}>
		<input
			{id}
			{name}
			type={type ?? (isPassword ? (showPassword ? 'text' : 'password') : (name === 'email' ? 'email' : 'text'))}
			{required}
			{autocomplete}
			{disabled}
			autocapitalize={autocapitalize ?? undefined}
			autocorrect={autocorrect ?? undefined}
			inputmode={inputmode ?? undefined}
			aria-describedby={error ? 'form-error' : undefined}
			bind:value
			{oninput}
			{onchange}
			class="h-14 w-full rounded-lg border border-outline-variant px-4 text-base bg-white dark:bg-surface-container text-on-surface placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-50 {isPassword ? 'pr-12' : ''}"
			{placeholder}
		/>
		{#if isPassword}
			<button
				type="button"
				onclick={() => showPassword = !showPassword}
				class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-on-surface-variant hover:text-on-surface transition-colors"
				aria-label={showPassword ? 'Hide password' : 'Show password'}
				aria-pressed={showPassword}
				tabindex="-1"
			>
				{#if showPassword}
					<EyeOff size={20} aria-hidden="true" />
				{:else}
					<Eye size={20} aria-hidden="true" />
				{/if}
			</button>
		{/if}
	</div>
</div>
