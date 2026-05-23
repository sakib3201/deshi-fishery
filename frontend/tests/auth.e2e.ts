import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
	test('user can register and is automatically logged in', async ({ page }) => {
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });

		await page.fill('input[name="name"]', 'Test User');
		await page.fill('input[name="email"]', `test${Date.now()}@example.com`);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');

		// After successful registration, user is automatically logged in
		// Should redirect to onboarding (since no farm exists yet)
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });
	});

	test('user can login with valid credentials', async ({ page }) => {
		// First register a user
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `test${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		// Registration auto-logs in and redirects to onboarding
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		// Logout first
		await page.goto('/app/logout');
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });

		// Now login
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.click('button[type="submit"]');

		// Should redirect to onboarding (no farm yet)
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });
	});

	test('login with invalid credentials shows error', async ({ page }) => {
		await page.goto('/app/login');

		await page.fill('input[name="email"]', 'nonexistent@example.com');
		await page.fill('input[name="password"]', 'wrongpassword');
		await page.click('button[type="submit"]');

		// Should show error message with AlertCircle icon
		await expect(page.locator('text=The provided credentials are incorrect')).toBeVisible({ timeout: 10000 });
	});

	test('logout clears session and redirects to login', async ({ page }) => {
		// Register (auto-login)
		await page.goto('/app/register');
		await page.waitForSelector('input[name="name"]', { timeout: 10000 });
		const email = `test${Date.now()}@example.com`;
		await page.fill('input[name="name"]', 'Test User');
		await page.fill('input[name="email"]', email);
		await page.fill('input[name="password"]', 'password123');
		await page.fill('input[name="password_confirmation"]', 'password123');
		await page.click('button[type="submit"]');
		await expect(page).toHaveURL('/app/onboarding', { timeout: 10000 });

		// Logout via navigation
		await page.goto('/app/logout');

		// Should redirect to login
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });
	});

	test('accessing dashboard while logged out redirects to login', async ({ page }) => {
		await page.goto('/app/dashboard');
		await expect(page).toHaveURL('/app/login', { timeout: 10000 });
	});
});
