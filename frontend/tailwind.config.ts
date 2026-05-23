import type { Config } from 'tailwindcss';

export default {
	content: ['./src/**/*.{html,js,svelte,ts}'],
	darkMode: 'class',
	theme: {
		extend: {
			colors: {
				brand: {
					primary: '#050C9C',
					secondary: '#3572EF',
					accent: '#3ABEF9',
					light: '#A7E6FF'
				},
				surface: {
					DEFAULT: '#F0F9FF',
					dark: '#0F172A',
					container: '#d6f2ff',
					'container-high': '#c7eeff',
					'container-low': '#e5f6ff',
					bright: '#f3fbff'
				},
				primary: {
					DEFAULT: '#000265',
					container: '#050c9c'
				},
				secondary: {
					DEFAULT: '#0054cc',
					container: '#2f6dea'
				},
				'on-surface': {
					DEFAULT: '#001f28',
					variant: '#4a5568'
				},
				background: '#f3fbff',
				'on-background': '#001f28',
				'outline-variant': '#c6c5d6',
				'success-green': '#047857',
				'on-primary': '#ffffff',
				'on-secondary': '#ffffff',
				'chart-primary': '#3572EF',
				'chart-secondary': '#3ABEF9'
			},
			fontFamily: {
				sans: ['Open Sans', 'Noto Sans Bengali', 'system-ui', 'sans-serif'],
				bengali: ['Noto Sans Bengali', 'system-ui', 'sans-serif'],
				headline: ['Open Sans', 'system-ui', 'sans-serif'],
				body: ['Open Sans', 'system-ui', 'sans-serif'],
				label: ['Noto Sans Bengali', 'Open Sans', 'system-ui', 'sans-serif']
			},
	screens: {
			xs: '360px',
			sm: '640px',
			md: '768px',
			lg: '1024px',
			xl: '1280px'
		},
			spacing: {
				base: '4px',
				xs: '8px',
				sm: '16px',
				md: '24px',
				lg: '32px',
				xl: '48px',
				'margin-desktop': '40px',
				'margin-mobile': '16px',
				gutter: '16px',
				touch: '56px'
			},
			borderRadius: {
				DEFAULT: '0.25rem',
				lg: '0.5rem',
				xl: '0.75rem',
				'2xl': '1rem',
				'3xl': '1.5rem',
				full: '9999px'
			}
		}
	},
	plugins: []
} satisfies Config;
