<script lang="ts">
	import { Fish, Heart } from 'lucide-svelte';
</script>

<footer class="border-t border-outline-variant/30 bg-white">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 py-6">
		<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
			<!-- Brand -->
			<div class="flex items-center gap-2">
				<div class="flex h-7 w-7 items-center justify-center rounded-md bg-primary-container/10 text-primary-container">
					<Fish size={16} aria-hidden="true" />
				</div>
				<span class="text-sm font-semibold text-on-surface">Deshi Fishery</span>
			</div>

			<!-- Copyright -->
			<p class="text-sm text-on-surface-variant text-center">
				&copy; {new Date().getFullYear()} Deshi Fishery. Built with <Heart size={14} class="inline text-error" aria-hidden="true" /> for Bangladeshi fish farmers.
			</p>

			<!-- Links -->
			<div class="flex items-center gap-4">
				<a
					href="/app/dashboard"
					class="text-sm text-on-surface-variant hover:text-primary-container transition-colors duration-150"
				>
					Dashboard
				</a>
				<a
					href="/app/farms"
					class="text-sm text-on-surface-variant hover:text-primary-container transition-colors duration-150"
				>
					Farms
				</a>
				<a
					href="/app/ponds"
					class="text-sm text-on-surface-variant hover:text-primary-container transition-colors duration-150"
				>
					Ponds
				</a>
				<a
					href="/"
					class="text-sm text-on-surface-variant hover:text-primary-container transition-colors duration-150"
				>
					Homepage
				</a>
			</div>
		</div>
	</div>
</footer>
