<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { sales } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import { PageHeader, DataCard } from '$lib/components/layout';
	import { Calendar, Fish, Scale, Loader2, Trash2, Receipt, User } from 'lucide-svelte';

	interface Sale {
		id: number;
		pond_id: number;
		sale_code: string;
		sale_type: 'wholesale' | 'retail';
		date: string;
		fish_type: string;
		avg_fish_weight_g: number;
		quantity_kg: number;
		rate_per_kg: number;
		total_amount: number;
		customer_name: string | null;
		custom_tags: string[] | null;
		payment_status: 'pending' | 'partial' | 'paid';
		amount_paid: number;
		amount_due: number;
		notes: string | null;
		created_at: string;
		pond?: { pond_number: string };
	}

	let sale = $state.raw<Sale | null>(null);
	let loading = $state(true);
	let error = $state('');
	let deleteLoading = $state(false);

	async function loadSale() {
		const idParam = page.params.id;
		if (!idParam) {
			error = 'Invalid sale ID';
			loading = false;
			return;
		}
		const id = parseInt(idParam, 10);
		if (!id) {
			error = 'Invalid sale ID';
			loading = false;
			return;
		}
		try {
			const response = await sales.get(id);
			if (response.success) {
				sale = response.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load sale';
		} finally {
			loading = false;
		}
	}

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadSale();
		}
	});

	async function handleDelete() {
		if (!sale) return;
		if (
			!confirm(
				`Delete sale ${sale.sale_code}? This will NOT restore pond stock. Use Stock Adjustment if needed.`
			)
		) {
			return;
		}
		deleteLoading = true;
		try {
			await sales.delete(sale.id);
			goto('/app/sales');
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to delete sale';
			deleteLoading = false;
		}
	}

	function formatDate(dateStr: string): string {
		return formatDt(new Date(dateStr), langStore.lang);
	}

	function formatNumber(num: number): string {
		return formatNum(num, langStore.lang);
	}

	function formatCurrency(num: number): string {
		return '\u09F3' + formatNumber(num);
	}

	function paymentStatusBadge(status: string): string {
		switch (status) {
			case 'paid':
				return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
			case 'partial':
				return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
			default:
				return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
		}
	}

	function paymentStatusLabel(status: string): string {
		switch (status) {
			case 'paid':
				return 'Paid';
			case 'partial':
				return 'Partial';
			default:
				return 'Pending';
		}
	}
</script>

<svelte:head>
	<title>{sale ? `${sale.sale_code}` : 'Sale'} – Deshi Fishery</title>
</svelte:head>

<PageHeader title={sale ? sale.sale_code : 'Sale'} backHref="/app/sales" backLabel="Back to Sales">
	{#if loading}
		<div class="animate-pulse rounded-xl border border-outline-variant/30 bg-surface-bright p-8">
			<div class="mb-4 h-8 w-1/2 rounded bg-surface-container"></div>
			<div class="mb-6 h-4 w-1/3 rounded bg-surface-container"></div>
			<div class="space-y-3">
				<div class="h-4 w-full rounded bg-surface-container"></div>
				<div class="h-4 w-3/4 rounded bg-surface-container"></div>
				<div class="h-4 w-1/2 rounded bg-surface-container"></div>
			</div>
		</div>
	{:else if error}
		<div class="rounded-xl border border-error/20 bg-error-container p-4" role="alert">
			<p class="font-medium text-error">{error}</p>
			<a
				href="/app/sales"
				class="mt-3 inline-flex h-11 items-center justify-center rounded-lg border border-outline-variant px-4 font-medium text-on-surface transition-colors hover:bg-surface-container"
			>
				Go Back
			</a>
		</div>
	{:else if sale}
		<div class="rounded-xl border border-outline-variant/30 bg-surface-bright p-6 sm:p-8">
			<div class="mb-6 flex items-start justify-between">
				<div>
					<div class="mb-2 flex items-center gap-2">
						<div
							class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container/10 text-primary-container"
						>
							<Receipt size={20} aria-hidden="true" />
						</div>
						<div>
							<h1 class="text-2xl font-bold text-on-surface">{sale.sale_code}</h1>
							<span
								class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {paymentStatusBadge(
									sale.payment_status
								)}"
							>
								{paymentStatusLabel(sale.payment_status)}
							</span>
						</div>
					</div>
					<p class="text-on-surface-variant">
						{sale.sale_type === 'wholesale' ? 'Wholesale' : 'Retail'} sale from {sale.pond
							?.pond_number || `Pond #${sale.pond_id}`}
					</p>
				</div>
				<button
					onclick={handleDelete}
					disabled={deleteLoading}
					class="inline-flex h-11 items-center justify-center gap-1.5 rounded-lg border border-error/30 px-4 text-sm font-medium text-error transition-colors hover:bg-error-container disabled:opacity-50"
				>
					{#if deleteLoading}
						<Loader2 size={16} class="animate-spin" aria-hidden="true" />
					{:else}
						<Trash2 size={16} aria-hidden="true" />
					{/if}
					Delete
				</button>
			</div>

			<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
				<DataCard label="Fish Type" value={sale.fish_type}>
					{#snippet icon()}<Fish size={18} aria-hidden="true" />{/snippet}
				</DataCard>
				<DataCard label="Avg Weight" value={`${sale.avg_fish_weight_g} g`}>
					{#snippet icon()}<Scale size={18} aria-hidden="true" />{/snippet}
				</DataCard>
				<DataCard label="Quantity" value={`${formatNumber(sale.quantity_kg)} kg`} />
				<DataCard label="Rate per kg" value={formatCurrency(sale.rate_per_kg)} />
				<DataCard label="Total Amount" value={formatCurrency(sale.total_amount)} />
				<DataCard label="Amount Paid" value={formatCurrency(sale.amount_paid)} />
				<DataCard label="Amount Due" value={formatCurrency(sale.amount_due)} />
				<DataCard label="Sale Date" value={formatDate(sale.date)}>
					{#snippet icon()}<Calendar size={18} aria-hidden="true" />{/snippet}
				</DataCard>
			</div>

			{#if sale.customer_name}
				<div class="mb-4 border-t border-outline-variant/20 pt-4">
					<p class="mb-1 text-sm text-on-surface-variant">Customer</p>
					<p class="flex items-center gap-1.5 text-lg font-medium text-on-surface">
						<User size={18} aria-hidden="true" />
						{sale.customer_name}
					</p>
				</div>
			{/if}

			{#if sale.notes}
				<div class="border-t border-outline-variant/20 pt-4">
					<p class="mb-1 text-sm text-on-surface-variant">Notes</p>
					<p class="whitespace-pre-wrap text-on-surface">{sale.notes}</p>
				</div>
			{/if}

			<div class="mt-4 border-t border-outline-variant/20 pt-4">
				<p class="text-xs text-on-surface-variant">
					Recorded on {formatDt(new Date(sale.created_at), langStore.lang)}
				</p>
			</div>
		</div>
	{/if}
</PageHeader>
