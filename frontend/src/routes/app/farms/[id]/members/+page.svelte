<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { api } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { PageHeader, ListState, ActionButton } from '$lib/components/layout';
	import { Users } from 'lucide-svelte';

	interface Member {
		id: number;
		name: string;
		email: string;
		role: string;
	}

	let farmId = $state(0);
	let members = $state.raw<Member[]>([]);
	let newMemberEmail = $state('');
	let adding = $state(false);
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
		if (idParam && authStore.isAuthenticated && authStore.currentFarmId) {
			farmId = parseInt(idParam, 10);
			loadMembers();
		}
	});

	async function addMember(e: SubmitEvent) {
		e.preventDefault();
		adding = true;
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
			adding = false;
		}
	}

	async function removeMember(userId: number) {
		if (!confirm('Are you sure you want to remove this member?')) return;
		try {
			await api.delete(`/farms/${farmId}/members/${userId}`);
			await loadMembers();
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to remove member';
		}
	}

	const roleStyles: Record<string, string> = {
		owner: 'bg-primary-container/10 text-primary-container',
		manager: 'bg-secondary/10 text-secondary',
		worker: 'bg-surface-container text-on-surface-variant'
	};
</script>

<svelte:head>
	<title>Farm Members – Deshi Fishery</title>
</svelte:head>

<PageHeader
	title="Farm Members"
	subtitle="Manage who can access this farm"
	backHref="/app/farms"
	backLabel="Back to Farms"
>
	<form onsubmit={addMember} class="mb-6 flex flex-col gap-3 sm:flex-row">
		<input
			type="email"
			bind:value={newMemberEmail}
			placeholder="Enter member email"
			required
			class="min-h-[56px] flex-1 rounded-lg border border-outline-variant bg-white px-4 py-3 text-on-surface placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-2 focus:ring-mist-light dark:bg-surface-container"
		/>
		<ActionButton loading={adding} disabled={!newMemberEmail.trim()}>
			Add Member
		</ActionButton>
	</form>

	<ListState
		loading={initialLoading}
		{error}
		empty={!initialLoading && members.length === 0}
		emptyTitle="No members yet"
	>
		{#snippet emptyIcon()}
			<div
				class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-container text-on-surface-variant"
			>
				<Users size={24} aria-hidden="true" />
			</div>
		{/snippet}

		<div class="space-y-3">
			{#each members as member (member.id)}
				<div
					class="flex flex-col justify-between gap-3 rounded-xl border border-outline-variant/30 bg-surface-bright p-4 sm:flex-row sm:items-center"
				>
					<div class="flex items-center gap-3">
						<div
							class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-container/10 text-sm font-semibold text-primary-container"
						>
							{member.name.charAt(0).toUpperCase()}
						</div>
						<div>
							<p class="font-medium text-on-surface">{member.name}</p>
							<p class="text-sm text-on-surface-variant">{member.email}</p>
							<span
								class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {roleStyles[member.role] || roleStyles.worker}"
							>
								{member.role}
							</span>
						</div>
					</div>
					{#if member.role !== 'owner'}
						<button
							onclick={() => removeMember(member.id)}
							class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-error/30 px-4 text-sm font-medium text-error transition-colors hover:bg-error-container"
						>
							Remove
						</button>
					{/if}
				</div>
			{/each}
		</div>
	</ListState>
</PageHeader>
