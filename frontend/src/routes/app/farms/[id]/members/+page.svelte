<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { api } from '$lib/api/client';
	import { onMount } from 'svelte';

	let farmId = $state(0);
	let members = $state<Array<{ id: number; name: string; email: string; role: string }>>([]);
	let newMemberEmail = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

	onMount(() => {
		farmId = parseInt($page.params.id);
		loadMembers();
	});

	async function loadMembers() {
		try {
			const response = await api.get(`/farms/${farmId}/members`);
			if (response.success) {
				members = response.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load members';
		} finally {
			initialLoading = false;
		}
	}

	async function addMember(e: Event) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			const response = await api.post(`/farms/${farmId}/members`, { email: newMemberEmail });
			if (response.success) {
				newMemberEmail = '';
				await loadMembers();
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to add member';
		} finally {
			loading = false;
		}
	}

	async function removeMember(userId: number) {
		if (!confirm('Are you sure you want to remove this member?')) {
			return;
		}

		try {
			await api.delete(`/farms/${farmId}/members/${userId}`);
			await loadMembers();
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to remove member';
		}
	}

	function goBack() {
		goto('/app/farms');
	}
</script>

<svelte:head>
	<title>Farm Members — Deshi Fishery</title>
</svelte:head>

<div class="max-w-2xl mx-auto px-4 py-8">
	<button onclick={goBack} class="text-slate-600 hover:text-slate-800 mb-4">← Back to Farms</button>

	<h1 class="text-2xl font-bold mb-6">Farm Members</h1>

	<form onsubmit={addMember} class="flex gap-2 mb-6">
		<input
			type="email"
			bind:value={newMemberEmail}
			placeholder="Enter member email"
			required
			class="flex-1 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
		/>
		<button
			type="submit"
			disabled={loading || !newMemberEmail}
			class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
		>
			{loading ? 'Adding...' : 'Add Member'}
		</button>
	</form>

	{#if error}
		<p class="text-red-600 text-sm mb-4">{error}</p>
	{/if}

	{#if initialLoading}
		<p class="text-slate-600">Loading members...</p>
	{:else if members.length === 0}
		<p class="text-slate-600">No members yet.</p>
	{:else}
		<div class="space-y-2">
			{#each members as member}
				<div class="flex justify-between items-center bg-white border border-slate-200 rounded-lg p-4">
					<div>
						<p class="font-medium">{member.name}</p>
						<p class="text-sm text-slate-600">{member.email}</p>
						<span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded mt-1 inline-block">
							{member.role}
						</span>
					</div>
					{#if member.role !== 'owner'}
						<button
							onclick={() => removeMember(member.id)}
							class="text-red-600 hover:text-red-800 text-sm px-3 py-1 border border-red-200 rounded-md hover:bg-red-50 transition-colors"
						>
							Remove
						</button>
					{/if}
				</div>
			{/each}
		</div>
	{/if}
</div>
