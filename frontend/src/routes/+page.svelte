<script lang="ts">
	import {
		Verified,
		ArrowRight,
		Play,
		CloudOff,
		CheckCircle,
		RefreshCw,
		Droplets,
		Package,
		Wallet,
		BarChart3,
		Menu,
		Globe,
		Bell
	} from '@lucide/svelte';

	let lang = $state<'en' | 'bn'>('en');
	let mobileMenuOpen = $state(false);

	const features = [
		{
			icon: CloudOff,
			title: 'Works Everywhere, Even Offline',
			description:
				"Don't let poor connectivity slow you down. Record feedings, update stock levels, and check pond data without an internet connection. Your data syncs automatically once you're back online.",
			points: [
				'Offline data entry for all features',
				'Automatic background synchronization',
				'Zero data loss during outages'
			]
		}
	];

	const bentoItems = [
		{
			span: 'md:col-span-8',
			icon: Droplets,
			iconColor: 'text-secondary',
			iconBg: 'bg-secondary/10',
			title: 'Intelligent Pond Management',
			description:
				'Monitor pH levels, oxygen, and temperature across all your ponds in real-time. Get instant alerts before problems arise.',
			bg: 'bg-white',
			textColor: 'text-primary-container',
			minHeight: 'min-h-[400px]'
		},
		{
			span: 'md:col-span-4',
			icon: Package,
			iconColor: 'text-chart-secondary',
			iconBg: '',
			title: 'Live Stock Tracking',
			description:
				'Automated feed calculations and growth rate predictions for every species.',
			bg: 'bg-primary-container',
			textColor: 'text-white',
			minHeight: 'min-h-[400px]',
			filled: true
		},
		{
			span: 'md:col-span-4',
			icon: Wallet,
			iconColor: 'text-primary-container',
			iconBg: 'bg-primary-container/10',
			title: 'Financial Reliability',
			description:
				'Precise expense tracking and profit analysis built specifically for the Bangladeshi market.',
			bg: 'bg-surface-container',
			textColor: 'text-primary-container',
			minHeight: 'min-h-[300px]'
		},
		{
			span: 'md:col-span-8',
			icon: BarChart3,
			iconColor: 'text-primary-container',
			iconBg: 'bg-white/40',
			title: 'Seasonal Reports',
			description:
				'Generate comprehensive PDF reports for bank loans or partnership reviews with one click.',
			bg: 'bg-surface-container-high',
			textColor: 'text-primary-container',
			minHeight: 'min-h-[300px]',
			tags: ['PDF', 'EXCEL', 'SYNC']
		}
	];
</script>

<svelte:head>
	<title>Deshi Fishery - মাছ চাষের সেরা assistant</title>
	<meta
		name="description"
		content="Empower your aquaculture business with data-driven insights. Manage ponds, track stock, and optimize your finances with Bangladesh's most reliable fishery management platform."
	/>
</svelte:head>

<div class="overflow-x-hidden bg-background text-on-background">
	<!-- TopNavBar -->
	<header class="sticky top-0 z-50 w-full bg-surface shadow-sm">
		<nav
			class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-margin-mobile md:px-margin-desktop"
		>
			<div class="flex items-center gap-md">
				<span class="text-headline-md font-bold text-primary-container">Deshi Fishery</span>
			</div>

			<div class="hidden items-center gap-lg md:flex">
				<a
					href="/dashboard"
					class="touch-target border-b-2 border-secondary pb-1 font-label-lg font-bold text-secondary transition-transform active:scale-95"
					>Dashboard</a
				>
				<a
					href="/ponds"
					class="touch-target font-label-lg text-on-surface-variant transition-colors duration-200 hover:text-secondary-container active:scale-95"
					>Ponds</a
				>
				<a
					href="/inventory"
					class="touch-target font-label-lg text-on-surface-variant transition-colors duration-200 hover:text-secondary-container active:scale-95"
					>Inventory</a
				>
				<a
					href="/reports"
					class="touch-target font-label-lg text-on-surface-variant transition-colors duration-200 hover:text-secondary-container active:scale-95"
					>Reports</a
				>
			</div>

			<div class="flex items-center gap-md">
				<div class="flex items-center gap-xs">
					<button
						class="touch-target text-on-surface-variant transition-colors hover:text-secondary"
						aria-label="Change language"
					>
						<Globe size={24} />
					</button>
					<button
						class="touch-target text-on-surface-variant transition-colors hover:text-secondary"
						aria-label="Notifications"
					>
						<Bell size={24} />
					</button>
				</div>
				<div class="hidden items-center gap-sm sm:flex">
					<a
						href="/login"
						class="touch-target px-md font-label-lg text-on-surface-variant transition-colors hover:text-primary"
						>Login</a
					>
					<a
						href="/register"
						class="touch-target rounded-full bg-primary-container px-md font-label-lg text-on-primary shadow-md transition-all hover:brightness-110"
						>Register</a
					>
				</div>
				<div class="md:hidden">
					<button
						class="touch-target text-primary"
						onclick={() => (mobileMenuOpen = !mobileMenuOpen)}
						aria-label="Toggle menu"
					>
						<Menu size={24} />
					</button>
				</div>
			</div>
		</nav>

		{#if mobileMenuOpen}
			<div class="border-t border-outline-variant/30 bg-surface px-margin-mobile py-sm md:hidden">
				<div class="flex flex-col gap-sm">
					<a href="/dashboard" class="touch-target justify-start font-label-lg text-secondary">Dashboard</a>
					<a href="/ponds" class="touch-target justify-start font-label-lg text-on-surface-variant">Ponds</a>
					<a href="/inventory" class="touch-target justify-start font-label-lg text-on-surface-variant">Inventory</a>
					<a href="/reports" class="touch-target justify-start font-label-lg text-on-surface-variant">Reports</a>
					<div class="flex gap-sm pt-sm">
						<a href="/login" class="touch-target flex-1 justify-center rounded-lg border border-outline-variant font-label-lg text-on-surface-variant">Login</a>
						<a href="/register" class="touch-target flex-1 justify-center rounded-full bg-primary-container font-label-lg text-on-primary">Register</a>
					</div>
				</div>
			</div>
		{/if}
	</header>

	<!-- Hero Section -->
	<section class="relative flex min-h-[800px] items-center overflow-hidden">
		<div class="absolute inset-0 z-0">
			<img
				alt="Deshi Fishery Farm"
				class="h-full w-full object-cover brightness-75"
				src="https://images.unsplash.com/photo-1559827260-dc66d52bef19?q=80&w=2070&auto=format&fit=crop"
			/>
			<div class="absolute inset-0 bg-gradient-to-r from-primary-container/80 to-transparent"></div>
		</div>

		<div
			class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-xl px-margin-mobile md:px-margin-desktop lg:grid-cols-2"
		>
			<div class="space-y-md text-white">
				<div
					class="inline-flex items-center gap-xs rounded-full border border-white/20 bg-white/10 px-sm py-xs backdrop-blur-md"
				>
					<Verified size={16} class="text-white" />
					<span class="text-sm uppercase tracking-wider">Trusted by 10,000+ Farmers</span>
				</div>

				<h1 class="font-bengali text-5xl font-bold leading-tight">
					মাছ চাষের সেরা <span class="text-chart-secondary">assistant</span>
				</h1>

				<p class="max-w-xl font-body-lg text-white/90">
					Empower your aquaculture business with data-driven insights. Manage ponds, track stock,
					and optimize your finances with Bangladesh's most reliable fishery management platform.
				</p>

				<div class="flex flex-wrap gap-md pt-md">
					<a
						href="/register"
						class="touch-target inline-flex items-center gap-xs rounded-xl bg-primary-container px-xl font-bold text-white shadow-xl transition-transform hover:scale-105"
					>
						Get Started
						<ArrowRight size={20} />
					</a>
					<button
						class="touch-target rounded-xl border border-white/20 bg-white/10 px-xl font-bold text-white backdrop-blur-md transition-all hover:bg-white/20"
					>
						<Play size={20} class="mr-2 inline" />
						Watch Demo
					</button>
				</div>
			</div>

			<div class="hidden lg:block">
				<div class="glass-card relative rounded-3xl p-lg shadow-2xl">
					<div
						class="absolute -right-6 -top-6 flex flex-col items-center rounded-2xl bg-success-green px-md py-sm shadow-lg"
					>
						<span class="text-2xl font-bold">98%</span>
						<span class="text-[10px] uppercase">Accuracy</span>
					</div>

					<div class="space-y-md">
						<div class="flex items-center justify-between">
							<span class="text-xl font-bold text-primary-container">Pond Analysis</span>
							<BarChart3 size={24} class="text-secondary" />
						</div>

						<div
							class="flex h-48 w-full items-end justify-between gap-xs rounded-xl bg-surface-container-low p-md"
						>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 60%"></div>
							<div class="w-8 rounded-t-lg bg-chart-secondary" style="height: 85%"></div>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 45%"></div>
							<div class="w-8 rounded-t-lg bg-chart-secondary" style="height: 70%"></div>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 95%"></div>
						</div>

						<div class="grid grid-cols-2 gap-md">
							<div class="rounded-lg bg-surface-container p-md">
								<span class="block text-xs opacity-70">Stock Level</span>
								<span class="text-xl font-bold text-chart-primary">4,250 kg</span>
							</div>
							<div class="rounded-lg bg-surface-container p-md">
								<span class="block text-xs opacity-70">Projected Profit</span>
								<span class="text-xl font-bold text-success-green">৳ 2.4L</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Offline Feature Section -->
	<section class="border-y border-outline-variant/30 bg-white py-xl">
		<div
			class="mx-auto flex max-w-7xl flex-col items-center gap-xl px-margin-mobile md:flex-row md:px-margin-desktop"
		>
			<div class="flex-1 space-y-md">
				<div
					class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-container/10 text-primary-container"
				>
					<CloudOff size={40} />
				</div>
				<h2 class="font-headline-lg text-primary-container">Works Everywhere, Even Offline</h2>
				<p class="font-body-lg text-on-surface-variant">
					Don't let poor connectivity slow you down. Record feedings, update stock levels, and check
					pond data without an internet connection. Your data syncs automatically once you're back
					online.
				</p>
				<ul class="space-y-sm">
					<li class="flex items-center gap-sm text-on-surface">
						<CheckCircle size={20} class="text-success-green" />
						Offline data entry for all features
					</li>
					<li class="flex items-center gap-sm text-on-surface">
						<CheckCircle size={20} class="text-success-green" />
						Automatic background synchronization
					</li>
					<li class="flex items-center gap-sm text-on-surface">
						<CheckCircle size={20} class="text-success-green" />
						Zero data loss during outages
					</li>
				</ul>
			</div>

			<div class="flex-1 rounded-3xl border border-outline-variant/20 bg-surface-container-low p-md">
				<div class="space-y-md rounded-2xl bg-white p-lg shadow-sm">
					<div class="flex items-center gap-md">
						<div
							class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600"
						>
							<RefreshCw size={20} />
						</div>
						<div>
							<p class="font-bold">Offline Mode Active</p>
							<p class="text-sm opacity-70">Changes will sync later</p>
						</div>
					</div>
					<div class="space-y-sm">
						<div class="h-12 w-full animate-pulse rounded-lg bg-surface-container"></div>
						<div class="h-12 w-full animate-pulse rounded-lg bg-surface-container"></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Bento Grid -->
	<section class="bg-surface-bright py-xl">
		<div class="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
			<div class="mb-xl text-center">
				<h2 class="mb-xs font-headline-lg text-primary-container">
					Professional Tools for Modern Farmers
				</h2>
				<p class="mx-auto max-w-2xl font-body-lg text-on-surface-variant">
					Designed to simplify the complexities of large-scale aquaculture with intuitive digital
					management.
				</p>
			</div>

			<div class="grid grid-cols-1 gap-md md:grid-cols-12">
				{#each bentoItems as item (item.title)}
					<div
						class="group {item.span} {item.bg} {item.textColor} {item.minHeight} flex flex-col overflow-hidden rounded-3xl border border-outline-variant/30 p-lg shadow-sm transition-all duration-700"
					>
						{#if item.filled}
							<item.icon size={48} class="mb-md {item.iconColor}" fill="currentColor" />
						{:else}
							<div
								class="mb-md flex h-12 w-12 items-center justify-center rounded-2xl {item.iconBg} {item.iconColor}"
							>
								<item.icon size={32} />
							</div>
						{/if}

						<h3 class="font-headline-md {item.filled ? 'mb-xs' : 'mb-xs text-primary-container'}">
							{item.title}
						</h3>
						<p class="{item.filled ? 'opacity-80' : 'text-on-surface-variant'} font-body-md">
							{item.description}
						</p>

						{#if item.tags}
							<div class="mt-md flex gap-xs">
								{#each item.tags as tag (tag)}
									<span class="rounded-lg bg-white/50 px-sm py-xs text-xs font-bold">{tag}</span>
								{/each}
							</div>
						{/if}

						{#if item.filled}
							<a
								href="/inventory"
								class="touch-target mt-md self-center rounded-full border border-white/30 bg-white/20 px-lg transition-colors hover:bg-white/30"
							>
								Explore Inventory
							</a>
						{/if}
					</div>
				{/each}
			</div>
		</div>
	</section>

	<!-- Language Toggle / CTA Section -->
	<section class="bg-surface-container-low py-xl">
		<div class="mx-auto max-w-4xl space-y-md px-margin-mobile text-center md:px-margin-desktop">
			<h2 class="font-bengali text-3xl text-primary-container">
				আপনার খামারের ডিজিটাল রূপান্তর শুরু করুন
			</h2>
			<div class="flex items-center justify-center gap-md">
				<button
					class="touch-target rounded-full px-xl font-bold shadow-md transition-all {lang === 'en'
						? 'bg-primary-container text-white'
						: 'border border-outline-variant bg-white font-label-lg text-on-surface hover:bg-surface'}"
					onclick={() => (lang = 'en')}
				>
					English
				</button>
				<button
					class="touch-target rounded-full px-xl font-bold shadow-md transition-all {lang === 'bn'
						? 'bg-primary-container text-white'
						: 'border border-outline-variant bg-white font-label-lg text-on-surface hover:bg-surface'}"
					onclick={() => (lang = 'bn')}
				>
					বাংলা
				</button>
			</div>
			<p class="pt-md font-body-md italic text-on-surface-variant opacity-80">
				"Deshi Fishery has doubled my production efficiency in just six months." - Rahat Ali, Farm
				Owner
			</p>
		</div>
	</section>

	<!-- Footer -->
	<footer class="mt-auto w-full bg-primary text-white">
		<div
			class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-lg px-margin-mobile py-xl md:grid-cols-3 md:px-margin-desktop"
		>
			<div class="flex flex-col gap-md">
				<span class="text-2xl font-bold">Deshi Fishery</span>
				<p class="font-body-md text-white/70">
					© 2024 Deshi Fishery. All rights reserved. Designed for the Bangladeshi Aquaculture
					Community.
				</p>
			</div>
			<div class="flex flex-col gap-sm">
				<h4 class="mb-xs font-bold">Quick Links</h4>
				<a href="/about" class="touch-target justify-start text-white/70 transition-colors hover:text-white"
					>About Us</a
				>
				<a href="/support" class="touch-target justify-start text-white/70 transition-colors hover:text-white"
					>Contact Support</a
				>
				<a href="/privacy" class="touch-target justify-start text-white/70 transition-colors hover:text-white"
					>Privacy Policy</a
				>
			</div>
			<div class="flex flex-col gap-sm">
				<h4 class="mb-xs font-bold">Accessibility</h4>
				<a href="/terms" class="touch-target justify-start text-white/70 transition-colors hover:text-white"
					>Terms of Service</a
				>
				<button
					class="touch-target justify-start font-bold underline"
					onclick={() => (lang = lang === 'en' ? 'bn' : 'en')}
				>
					{lang === 'en' ? 'Bengali' : 'English'} / {lang === 'en' ? 'English' : 'বাংলা'}
				</button>
			</div>
		</div>
	</footer>
</div>
