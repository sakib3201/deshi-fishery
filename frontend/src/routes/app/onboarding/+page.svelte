<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';
	import { api } from '$lib/api/client';
	import { AuthCard, AuthForm, AuthInput, AuthButton, AuthError } from '$lib/components/auth';

	let name = $state('');
	let location = $state('');
	let loading = $state(false);
	let error = $state('');

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		if (loading) return;
		loading = true;
		error = '';

		try {
			const response = await api.post('/farms', { name, location });
			if (response.success) {
				// Update user data after farm creation
				await authStore.init();
				goto('/app/dashboard');
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to create farm';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Create Your Farm — Deshi Fishery</title>
</svelte:head>

<AuthCard title="Welcome to Deshi Fishery" subtitle="Create your first farm to get started">
	<AuthForm loading={loading} onsubmit={handleSubmit}>
		<AuthInput
			id="name"
			name="name"
			label="Farm Name *"
			required
			autocomplete="organization"
			disabled={loading}
			oninput={() => { error = ''; }}
			placeholder="e.g., My Fish Farm"
			error={error || null}
			bind:value={name}
		/>

		<AuthInput
			id="location"
			name="location"
			label="Location"
			autocomplete="address-level1"
			disabled={loading}
			oninput={() => { error = ''; }}
			placeholder="e.g., Rajshahi"
			error={error || null}
			bind:value={location}
		/>

		{#if error}
			<AuthError message={error} />
		{/if}

		<AuthButton loading={loading} disabled={!name}>
			{#if loading}
				Creating...
			{:else}
				Create Farm
			{/if}
		</AuthButton>
	</AuthForm>
</AuthCard>
