import { test, expect } from '@playwright/test';

test.describe('Sales', () => {
	async function registerAndCreateFarm(page: any) {
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `saletest${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Sale Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		await page.fill('input[id="name"]', 'Sale Test Farm');
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
		await page.waitForTimeout(3000);

		const pondId = await page.evaluate(() => {
			const editButton = document.querySelector('[data-testid^="pond-edit-"]');
			if (editButton) {
				const testId = editButton.getAttribute('data-testid');
				const match = testId?.match(/pond-edit-(\d+)/);
				return match ? parseInt(match[1], 10) : 1;
			}
			const urlMatch = window.location.pathname.match(/\/app\/ponds\/(\d+)/);
			if (urlMatch) return parseInt(urlMatch[1], 10);
			return 1;
		});
		console.log('Extracted pond ID:', pondId);
		return pondId;
	}

	test('user can navigate to sales list', async ({ page }) => {
		await registerAndCreateFarm(page);
		await createPond(page);

		await page.goto('/app/sales');
		await page.waitForSelector('text=Sales', { timeout: 10000 });
		await expect(page.locator('text=Track fish sales and payments')).toBeVisible();
		await expect(page.locator('text=Record Sale')).toBeVisible();
	});

	test('user can create a wholesale sale', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Add stock to the pond first via stock release
		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="species"]', 'Rui');
		await page.fill('input[id="quantity"]', '100000');
		await page.fill('input[id="avg_weight"]', '2.5');
		await page.fill('input[id="cost"]', '5000');
		await page.fill('input[id="release_date"]', '2026-05-20');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		await page.goto('/app/sales/new');
		await page.waitForSelector('text=Record Sale', { timeout: 10000 });

		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="sale_type"]', 'wholesale');
		await page.fill('input[id="date"]', '2026-05-20');
		await page.selectOption('select[id="fish_type"]', 'Rui');
		await page.fill('input[id="avg_weight"]', '250');
		await page.fill('input[id="quantity"]', '100');
		await page.fill('input[id="rate"]', '250');
		await page.fill('input[id="customer_name"]', 'Ali Bhai');
		await page.fill('textarea[id="notes"]', 'Test sale notes');

		await page.click('button[type="submit"]');

		await page.waitForTimeout(3000);
		await page.screenshot({ path: 'test-results/create-sale.png' });
		await expect(page.locator('td:has-text("Ali Bhai")').first()).toBeVisible({ timeout: 10000 });
		await expect(page.locator('td:has-text("Rui")').first()).toBeVisible();
	});

	test('user can view sale detail', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Add stock to the pond first via stock release
		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="species"]', 'Katla');
		await page.fill('input[id="quantity"]', '100000');
		await page.fill('input[id="avg_weight"]', '3.0');
		await page.fill('input[id="cost"]', '3000');
		await page.fill('input[id="release_date"]', '2026-05-15');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		await page.goto('/app/sales/new');
		await page.waitForSelector('text=Record Sale', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="fish_type"]', 'Katla');
		await page.fill('input[id="avg_weight"]', '300');
		await page.fill('input[id="quantity"]', '50');
		await page.fill('input[id="rate"]', '300');
		await page.fill('input[id="date"]', '2026-05-15');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		await page.locator('text=View').first().click();

		await page.waitForSelector('text=Katla', { timeout: 10000 });
		await expect(page.locator('text=50 kg')).toBeVisible();
		await page.screenshot({ path: 'test-results/view-sale-detail.png' });
	});

	test('user can delete a sale', async ({ page }) => {
		await registerAndCreateFarm(page);
		const pondId = await createPond(page);

		// Add stock to the pond first via stock release
		await page.goto('/app/stock-releases/new');
		await page.waitForSelector('text=Record Fry Release', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="species"]', 'Tilapia');
		await page.fill('input[id="quantity"]', '100000');
		await page.fill('input[id="avg_weight"]', '1.5');
		await page.fill('input[id="cost"]', '4000');
		await page.fill('input[id="release_date"]', '2026-05-10');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		await page.goto('/app/sales/new');
		await page.waitForSelector('text=Record Sale', { timeout: 10000 });
		await page.fill('input[id="pond_id"]', String(pondId));
		await page.selectOption('select[id="fish_type"]', 'Tilapia');
		await page.fill('input[id="avg_weight"]', '200');
		await page.fill('input[id="quantity"]', '75');
		await page.fill('input[id="rate"]', '200');
		await page.fill('input[id="date"]', '2026-05-10');
		await page.click('button[type="submit"]');
		await page.waitForTimeout(3000);

		await page.locator('text=View').first().click();
		await page.waitForSelector('text=Tilapia', { timeout: 10000 });

		page.on('dialog', async (dialog) => {
			await dialog.accept();
		});

		await page.click('text=Delete');

		await page.waitForTimeout(5000);
		await page.screenshot({ path: 'test-results/delete-sale.png' });
		await expect(page).toHaveURL('/app/sales', { timeout: 10000 });
		await expect(page.locator('td:has-text("Tilapia")')).not.toBeVisible();
	});
});
