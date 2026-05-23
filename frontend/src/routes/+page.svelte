<script lang="ts">
	import {
		Verified,
		ArrowRight,
		CloudOff,
		CheckCircle,
		RefreshCw,
		Droplets,
		Package,
		Wallet,
		BarChart3,
		Menu,
		X,
		Globe,
		Bell,
		Sun,
		Moon,
		Fish,
		User
	} from '@lucide/svelte';
	import { browser } from '$app/environment';
	import { translations, getLandingLang, setLandingLang, type Lang } from '$lib/i18n/landing';

	let lang = $state<Lang>(getLandingLang());
	let mobileMenuOpen = $state(false);
	let darkMode = $state(false);
	let darkModeInitialized = $state(false);

	// Initialize dark mode from localStorage and system preference
	$effect(() => {
		if (!browser || darkModeInitialized) return;
		const savedDark = localStorage.getItem('df-dark');
		if (savedDark !== null) {
			darkMode = savedDark === 'true';
		} else {
			darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
		}
		darkModeInitialized = true;
	});

	// Persist language
	$effect(() => {
		setLandingLang(lang);
	});

	// Persist and apply dark mode
	$effect(() => {
		if (!browser || !darkModeInitialized) return;
		localStorage.setItem('df-dark', String(darkMode));
		document.documentElement.classList.toggle('dark', darkMode);
	});

	let currentLang = $derived(translations[lang]);

	// Scroll animation with reduced motion support
	function initScrollAnimation(node: HTMLElement) {
		if (!browser) return { destroy() {} };
		const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		if (prefersReducedMotion) {
			node.classList.add('opacity-100', 'translate-y-0');
			return { destroy() {} };
		}

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('opacity-100', 'translate-y-0');
						entry.target.classList.remove('opacity-0', 'translate-y-10');
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
		);
		observer.observe(node);
		return {
			destroy() {
				observer.disconnect();
			}
		};
	}

	// Close mobile menu on escape key
	function handleKeydown(event: KeyboardEvent) {
		if (event.key === 'Escape' && mobileMenuOpen) {
			mobileMenuOpen = false;
		}
	}
</script>

<svelte:window onkeydown={handleKeydown} />

<svelte:head>
	<title>Deshi Fishery - মাছ চাষের সেরা সহায়ক</title>
	<meta
		name="description"
		content="Empower your aquaculture business with data-driven insights. Manage ponds, track stock, and optimize your finances with Bangladesh's most reliable fishery management platform."
	/>
	<meta property="og:title" content="Deshi Fishery" />
	<meta
		property="og:description"
		content="Fisheries management platform for Bangladeshi fish farmers. Works offline."
	/>
	<meta property="og:type" content="website" />
</svelte:head>

<!-- Skip to main content link -->
<a
	href="#main-content"
	class="sr-only focus:not-sr-only focus:absolute focus:z-[999] focus:bg-surface-bright focus:text-primary-container focus:p-4 focus:rounded-lg focus:shadow-lg"
>
	Skip to main content
</a>

<div class="overflow-x-hidden bg-background text-on-background" class:inert={mobileMenuOpen}>
	<!-- TopNavBar -->
	<header class="sticky top-0 z-sticky w-full bg-surface shadow-sm pt-safe">
		<nav
			class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-margin-mobile md:px-margin-desktop"
		>
			<div class="flex items-center gap-md">
				<span class="text-headline-md font-bold text-primary-container">Deshi Fishery</span>
			</div>

			<div class="hidden items-center gap-lg md:flex">
				<a
					href="/app/dashboard"
					class="touch-target border-b-2 border-secondary pb-1 font-label-lg font-bold text-secondary transition-transform active:scale-[0.98]"
				>
					{currentLang.header.dashboard}
				</a>
				<a
					href="/app/ponds"
					class="touch-target font-label-lg text-on-surface-variant transition-colors duration-200 hover:text-secondary-container active:scale-[0.98]"
				>
					{currentLang.header.ponds}
				</a>
				<a
					href="/app/farms"
					class="touch-target font-label-lg text-on-surface-variant transition-colors duration-200 hover:text-secondary-container active:scale-[0.98]"
				>
					{currentLang.header.inventory}
				</a>
				<span class="touch-target font-label-lg text-on-surface-variant/50">
					{currentLang.header.reports}
				</span>
			</div>

			<div class="flex items-center gap-md">
				<div class="flex items-center gap-xs">
					<!-- Dark mode toggle -->
					<button
						class="touch-target relative text-on-surface-variant transition-colors hover:text-secondary"
						aria-label={darkMode ? 'Switch to light mode' : 'Switch to dark mode'}
						onclick={() => (darkMode = !darkMode)}
					>
						<div class="relative h-6 w-6">
							<Sun
								size={24}
								class="absolute inset-0 transition-all duration-300 {darkMode
									? 'rotate-90 scale-0 opacity-0'
									: 'rotate-0 scale-100 opacity-100'}"
							/>
							<Moon
								size={24}
								class="absolute inset-0 transition-all duration-300 {darkMode
									? 'rotate-0 scale-100 opacity-100'
									: '-rotate-90 scale-0 opacity-0'}"
							/>
						</div>
					</button>
					<button
						class="touch-target text-on-surface-variant transition-colors hover:text-secondary"
						aria-label="Change language"
						onclick={() => (lang = lang === 'en' ? 'bn' : 'en')}
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
						href="/app/login"
						class="touch-target px-md font-label-lg text-on-surface-variant transition-colors hover:text-primary"
					>
						{currentLang.header.login}
					</a>
					<a
						href="/app/register"
						class="touch-target rounded-full bg-primary-container px-md font-label-lg text-on-primary shadow-md transition-all hover:brightness-110 active:scale-[0.98]"
					>
						{currentLang.header.register}
					</a>
				</div>
				<div class="md:hidden">
					<button
						class="touch-target text-primary"
						onclick={() => (mobileMenuOpen = !mobileMenuOpen)}
						aria-label="Toggle menu"
						aria-expanded={mobileMenuOpen}
					>
						{#if mobileMenuOpen}
							<X size={24} />
						{:else}
							<Menu size={24} />
						{/if}
					</button>
				</div>
			</div>
		</nav>

		{#if mobileMenuOpen}
			<!-- Backdrop -->
			<div
				class="fixed inset-0 z-modal-backdrop bg-black/50 md:hidden"
				onclick={() => (mobileMenuOpen = false)}
				role="presentation"
			></div>
			<div
				class="mobile-menu relative z-modal border-t border-outline-variant/30 bg-surface px-margin-mobile py-sm pb-safe md:hidden"
				role="dialog"
				aria-label="Mobile navigation"
				aria-modal="true"
			>
				<div class="flex flex-col gap-sm">
					<a
						href="/app/dashboard"
						class="touch-target justify-start font-label-lg text-secondary"
					>
						{currentLang.header.dashboard}
					</a>
					<a
						href="/app/ponds"
						class="touch-target justify-start font-label-lg text-on-surface-variant"
					>
						{currentLang.header.ponds}
					</a>
					<a
						href="/app/farms"
						class="touch-target justify-start font-label-lg text-on-surface-variant"
					>
						{currentLang.header.inventory}
					</a>
					<span class="touch-target justify-start font-label-lg text-on-surface-variant/50">
						{currentLang.header.reports}
					</span>
					<div class="flex gap-sm pt-sm">
						<a
							href="/app/login"
							class="touch-target flex-1 justify-center rounded-lg border border-outline-variant font-label-lg text-on-surface-variant"
						>
							{currentLang.header.login}
						</a>
						<a
							href="/app/register"
							class="touch-target flex-1 justify-center rounded-full bg-primary-container font-label-lg text-on-primary"
						>
							{currentLang.header.register}
						</a>
					</div>
				</div>
			</div>
		{/if}
	</header>

	<!-- Hero Section -->
	<section
		id="main-content"
		class="relative flex min-h-[600px] items-center overflow-hidden bg-primary-container lg:min-h-[700px]"
	>
		<div class="absolute inset-0 bg-gradient-to-br from-primary-container via-primary-container to-secondary/30"></div>

		<div
			class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-12 px-margin-mobile py-20 md:px-margin-desktop md:py-28 lg:grid-cols-2 lg:gap-16 lg:py-36"
		>
			<div class="flex flex-col gap-6 text-white">
				<div
					class="inline-flex w-fit max-w-full items-center gap-2 rounded-full border border-white/20 bg-surface-bright/10 px-4 py-2 backdrop-blur-md"
				>
					<Verified size={18} class="shrink-0 text-white" />
					<span class="text-base font-medium truncate">{currentLang.hero.trustBadge}</span>
				</div>

				<h1
					class="text-[2rem] font-extrabold leading-[1.15] md:text-[2.75rem] lg:text-[3.5rem]"
					class:font-bengali={lang === 'bn'}
				>
					{#if lang === 'bn'}
						মাছ চাষের সেরা <span class="text-mist-light">সহায়ক</span>
					{:else}
						{currentLang.hero.title}
					{/if}
				</h1>

				<p class="max-w-lg text-xl leading-relaxed text-white/90 md:text-[1.375rem]">
					{currentLang.hero.subtitle}
				</p>

				<div class="flex flex-wrap gap-4 pt-4">
					<a
						href="/app/register"
						class="touch-target inline-flex items-center gap-2 rounded-xl bg-surface-bright px-8 py-3.5 text-base font-semibold text-primary-container shadow-xl transition-transform active:scale-[0.98] md:hover:scale-[1.02]"
					>
						{currentLang.hero.ctaPrimary}
						<ArrowRight size={20} />
					</a>
				</div>
			</div>

			<div class="hidden lg:block">
				<div class="glass-card relative rounded-3xl p-8 shadow-2xl">
					<div class="flex flex-col gap-6">
						<div class="flex items-center justify-between">
							<span class="text-xl font-bold text-primary-container"
								>{lang === 'bn' ? 'পুকুর বিশ্লেষণ' : 'Pond Analysis'}</span
							>
							<BarChart3 size={24} class="text-secondary" />
						</div>

						<div
							class="flex h-48 w-full items-end justify-between gap-3 rounded-xl bg-surface-container-low p-6"
						>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 60%"></div>
							<div class="w-8 rounded-t-lg bg-chart-secondary" style="height: 85%"></div>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 45%"></div>
							<div class="w-8 rounded-t-lg bg-chart-secondary" style="height: 70%"></div>
							<div class="w-8 rounded-t-lg bg-chart-primary" style="height: 95%"></div>
						</div>

						<div class="grid grid-cols-2 gap-4">
							<div class="rounded-lg bg-surface-container p-4">
								<span class="block text-sm font-medium text-on-surface-variant/70"
									>{lang === 'bn' ? 'স্টক লেভেল' : 'Stock Level'}</span
								>
								<span class="text-3xl font-bold text-chart-primary tracking-tight"
									>4,250 kg</span
								>
							</div>
							<div class="rounded-lg bg-surface-container p-4">
								<span class="block text-sm font-medium text-on-surface-variant/70"
									>{lang === 'bn' ? 'প্রজেক্টেড লাভ' : 'Projected Profit'}</span
								>
								<span class="text-3xl font-bold text-success-green tracking-tight"
									>৳ 2.4L</span
								>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Offline Feature Section -->
	<section class="section-padding border-y border-outline-variant/30 bg-surface-bright"
	>
		<div
			class="mx-auto flex max-w-7xl flex-col items-start gap-12 px-margin-mobile md:flex-row md:px-margin-desktop lg:gap-16"
		>
			<div class="flex-1 flex flex-col gap-5"
			>
				<div
					class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-container/10 text-primary-container"
				>
					<CloudOff size={40} aria-hidden="true" />
				</div>
				<h2 class="font-headline-lg text-on-background">{currentLang.offline.title}</h2>
				<p class="max-w-lg text-lg leading-relaxed text-on-surface-variant">
					{currentLang.offline.description}
				</p>
				<ul class="flex flex-col gap-3 mt-2">
					{#each currentLang.offline.points as point (point)}
						<li class="flex items-start gap-3 text-on-surface"
						>
							<CheckCircle size={22} class="mt-0.5 shrink-0 text-success-green" aria-hidden="true" />
							<span class="leading-relaxed">{point}</span>
						</li>
					{/each}
				</ul>
			</div>

			<div class="flex-1 w-full max-w-md md:mt-8">
				<div class="rounded-3xl border border-outline-variant/20 bg-surface-container-low p-6"
				>
					<div class="flex flex-col gap-6 rounded-2xl bg-surface-bright p-8 shadow-sm"
					>
						<div class="flex items-center gap-4"
						>
							<div
								class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-warning-amber/20 text-warning-amber"
							>
								<RefreshCw size={24} aria-hidden="true" />
							</div>
							<div class="min-w-0">
								<p class="truncate text-lg font-bold">{currentLang.offline.offlineCardTitle}</p>
								<p class="truncate text-sm opacity-70">{currentLang.offline.offlineCardSubtitle}</p>
							</div>
						</div>
						<div class="flex flex-col gap-4"
						>
							<div class="h-14 w-full rounded-xl bg-surface-container"></div>
							<div class="h-14 w-full rounded-xl bg-surface-container"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Bento Grid -->
	<section class="section-padding bg-surface"
	>
		<div class="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop"
		>
			<div class="mb-16 md:mb-20"
			>
				<h2 class="font-headline-lg text-on-background">
					{currentLang.features.title}
				</h2>
				<p class="mt-4 max-w-2xl text-lg leading-relaxed text-on-surface-variant"
				>
					{currentLang.features.subtitle}
				</p>
			</div>

			<div class="grid grid-cols-1 gap-8 md:grid-cols-12 lg:gap-10"
			>
				<!-- Pond Management -->
				<div
					class="md:col-span-8 flex flex-col justify-between overflow-hidden rounded-3xl border border-outline-variant/30 bg-surface-bright p-8 shadow-sm transition-[opacity,transform] duration-500 lg:p-10"
					use:initScrollAnimation
				>
					<div class="flex flex-col gap-5"
					>
						<div
							class="flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary/10 text-secondary"
						>
							<Droplets size={32} aria-hidden="true" />
						</div>
						<h3 class="font-headline-md text-on-surface"
						>
							{currentLang.features.pondManagement.title}
						</h3>
						<p class="max-w-md leading-relaxed text-on-surface-variant"
						>
							{currentLang.features.pondManagement.description}
						</p>
					</div>
					<div class="mt-8"
					>
						<div class="flex h-52 w-full items-center justify-center rounded-2xl border border-outline-variant/20 bg-surface-container"
						>
							<div class="text-center"
							>
								<div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-secondary/10"
								>
									<Fish size={32} class="text-secondary" aria-hidden="true" />
								</div>
								<p class="text-sm text-on-surface-variant/60"
									>{lang === 'bn' ? 'পুকুর ড্যাশবোর্ড' : 'Pond Dashboard'}</p
								>
							</div>
						</div>
					</div>
				</div>

				<!-- Stock Tracking -->
				<div
					class="md:col-span-4 flex flex-col items-center justify-center gap-6 rounded-3xl border border-outline-variant/30 bg-surface-bright p-8 text-center shadow-lg transition-[opacity,transform] duration-500 lg:p-10"
					use:initScrollAnimation
				>
					<div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-container/10"
					>
						<Package size={32} class="text-primary-container" aria-hidden="true" />
					</div>
					<div class="flex flex-col gap-3"
					>
						<h3 class="font-headline-md text-on-surface"
						>{currentLang.features.stockTracking.title}</h3>
						<p class="max-w-xs leading-relaxed text-on-surface-variant"
						>
							{currentLang.features.stockTracking.description}
						</p>
					</div>
					<a
						href="/app/farms"
						class="touch-target mt-2 rounded-full border border-outline-variant bg-surface-container px-8 transition-colors hover:bg-surface-container-high active:scale-[0.98]"
					>
						{currentLang.features.stockTracking.cta}
					</a>
				</div>

				<!-- Financial Reliability -->
				<div
					class="md:col-span-4 flex flex-col gap-5 rounded-3xl border border-outline-variant/30 bg-surface-bright p-8 shadow-sm transition-[opacity,transform] duration-500 lg:p-10"
					use:initScrollAnimation
				>
					<div
						class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-container/10 text-primary-container"
					>
						<Wallet size={32} aria-hidden="true" />
					</div>
					<div class="flex flex-col gap-3"
					>
						<h3 class="font-headline-md text-on-surface"
						>
							{currentLang.features.financial.title}
						</h3>
						<p class="leading-relaxed text-on-surface-variant"
						>
							{currentLang.features.financial.description}
						</p>
					</div>
				</div>

				<!-- Seasonal Reports -->
				<div
					class="md:col-span-8 flex items-start gap-8 rounded-3xl border border-outline-variant/30 bg-surface-bright p-8 shadow-sm transition-[opacity,transform] duration-500 lg:p-10"
					use:initScrollAnimation
				>
					<div class="flex flex-1 min-w-0 flex-col gap-4"
					>
						<h3 class="font-headline-md text-on-surface"
						>
							{currentLang.features.reports.title}
						</h3>
						<p class="leading-relaxed text-on-surface-variant"
						>
							{currentLang.features.reports.description}
						</p>
						<div class="mt-2 flex flex-wrap gap-3"
						>
							{#each currentLang.features.reports.tags as tag (tag)}
								<span class="rounded-lg bg-surface-container px-4 py-2 text-xs font-bold"
									>{tag}</span
								>
							{/each}
						</div>
					</div>
					<div
						class="hidden h-28 w-28 shrink-0 items-center justify-center rounded-full bg-surface-container sm:flex lg:h-32 lg:w-32"
					>
						<BarChart3 size={48} class="text-primary-container" aria-hidden="true" />
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Testimonial Section -->
	<section class="section-padding bg-surface-container-low"
	>
		<div class="mx-auto flex max-w-3xl flex-col items-center gap-10 px-margin-mobile text-center md:px-margin-desktop lg:gap-12"
		>
			<h2
				class="max-w-2xl text-[1.75rem] font-bold leading-snug text-primary-container md:text-[2.25rem]"
				class:font-bengali={lang === 'bn'}
			>
				{currentLang.cta.title}
			</h2>

			<!-- Testimonial Card -->
			<div class="mx-auto w-full rounded-3xl border border-outline-variant/20 bg-surface-bright p-8 shadow-sm md:p-12"
			>
				<div class="flex flex-col items-center gap-6 md:flex-row md:items-start md:text-left"
				>
					<!-- Avatar placeholder with SVG -->
					<div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-primary-container/10"
					>
						<User size={36} class="text-primary-container" aria-hidden="true" />
					</div>
					<div class="flex flex-col gap-5"
					>
						<blockquote class="text-xl leading-relaxed text-on-surface"
						>
							<p>{currentLang.cta.testimonial.quote}</p>
							<cite class="mt-4 block text-base text-on-surface-variant not-italic"
							>
								<span class="font-bold text-primary-container"
									>{currentLang.cta.testimonial.name}</span> — {currentLang.cta.testimonial.role} · {currentLang.cta.testimonial.location}
								<br />
								<span class="text-sm opacity-70"
									>{currentLang.cta.testimonial.ponds}</span
								>
							</cite>
						</blockquote>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Pricing / Reassurance Section -->
	<section class="section-padding bg-surface-bright"
	>
		<div class="mx-auto flex max-w-4xl flex-col items-center gap-10 px-margin-mobile text-center md:px-margin-desktop lg:gap-12"
		>
			<div class="flex flex-col gap-4"
			>
				<h2 class="font-headline-lg text-on-background">
					{currentLang.pricing.title}
				</h2>
				<p class="mx-auto max-w-xl text-lg leading-relaxed text-on-surface-variant"
				>
					{currentLang.pricing.subtitle}
				</p>
			</div>

			<div class="flex w-full flex-wrap justify-center gap-4"
			>
				{#each currentLang.pricing.features as feature (feature)}
					<div class="flex min-w-[280px] flex-1 max-w-md items-center gap-4 rounded-xl border border-outline-variant/20 bg-surface-container px-6 py-5"
					>
						<CheckCircle size={24} class="shrink-0 text-success-green" aria-hidden="true" />
						<span class="text-left leading-relaxed text-on-surface text-base"
							>{feature}</span
						>
					</div>
				{/each}
			</div>

			<a
				href="/app/register"
				class="touch-target inline-flex items-center gap-2 rounded-xl bg-primary-container px-10 py-4 font-bold text-white shadow-xl transition-transform active:scale-[0.98] md:hover:scale-[1.02]"
			>
				{currentLang.pricing.cta}
				<ArrowRight size={20} />
			</a>
		</div>
	</section>

	<!-- Footer -->
	<footer class="w-full bg-primary text-white"
	>
		<div
			class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-10 px-margin-mobile py-16 md:grid-cols-3 md:px-margin-desktop lg:gap-16"
		>
			<div class="flex flex-col gap-5"
			>
				<span class="text-2xl font-bold">{currentLang.footer.brand}</span>
				<p class="leading-relaxed text-white/70">
					{currentLang.footer.copyright}
				</p>
			</div>
			<div class="flex flex-col gap-3"
			>
				<h4 class="mb-1 text-sm font-bold text-white/70"
					>{currentLang.footer.quickLinks}</h4
				>
				<a
					href="/about"
					class="touch-target justify-start text-white/70 transition-colors hover:text-white active:scale-[0.98]"
				>
					{currentLang.footer.about}
				</a>
				<a
					href="/support"
					class="touch-target justify-start text-white/70 transition-colors hover:text-white active:scale-[0.98]"
				>
					{currentLang.footer.support}
				</a>
				<a
					href="/privacy"
					class="touch-target justify-start text-white/70 transition-colors hover:text-white active:scale-[0.98]"
				>
					{currentLang.footer.privacy}
				</a>
			</div>
			<div class="flex flex-col gap-3"
			>
				<h4 class="mb-1 text-sm font-bold text-white/70"
					>{currentLang.footer.accessibility}</h4
				>
				<a
					href="/terms"
					class="touch-target justify-start text-white/70 transition-colors hover:text-white active:scale-[0.98]"
				>
					{currentLang.footer.terms}
				</a>
				<button
					class="touch-target justify-start text-white/70 transition-colors hover:text-white hover:underline font-medium"
					onclick={() => (lang = lang === 'en' ? 'bn' : 'en')}
				>
					{currentLang.footer.languageToggle}
				</button>
			</div>
		</div>
		<div class="h-6 md:h-0"></div>
	</footer>
</div>

<style>
	/* Mobile menu animation */
	.mobile-menu {
		animation: slideDown 200ms cubic-bezier(0.25, 1, 0.5, 1);
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-8px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}
</style>
