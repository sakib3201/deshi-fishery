<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';
	import { ArrowLeft, Users, Loader2 } from 'lucide-svelte';

	interface Member {
		id: number;
		name: string;
		email: string;
		role: string;
	}

	let farmId = $state(0);
	let members = $state<Member[]>([]);
	let newMemberEmail = $state('');
	let loading = $state(false);
	let error = $state('');
	let initialLoading = $state(true);

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

	$effect(() => {
		const idParam = page.params.id;
		if (idParam) {
			farmId = parseInt(idParam, 10);
			loadMembers();
		}
	});

	async function addMember(e: SubmitEvent) {
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

	const roleStyles: Record<string, string> = {
		owner: 'bg-primary-container/10 text-primary-container',
		manager: 'bg-secondary/10 text-secondary',
		worker: 'bg-surface-container text-on-surface-variant',
	};
</script>

<svelte:head>
	<title>Farm Members – Deshi Fishery</title>
</svelte:head>

<div class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
	<button
		onclick={goBack}
		class="inline-flex items-center gap-1.5 text-sm font-medium text-on-surface-variant hover:text-on-surface transition-colors mb-6 min-h-[44px]"
	>
		<ArrowLeft size={18} aria-hidden="true" />
		Back to Farms
	</button>

	<div class="flex items-center gap-3 mb-6">
		<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container/10 text-primary-container">
			<Users size={20} aria-hidden="true" />
		</div>
		<div>
			<h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Farm Members</h1>
			<p class="text-sm text-on-surface-variant">Manage who can access this farm</p>
		</div>
	</div>

	<form onsubmit={addMember} class="flex flex-col sm:flex-row gap-3 mb-6">
		<input
			type="email"
			bind:value={newMemberEmail}
			placeholder="Enter member email"
			required
			class="flex-1 px-4 py-3 min-h-[56px] bg-white dark:bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-secondary focus:ring-2 focus:ring-mist-light text-on-surface placeholder:text-on-surface-variant/50"
		/>
		<button
			type="submit"
			disabled={loading || !newMemberEmail.trim()}
			class="inline-flex items-center justify-center h-14 px-6 rounded-lg bg-primary-container text-white font-medium hover:brightness-110 transition-all duration-150 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed min-h-[56px]"
		>
			{loading ? 'Adding...' : 'Add Member'}
		</button>
	</form>

	{#if error}
		<div class="mb-4 rounded-lg bg-error-container border border-error/20 p-3" role="alert">
			<p class="text-error text-sm font-medium">{error}</p>
		</div>
	{/if}

	{#if initialLoading}
		<div class="flex items-center gap-2 text-on-surface-variant py-8">
			<div class="h-5 w-5 border-2 border-on-surface-variant/30 border-t-primary-container rounded-full animate-spin"></div>
			Loading members...
		</div>
	{:else if members.length === 0}
		<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-10 text-center">
			<div class="flex justify-center mb-3">
				<div class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-container text-on-surface-variant">
					<Users size={24} aria-hidden="true" />
				</div>
			</div>
			<p class="text-on-surface-variant">No members yet.</p>
		</div>
	{:else}
		<div class="space-y-3">
			{#each members as member (member.id)}
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-bright border border-outline-variant/30 rounded-xl p-4">
					<div class="flex items-center gap-3">
						<div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-container/10 text-primary-container font-semibold text-sm shrink-0">
							{member.name.charAt(0).toUpperCase()}
						</div>
						<div>
							<p class="font-medium text-on-surface">{member.name}</p>
							<p class="text-sm text-on-surface-variant">{member.email}</p>
							<span class="text-xs font-medium px-2.5 py-0.5 rounded-full capitalize mt-1 inline-block {roleStyles[member.role] || roleStyles.worker}">
								{member.role}
							</span>
						</div>
					</div>
					{#if member.role !== 'owner'}
						<button
							onclick={() => removeMember(member.id)}
							class="inline-flex items-center justify-center h-11 px-4 rounded-lg border border-error/30 text-sm font-medium text-error hover:bg-error-container transition-colors min-h-[44px]"
						>
							Remove
						</button>
					{/if}
				</div>
			{/each}
		</div>
	{/if}
</div>
