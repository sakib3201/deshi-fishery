<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	onMount(() => {
		if (authStore.isAuthenticated) {
			goto('/app/dashboard');
		}
	});
</script>

<svelte:head>
	<title>Login - Deshi Fishery</title>
</svelte:head>

<div class="flex min-h-screen items-center justify-center bg-surface px-4">
	<div class="w-full max-w-md space-y-6 rounded-2xl bg-white p-8 shadow-lg">
		<div class="text-center">
			<h1 class="text-2xl font-bold text-primary-container">Welcome Back</h1>
			<p class="mt-2 text-on-surface-variant">Sign in to your Deshi Fishery account</p>
		</div>

		<form
			class="space-y-4"
			onsubmit={async (e) => {
				e.preventDefault();
				const form = e.target as HTMLFormElement;
				const formData = new FormData(form);
			try {
				await authStore.login(
					formData.get('email') as string,
					formData.get('password') as string
				);
				await goto('/app/dashboard');
			} catch (err) {
				// Error handled by store
			}
			}}
		>
			<div>
				<label for="email" class="mb-1 block text-sm font-medium text-on-surface">Email</label>
				<input
					id="email"
					name="email"
					type="email"
					required
					class="h-14 w-full rounded-lg border border-outline-variant px-4 text-base focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
					placeholder="you@example.com"
				/>
			</div>

			<div>
				<label for="password" class="mb-1 block text-sm font-medium text-on-surface">Password</label>
				<input
					id="password"
					name="password"
					type="password"
					required
					class="h-14 w-full rounded-lg border border-outline-variant px-4 text-base focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
					placeholder="••••••••"
				/>
			</div>

			{#if authStore.error}
				<div class="rounded-lg bg-error/10 p-3 text-sm text-error">
					{authStore.error}
				</div>
			{/if}

			<button
				type="submit"
				disabled={authStore.loading}
				class="h-14 w-full rounded-lg bg-primary-container font-bold text-white transition-all hover:brightness-110 disabled:opacity-50"
			>
				{authStore.loading ? 'Signing in...' : 'Sign In'}
			</button>
		</form>

		<p class="text-center text-sm text-on-surface-variant">
			Don't have an account?
			<a href="/app/register" class="font-medium text-primary hover:underline">Register</a>
		</p>
	</div>
</div>
