import { browser } from '$app/environment';

const THEME_KEY = 'df-theme';
const LANG_KEY = 'df-lang';

export type Theme = 'light' | 'dark';
export type Lang = 'en' | 'bn';

function createThemeStore() {
	let theme = $state<Theme>('light');

	function init() {
		if (!browser) return;
		const saved = localStorage.getItem(THEME_KEY);
		const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
		if (saved === 'dark' || saved === 'light') {
			theme = saved;
		} else {
			theme = systemDark ? 'dark' : 'light';
		}
		applyTheme(theme);
	}

	function applyTheme(t: Theme) {
		if (!browser) return;
		const html = document.documentElement;
		if (t === 'dark') {
			html.classList.add('dark');
		} else {
			html.classList.remove('dark');
		}
		localStorage.setItem(THEME_KEY, t);
	}

	function toggle() {
		theme = theme === 'light' ? 'dark' : 'light';
		applyTheme(theme);
	}

	function set(t: Theme) {
		theme = t;
		applyTheme(theme);
	}

	return {
		get theme() { return theme; },
		init,
		toggle,
		set
	};
}

function createLangStore() {
	let lang = $state<Lang>('en');

	function init() {
		if (!browser) return;
		const saved = localStorage.getItem(LANG_KEY);
		if (saved === 'bn' || saved === 'en') {
			lang = saved;
		}
		applyLang(lang);
	}

	function applyLang(l: Lang) {
		if (!browser) return;
		document.documentElement.lang = l;
		localStorage.setItem(LANG_KEY, l);
	}

	function toggle() {
		lang = lang === 'en' ? 'bn' : 'en';
		applyLang(lang);
	}

	function set(l: Lang) {
		lang = l;
		applyLang(lang);
	}

	return {
		get lang() { return lang; },
		init,
		toggle,
		set
	};
}

export const themeStore = createThemeStore();
export const langStore = createLangStore();
