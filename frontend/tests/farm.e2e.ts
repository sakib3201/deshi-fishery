import { test, expect } from '@playwright/test';

test.describe('Farm Management', () => {
	test('new user is redirected to onboarding after login', async ({ page }) => {
		// Register a new user
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `farmtest${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Farm Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });

		// Login - should redirect to onboarding
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.click('button[type="submit"]');

		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });
		await expect(page.locator('text=Create Your Farm')).toBeVisible();
	});

	test('user can create a farm during onboarding', async ({ page }) => {
		// Register and login
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `farmtest${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Farm Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });

		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		// Create farm
		await page.fill('input[id="name"]', 'My Test Farm');
		await page.fill('input[id="location"]', 'Rajshahi');
		await page.click('button[type="submit"]');

		// Should redirect to dashboard
		await expect(page).toHaveURL('/app/dashboard', { timeout: 10000 });
		await expect(page.locator('text=My Test Farm')).toBeVisible();
	});

	test('user can switch between farms', async ({ page }) => {
		// Register and login
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `farmtest${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Farm Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });

		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		// Create first farm
		await page.fill('input[id="name"]', 'Farm A');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/dashboard', { timeout: 10000 });

		// Navigate to farms page and create second farm
		await page.goto('/app/farms');
		await page.click('text=+ Add Farm');
		await page.fill('input[id="name"]', 'Farm B');
		await page.click('button[type="submit"]');

		// Go back to dashboard
		await page.goto('/app/dashboard');

		// Open farm switcher and switch
		await page.click('text=Farm A');
		await page.click('text=Farm B');

		// Should show Farm B as current
		await expect(page.locator('text=Farm B')).toBeVisible();
	});
});
