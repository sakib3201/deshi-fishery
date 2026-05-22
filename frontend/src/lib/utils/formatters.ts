export function formatCurrency(amount: number, locale: 'en' | 'bn' = 'en'): string {
	const formatter = new Intl.NumberFormat(locale === 'bn' ? 'bn-BD' : 'en-BD', {
		style: 'currency',
		currency: 'BDT',
		minimumFractionDigits: 2
	});
	return formatter.format(amount);
}

export function formatNumber(value: number, locale: 'en' | 'bn' = 'en'): string {
	return new Intl.NumberFormat(locale === 'bn' ? 'bn-BD' : 'en-BD').format(value);
}

export function formatDate(date: Date, locale: 'en' | 'bn' = 'en'): string {
	return new Intl.DateTimeFormat(locale === 'bn' ? 'bn-BD' : 'en-GB', {
		year: 'numeric',
		month: 'short',
		day: 'numeric'
	}).format(date);
}
