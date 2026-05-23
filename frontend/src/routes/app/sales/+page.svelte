<script lang="ts">
	import { goto } from '$app/navigation';
	import { sales } from '$lib/api/client';
	import { authStore } from '$lib/stores/auth.svelte';
	import { langStore } from '$lib/stores/ui.svelte';
	import { formatNumber as formatNum, formatDate as formatDt } from '$lib/utils/formatters';
	import { ListPageLayout, ListState, ActionButton } from '$lib/components/layout';
	import { Receipt, Calendar, ArrowRight, Filter } from 'lucide-svelte';

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
		payment_status: 'pending' | 'partial' | 'paid';
		pond?: { pond_number: string };
	}

	let saleList = $state.raw<Sale[]>([]);
	let loading = $state(true);
	let error = $state('');
	let filterSaleType = $state('');
	let filterPaymentStatus = $state('');

	async function loadSales() {
		try {
			const params: Record<string, string> = {};
			if (filterSaleType) params.sale_type = filterSaleType;
			if (filterPaymentStatus) params.payment_status = filterPaymentStatus;

			const response = await sales.list(params);
			if (response.success) {
				saleList = response.data.data;
			}
		} catch (err) {
			error = err instanceof Error ? err.message : 'Failed to load sales';
		} finally {
			loading = false;
		}
	}

	$effect(() => {
		if (authStore.isAuthenticated && authStore.currentFarmId) {
			loadSales();
		}
	});

	function navigateToNew() {
		goto('/app/sales/new');
	}

	function navigateToDetail(id: number) {
		goto(`/app/sales/${id}`);
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
	<title>Sales – Deshi Fishery</title>
</svelte:head>

<ListPageLayout
	title="Sales"
	description="Track fish sales and payments"
>
	{#snippet action()}
		<ActionButton onclick={navigateToNew}>
			<Receipt size={20} aria-hidden="true" />
			Record Sale
		</ActionButton>
	{/snippet}

	<!-- Filters -->
	<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
		<div class="flex items-center gap-2">
			<Filter size={16} class="text-on-surface-variant" />
			<span class="text-sm font-medium text-on-surface-variant">Filters:</span>
		</div>
		<select
			bind:value={filterSaleType}
			onchange={loadSales}
			class="min-h-[44px] rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm text-on-surface dark:bg-surface-container"
		>
			<option value="">All Types</option>
			<option value="wholesale">Wholesale</option>
			<option value="retail">Retail</option>
		</select>
		<select
			bind:value={filterPaymentStatus}
			onchange={loadSales}
			class="min-h-[44px] rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm text-on-surface dark:bg-surface-container"
		>
			<option value="">All Payments</option>
			<option value="pending">Pending</option>
			<option value="partial">Partial</option>
			<option value="paid">Paid</option>
		</select>
	</div>

	<ListState
		{loading}
		{error}
		empty={!loading && saleList.length === 0}
		emptyTitle="No sales yet"
		emptyDescription="Record your first sale to start tracking revenue and payment status."
	>
		{#snippet emptyIcon()}
			<div
				class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-on-surface-variant"
			>
				<Receipt size={28} aria-hidden="true" />
			</div>
		{/snippet}
		{#snippet emptyAction()}
			<ActionButton onclick={navigateToNew}>
				<Receipt size={20} aria-hidden="true" />
				Record First Sale
			</ActionButton>
		{/snippet}

		<div class="overflow-hidden rounded-xl border border-outline-variant/30 bg-surface-bright">
			<table class="w-full text-left">
				<thead class="border-b border-outline-variant/20 bg-surface-container/50">
					<tr>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Code</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Date</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Customer</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Fish Type</th>
						<th class="px-4 py-3 text-right text-sm font-semibold text-on-surface-variant">Qty (kg)</th>
						<th class="px-4 py-3 text-right text-sm font-semibold text-on-surface-variant">Total</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant">Status</th>
						<th class="px-4 py-3 text-sm font-semibold text-on-surface-variant"></th>
					</tr>
				</thead>
				<tbody>
					{#each saleList as sale (sale.id)}
						<tr class="border-b border-outline-variant/10 transition-colors hover:bg-surface-container/30">
							<td class="px-4 py-3 text-sm font-medium text-on-surface">{sale.sale_code}</td>
							<td class="px-4 py-3 text-sm text-on-surface-variant">
								<div class="flex items-center gap-1.5">
									<Calendar size={14} aria-hidden="true" />
									{formatDate(sale.date)}
								</div>
							</td>
							<td class="px-4 py-3 text-sm text-on-surface">{sale.customer_name || '—'}</td>
							<td class="px-4 py-3 text-sm text-on-surface">{sale.fish_type}</td>
							<td class="px-4 py-3 text-right text-sm font-medium text-on-surface">
								{formatNumber(sale.quantity_kg)}
							</td>
							<td class="px-4 py-3 text-right text-sm font-medium text-on-surface">
								{formatCurrency(sale.total_amount)}
							</td>
							<td class="px-4 py-3">
								<span
									class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {paymentStatusBadge(
										sale.payment_status
									)}"
								>
									{paymentStatusLabel(sale.payment_status)}
								</span>
							</td>
							<td class="px-4 py-3 text-right">
								<button
									onclick={() => navigateToDetail(sale.id)}
									class="inline-flex items-center gap-1 text-sm font-medium text-primary-container transition-colors hover:text-primary"
								>
									View
									<ArrowRight size={14} aria-hidden="true" />
								</button>
							</td>
						</tr>
					{/each}
				</tbody>
			</table>
		</div>
	</ListState>
</ListPageLayout>
