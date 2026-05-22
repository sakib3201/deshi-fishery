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
	<title>Register - Deshi Fishery</title>
</svelte:head>

<div class="flex min-h-screen items-center justify-center bg-surface px-4">
	<div class="w-full max-w-md space-y-6 rounded-2xl bg-white p-8 shadow-lg">
		<div class="text-center">
			<h1 class="text-2xl font-bold text-primary-container">Create Account</h1>
			<p class="mt-2 text-on-surface-variant">Join Deshi Fishery today</p>
		</div>

		<form
			class="space-y-4"
			onsubmit={async (e) => {
				e.preventDefault();
				const form = e.target as HTMLFormElement;
				const formData = new FormData(form);
			try {
				await authStore.register(
					formData.get('name') as string,
					formData.get('email') as string,
					formData.get('password') as string,
					formData.get('password_confirmation') as string
				);
				authStore.clearAuth();
				await goto('/app/login');
			} catch (err) {
				// Error handled by store
			}
			}}
		>
			<div>
				<label for="name" class="mb-1 block text-sm font-medium text-on-surface">Name</label>
				<input
					id="name"
					name="name"
					type="text"
					required
					class="h-14 w-full rounded-lg border border-outline-variant px-4 text-base focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
					placeholder="Your name"
				/>
			</div>

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

			<div>
				<label for="password_confirmation" class="mb-1 block text-sm font-medium text-on-surface">Confirm Password</label>
				<input
					id="password_confirmation"
					name="password_confirmation"
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
				{authStore.loading ? 'Creating account...' : 'Create Account'}
			</button>
		</form>

		<p class="text-center text-sm text-on-surface-variant">
			Already have an account?
			<a href="/app/login" class="font-medium text-primary hover:underline">Sign in</a>
		</p>
	</div>
</div>
