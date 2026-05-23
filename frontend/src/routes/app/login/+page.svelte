<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';
	import { AuthCard, AuthForm, AuthInput, AuthButton, AuthError } from '$lib/components/auth';

	onMount(() => {
		if (authStore.isAuthenticated) {
			if (authStore.requiresOnboarding) {
				goto('/app/onboarding');
			} else {
				goto('/app/dashboard');
			}
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		if (authStore.loading) return;
		const form = e.target as HTMLFormElement;
		const formData = new FormData(form);
		try {
			await authStore.login(
				formData.get('email') as string,
				formData.get('password') as string
			);
			if (authStore.requiresOnboarding) {
				await goto('/app/onboarding');
			} else {
				await goto('/app/dashboard');
			}
		} catch {
			// Error handled by store
		}
	}
</script>

<svelte:head>
	<title>Login - Deshi Fishery</title>
</svelte:head>

<AuthCard title="Welcome Back" subtitle="Sign in to your Deshi Fishery account">
	<AuthForm loading={authStore.loading} onsubmit={handleSubmit}>
		<AuthInput
			id="email"
			name="email"
			label="Email"
			type="email"
			required
			autocomplete="email"
			autocapitalize="off"
			autocorrect="off"
			inputmode="email"
			disabled={authStore.loading}
			oninput={() => { authStore.error = null; }}
			placeholder="you@example.com"
			error={authStore.error}
		/>

		<AuthInput
			id="password"
			name="password"
			label="Password"
			required
			autocomplete="current-password"
			disabled={authStore.loading}
			oninput={() => { authStore.error = null; }}
			placeholder="••••••••"
			error={authStore.error}
		/>

		{#if authStore.error}
			<AuthError message={authStore.error} />
		{/if}

		<AuthButton loading={authStore.loading}>
			{#if authStore.loading}
				Signing in...
			{:else}
				Sign In
			{/if}
		</AuthButton>

		<p class="text-center text-sm">
			<a href="/app/forgot-password" class="font-medium text-primary hover:underline active:scale-[0.98] transition-transform">
				Forgot password?
			</a>
		</p>
	</AuthForm>

	<p class="text-center text-sm text-on-surface-variant">
		Don't have an account?
		<a href="/app/register" class="font-medium text-primary hover:underline active:scale-[0.98] transition-transform">Register</a>
	</p>
</AuthCard>
