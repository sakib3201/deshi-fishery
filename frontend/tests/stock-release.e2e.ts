import { test, expect } from '@playwright/test';

test.describe('Stock Releases', () => {
	async function registerAndCreateFarm(page: any) {
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `stocktest${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Stock Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		await page.fill('input[id="name"]', 'Stock Test Farm');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/dashboard', { timeout: 10000 });
		return email;
	}

	async function createPond(page: any): Promise<number> {
		await page.goto('/app/ponds/new');
		await page.waitForSelector('text=Add New Pond', { timeout: 10000 });
		await page.fill('input[id="pond_number"]', 'Pond 1');
		await page.fill('input[id="size"]', '0.5');
		await page.click('button[type="submit"]');
		// Wait for navigation to complete (pond creation redirects to list)
		await page.waitForTimeout(3000);

		// Extract pond ID from the page using data-testid
		const pondId = await page.evaluate(() => {
			const editButton = document.querySelector('[data-testid^="pond-edit-"]');
			if (editButton) {
				const testId = editButton.getAttribute('data-testid');
				const match = testId?.match(/pond-edit-(\d+)/);
				return match ? parseInt(match[1], 10) : 1;
			}
			// Fallback: check URL for pond ID if redirected to edit page
			const urlMatch = window.location.pathname.match(/\/app\/ponds\/(\d+)/);
			if (urlMatch) return parseInt(urlMatch[1], 10);
			return 1;
		});
		console.log('Extracted pond ID:', pondId);
		return pondId;
	}

	test('user can navigate to stock releases list', async ({ page }) => {
		await registerAndCreateFarm(page);
		await createPond(page);

		await page.goto('/app/stock-releases');
		await page.waitForSelector('text=Stock Releases', { timeout: 10000 });
		await expect(page.locator('text=Track fish fry releases into your ponds')).toBeVisible();
		await expect(page.locator('text=Record Release')).toBeVisible();
	});

	test('user can create a stock release', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Navigate to create stock release
		// First go to ponds page to ensure farm context is loaded
		await page.goto('/app/ponds');
		await page.waitForTimeout(2000);

		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });

		// Fill the form with the actual pond ID
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.waitForTimeout(500);
		await page.selectOption('select[id="species"]', 'Rui');
		await page.fill('input[id="quantity"]', '1000');
		await page.fill('input[id="avg_weight"]', '2.5');
		await page.fill('input[id="cost"]', '5000');
		await page.fill('input[id="release_date"]', '2026-05-20');
		await page.fill('textarea[id="notes"]', 'Test release notes');

		await page.click('button[type="submit"]');

		// Wait for navigation to complete and check we're on the list page
		await page.waitForTimeout(3000);
		await page.screenshot({ path: 'test-results/create-release.png' });
		// Check for the species in the table (not the dropdown option)
		await expect(page.locator('td:has-text("Rui")').first()).toBeVisible({ timeout: 10000 });
		await expect(page.locator('td:has-text("1,000")').first()).toBeVisible();
	});

	test('user can view stock release detail', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Create a stock release first
		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="species"]', 'Katla');
		await page.fill('input[id="quantity"]', '500');
		await page.fill('input[id="avg_weight"]', '3.0');
		await page.fill('input[id="cost"]', '3000');
		await page.fill('input[id="release_date"]', '2026-05-15');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		// Click view button for the release (first row in table)
		await page.locator('text=View').first().click();

		// Should show detail page
		await page.waitForSelector('text=Katla', { timeout: 10000 });
		await expect(page.locator('text=500 fish')).toBeVisible();
		await page.screenshot({ path: 'test-results/view-detail.png' });
		// Average weight renders as "3.00 g" because the backend preserves decimal precision
		await expect(page.locator('text=/3\\.00 g/')).toBeVisible();
	});

	test('user can delete a stock release', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Create a stock release
		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="species"]', 'Tilapia');
		await page.fill('input[id="quantity"]', '2000');
		await page.fill('input[id="avg_weight"]', '1.5');
		await page.fill('input[id="cost"]', '4000');
		await page.fill('input[id="release_date"]', '2026-05-10');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		// Navigate to detail and delete
		await page.locator('text=View').first().click();
		await page.waitForSelector('text=Tilapia', { timeout: 10000 });

		// Handle confirmation dialog
		page.on('dialog', async (dialog) => {
			await dialog.accept();
		});

		await page.click('text=Delete');

		// Should redirect back to list and Tilapia release should be gone
		await page.waitForTimeout(5000);
		await page.screenshot({ path: 'test-results/delete-release.png' });
		await expect(page).toHaveURL('/app/stock-releases', { timeout: 10000 });
		await expect(page.locator('td:has-text("Tilapia")')).not.toBeVisible();
	});
});
