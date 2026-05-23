<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { AuthCard, AuthForm, AuthInput, AuthButton, AuthError } from '$lib/components/auth';

	// Redirect authenticated users away from register
	$effect(() => {
		if (authStore.isAuthenticated) {
			goto('/app/dashboard');
		}
	});

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		if (authStore.loading) return;
		const form = e.target as HTMLFormElement;
		const formData = new FormData(form);
		try {
			await authStore.register(
				formData.get('name') as string,
				formData.get('email') as string,
				formData.get('password') as string,
				formData.get('password_confirmation') as string
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

	function clearError() {
		authStore.error = null;
	}
</script>

<svelte:head>
	<title>Register - Deshi Fishery</title>
</svelte:head>

<AuthCard title="Create Account" subtitle="Join Deshi Fishery today">
	<AuthForm loading={authStore.loading} onsubmit={handleSubmit}>
		<AuthInput
			id="name"
			name="name"
			label="Name"
			required
			autocomplete="name"
			disabled={authStore.loading}
			oninput={clearError}
			placeholder="Your name"
			error={authStore.error}
		/>

		<AuthInput
			id="email"
			name="email"
			label="Email"
			required
			autocomplete="email"
			autocapitalize="off"
			autocorrect="off"
			inputmode="email"
			disabled={authStore.loading}
			oninput={clearError}
			placeholder="you@example.com"
			error={authStore.error}
		/>

		<AuthInput
			id="password"
			name="password"
			label="Password"
			required
			autocomplete="new-password"
			disabled={authStore.loading}
			oninput={clearError}
			placeholder="••••••••"
			error={authStore.error}
		/>

		<AuthInput
			id="password_confirmation"
			name="password_confirmation"
			label="Confirm Password"
			required
			autocomplete="new-password"
			disabled={authStore.loading}
			oninput={clearError}
			placeholder="••••••••"
			error={authStore.error}
		/>

		{#if authStore.error}
			<AuthError message={authStore.error} />
		{/if}

		<AuthButton loading={authStore.loading}>
			{#if authStore.loading}
				Creating account...
			{:else}
				Create Account
			{/if}
		</AuthButton>
	</AuthForm>

	<p class="text-center text-sm text-on-surface-variant">
		Already have an account?
		<a href="/app/login" class="font-medium text-primary hover:underline active:scale-[0.98] transition-transform">Sign in</a>
	</p>
</AuthCard>
