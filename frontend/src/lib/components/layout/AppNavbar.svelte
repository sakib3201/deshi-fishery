<script lang="ts">
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';
	import { themeStore, langStore } from '$lib/stores/ui.svelte';
	import {
		LayoutDashboard,
		Home,
		Droplets,
		Settings,
		LogOut,
		Menu,
		X,
		ChevronDown,
		Fish,
		Sun,
		Moon,
		Globe,
		PackagePlus
	} from 'lucide-svelte';

	let mobileMenuOpen = $state(false);
	let profileMenuOpen = $state(false);
	let profileMenuRef = $state<HTMLDivElement | null>(null);

	const navItems = [
		{ label: 'Dashboard', href: '/app/dashboard', icon: LayoutDashboard },
		{ label: 'Farms', href: '/app/farms', icon: Home },
		{ label: 'Ponds', href: '/app/ponds', icon: Droplets },
		{ label: 'Stock Releases', href: '/app/stock-releases', icon: PackagePlus }
	];

	function isActive(path: string) {
		return $page.url.pathname === path || $page.url.pathname.startsWith(path + '/');
	}

	function handleLogout() {
		profileMenuOpen = false;
		mobileMenuOpen = false;
		authStore.logout();
		goto('/app/login');
	}

	function toggleMobileMenu() {
		mobileMenuOpen = !mobileMenuOpen;
	}

	function closeMobileMenu() {
		mobileMenuOpen = false;
	}

	function toggleProfileMenu() {
		profileMenuOpen = !profileMenuOpen;
	}

	function handleClickOutside(event: MouseEvent) {
		if (profileMenuRef && !profileMenuRef.contains(event.target as Node)) {
			profileMenuOpen = false;
		}
	}
</script>

<svelte:window onclick={handleClickOutside} />

<!-- Skip Link -->
<a
	href="#main-content"
	class="focus:bg-surface-bright focus:text-primary-container sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[700] focus:rounded-lg focus:px-4 focus:py-3 focus:shadow-lg"
>
	Skip to main content
</a>

<header class="z-sticky bg-surface border-outline-variant/30 sticky top-0 border-b shadow-sm">
	<div class="mx-auto max-w-7xl px-4 sm:px-6">
		<div class="flex h-16 items-center justify-between">
			<!-- Logo + Brand -->
			<div class="flex items-center gap-3">
				<a
					href="/app/dashboard"
					class="focus:ring-primary-container flex items-center gap-2.5 rounded-lg focus:ring-2 focus:ring-offset-2 focus:outline-none"
				>
					<div
						class="bg-primary-container flex h-9 w-9 items-center justify-center rounded-lg text-white"
					>
						<Fish size={20} aria-hidden="true" />
					</div>
					<span class="text-primary-container hidden text-lg font-bold sm:block">Deshi Fishery</span
					>
				</a>
			</div>

			<!-- Desktop Navigation -->
			<nav class="hidden items-center gap-1 md:flex" aria-label="Main navigation">
				{#each navItems as item}
					<a
						href={item.href}
						class="flex min-h-[44px] items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors duration-150 {isActive(
							item.href
						)
							? 'bg-primary-container/10 text-primary-container'
							: 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'}"
						aria-current={isActive(item.href) ? 'page' : undefined}
					>
						<item.icon size={18} aria-hidden="true" />
						{item.label}
					</a>
				{/each}
			</nav>

			<!-- Right side: Toggles + Profile + Mobile menu -->
			<div class="flex items-center gap-1 sm:gap-2">
				<!-- Theme Toggle -->
				<button
					onclick={() => themeStore.toggle()}
					class="text-on-surface-variant hover:bg-surface-container flex h-10 w-10 items-center justify-center rounded-lg transition-colors duration-150"
					aria-label={themeStore.theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
					title={themeStore.theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
				>
					{#if themeStore.theme === 'dark'}
						<Sun size={20} aria-hidden="true" />
					{:else}
						<Moon size={20} aria-hidden="true" />
					{/if}
				</button>

				<!-- Language Toggle -->
				<button
					onclick={() => langStore.toggle()}
					class="text-on-surface-variant hover:bg-surface-container flex h-10 min-w-[44px] items-center justify-center gap-1 rounded-lg px-2.5 text-sm font-medium transition-colors duration-150"
					aria-label={langStore.lang === 'bn' ? 'Switch to English' : 'Switch to Bengali'}
					title={langStore.lang === 'bn' ? 'Switch to English' : 'Switch to Bengali'}
				>
					<Globe size={18} aria-hidden="true" />
					<span class="hidden sm:inline">{langStore.lang === 'bn' ? 'বাংলা' : 'EN'}</span>
				</button>

				<!-- Profile Dropdown -->
				<div class="relative" bind:this={profileMenuRef}>
					<button
						onclick={toggleProfileMenu}
						class="text-on-surface-variant hover:bg-surface-container flex min-h-[44px] items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150"
						aria-expanded={profileMenuOpen}
						aria-haspopup="menu"
						aria-label="User menu for {authStore.user?.name || 'User'}"
					>
						<div
							class="bg-primary-container/10 text-primary-container flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold"
						>
							{authStore.user?.name?.charAt(0)?.toUpperCase() || 'U'}
						</div>
						<span class="hidden max-w-[120px] truncate sm:block"
							>{authStore.user?.name || 'User'}</span
						>
						<ChevronDown
							size={16}
							class="transition-transform duration-150 {profileMenuOpen ? 'rotate-180' : ''}"
							aria-hidden="true"
						/>
					</button>

					{#if profileMenuOpen}
						<div
							class="bg-surface-bright shadow-elevated border-outline-variant/30 z-dropdown absolute top-full right-0 mt-2 w-56 rounded-xl border py-1.5"
							role="menu"
							aria-label="User menu"
						>
							<div class="border-outline-variant/20 mb-1 border-b px-4 py-2">
								<p class="text-on-surface truncate text-sm font-semibold">
									{authStore.user?.name || 'User'}
								</p>
								<p class="text-on-surface-variant truncate text-xs">
									{authStore.user?.email || ''}
								</p>
								<p class="text-primary-container mt-0.5 text-xs capitalize">
									{authStore.user?.role || 'user'}
								</p>
							</div>
							<a
								href="/app/farms"
								class="text-on-surface-variant hover:bg-surface-container flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
								role="menuitem"
								onclick={() => (profileMenuOpen = false)}
							>
								<Settings size={16} aria-hidden="true" />
								Manage Farms
							</a>
							<button
								onclick={handleLogout}
								class="text-error hover:bg-error-container flex w-full items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
								role="menuitem"
							>
								<LogOut size={16} aria-hidden="true" />
								Log Out
							</button>
						</div>
					{/if}
				</div>

				<!-- Mobile Menu Toggle -->
				<button
					onclick={toggleMobileMenu}
					class="text-on-surface-variant hover:bg-surface-container flex h-11 w-11 items-center justify-center rounded-lg transition-colors md:hidden"
					aria-expanded={mobileMenuOpen}
					aria-controls="mobile-menu"
					aria-label={mobileMenuOpen ? 'Close menu' : 'Open menu'}
				>
					{#if mobileMenuOpen}
						<X size={24} aria-hidden="true" />
					{:else}
						<Menu size={24} aria-hidden="true" />
					{/if}
				</button>
			</div>
		</div>
	</div>

	<!-- Mobile Menu -->
	{#if mobileMenuOpen}
		<div
			id="mobile-menu"
			class="border-outline-variant/30 bg-surface border-t md:hidden"
			role="navigation"
			aria-label="Mobile navigation"
		>
			<div class="space-y-1 px-4 py-3">
				{#each navItems as item}
					<a
						href={item.href}
						class="flex min-h-[56px] items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors duration-150 {isActive(
							item.href
						)
							? 'bg-primary-container/10 text-primary-container'
							: 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'}"
						aria-current={isActive(item.href) ? 'page' : undefined}
						onclick={closeMobileMenu}
					>
						<item.icon size={22} aria-hidden="true" />
						{item.label}
					</a>
				{/each}
				<div class="border-outline-variant/20 mt-2 space-y-1 border-t pt-2">
					<!-- Mobile Theme Toggle -->
					<button
						onclick={() => {
							themeStore.toggle();
						}}
						class="text-on-surface-variant hover:bg-surface-container flex min-h-[56px] w-full items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors duration-150"
					>
						{#if themeStore.theme === 'dark'}
							<Sun size={22} aria-hidden="true" />
							Switch to Light Mode
						{:else}
							<Moon size={22} aria-hidden="true" />
							Switch to Dark Mode
						{/if}
					</button>
					<!-- Mobile Language Toggle -->
					<button
						onclick={() => {
							langStore.toggle();
						}}
						class="text-on-surface-variant hover:bg-surface-container flex min-h-[56px] w-full items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors duration-150"
					>
						<Globe size={22} aria-hidden="true" />
						{langStore.lang === 'bn' ? 'Switch to English' : 'Switch to Bengali (বাংলা)'}
					</button>
					<button
						onclick={handleLogout}
						class="text-error hover:bg-error-container flex min-h-[56px] w-full items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors duration-150"
					>
						<LogOut size={22} aria-hidden="true" />
						Log Out
					</button>
				</div>
			</div>
		</div>
	{/if}
</header>
