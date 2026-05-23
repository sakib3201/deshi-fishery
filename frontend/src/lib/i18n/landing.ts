import { browser } from '$app/environment';

const LANG_KEY = 'df-lang';

export type Lang = 'en' | 'bn';

export const translations = {
	en: {
		header: {
			dashboard: 'Dashboard',
			ponds: 'Ponds',
			inventory: 'Inventory',
			reports: 'Reports',
			login: 'Login',
			register: 'Register'
		},
		hero: {
			trustBadge: 'Trusted by 10,000+ farmers across Bangladesh',
			title: 'The Best Assistant for Fish Farming',
			subtitle:
				'Manage ponds, track stock, and know your profits. Works without internet. Built for Bangladeshi fish farmers.',
			ctaPrimary: 'Start Using for Free'
		},
		offline: {
			title: 'Works Without Internet',
			description:
				'Record feedings, update stock, and check pond data even when your connection drops. Everything syncs automatically when you are back online.',
			points: [
				'Record data without internet',
				'Auto-sync when connection returns',
				'Nothing gets lost'
			],
			offlineCardTitle: 'Working Offline',
			offlineCardSubtitle: 'Will sync when online'
		},
		features: {
			title: 'Everything You Need to Run Your Farm',
			subtitle: 'Track stock, manage ponds, and see your profits. All in one place.',
			pondManagement: {
				title: 'Pond Management',
				description:
					'Track water quality, feeding schedules, and fish health for every pond. Know which ponds need attention before problems cost you money.'
			},
			stockTracking: {
				title: 'Stock Tracking',
				description:
					'See how much fish you have, how fast they are growing, and when they are ready for sale.',
				cta: 'See How It Works'
			},
			financial: {
				title: 'Know Your Profits',
				description:
					'Track expenses, sales, and profit for each pond. See which ponds make money and which do not.'
			},
			reports: {
				title: 'Reports for Banks and Partners',
				description:
					'Generate clean PDF and Excel reports in one tap. Ready for bank loans, partnerships, or tax filing.',
				tags: ['PDF', 'EXCEL', 'SYNC']
			}
		},
		cta: {
			title: 'Join 10,000+ Farmers Already Using Deshi Fishery',
			testimonial: {
				quote: '"Since switching to Deshi Fishery, I have cut feed waste by 30% and finally know which ponds are profitable."',
				name: 'Rahat Ali',
				role: 'Farm Owner',
				location: 'Mymensingh District',
				ponds: '12 ponds, 8 acres'
			}
		},
		pricing: {
			title: 'Free. No Surprises.',
			subtitle: 'Every feature is free. No credit card needed. No hidden fees. No paid tiers.',
			features: [
				'Unlimited ponds and stock records',
				'Full offline mode',
				'PDF and Excel reports',
				'Bengali and English',
				'Phone and desktop access'
			],
			cta: 'Create Free Account'
		},
		footer: {
			brand: 'Deshi Fishery',
			copyright: '\u00A9 2024 Deshi Fishery. Built for fish farmers in Bangladesh.',
			quickLinks: 'Quick Links',
			about: 'About Us',
			support: 'Get Help',
			privacy: 'Privacy Policy',
			accessibility: 'Accessibility',
			terms: 'Terms of Service',
			languageToggle: 'Bengali / English'
		}
	},
	bn: {
		header: {
			dashboard: 'ড্যাশবোর্ড',
			ponds: 'পুকুর',
			inventory: 'ইনভেন্টরি',
			reports: 'রিপোর্ট',
			login: 'লগইন',
			register: 'নিবন্ধন'
		},
		hero: {
			trustBadge: 'বাংলাদেশের ১০,০০০+ কৃষকের বিশ্বাস',
			title: 'মাছ চাষের সেরা সহায়ক',
			subtitle:
				'পুকুর পরিচালনা, স্টক ট্র্যাকিং এবং লাভ জানুন। ইন্টারনেট ছাড়াই কাজ করে। বাংলাদেশের মৎস্য চাষিদের জন্য তৈরি।',
			ctaPrimary: 'বিনামূল্যে ব্যবহার শুরু করুন'
		},
		offline: {
			title: 'ইন্টারনেট ছাড়াই কাজ করে',
			description:
				'খাবার দেওয়া রেকর্ড করুন, স্টক আপডেট করুন এবং পুকুরের তথ্য দেখুন ইন্টারনেট না থাকলেও। অনলাইনে আসলে সবকিছু স্বয়ংক্রিয়ভাবে সিঙ্ক হয়ে যাবে।',
			points: [
				'ইন্টারনেট ছাড়াই ডেটা রেকর্ড করুন',
				'কানেকশন ফিরলে অটো সিঙ্ক',
				'কিছুই হারাবে না'
			],
			offlineCardTitle: 'অফলাইনে কাজ করছে',
			offlineCardSubtitle: 'অনলাইনে আসলে সিঙ্ক হবে'
		},
		features: {
			title: 'খামার চালানোর যা কিছু দরকার',
			subtitle: 'স্টক ট্র্যাক করুন, পুকুর পরিচালনা করুন এবং লাভ দেখুন। সবকিছু এক জায়গায়।',
			pondManagement: {
				title: 'পুকুর পরিচালনা',
				description:
					'প্রতিটি পুকুরের পানির মান, খাবারের সময়সূচি এবং মাছের স্বাস্থ্য ট্র্যাক করুন। সমস্যা বড় হওয়ার আগেই জেনে নিন কোন পুকুরে মনোযোগ দরকার।'
			},
			stockTracking: {
				title: 'স্টক ট্র্যাকিং',
				description:
					'কত মাছ আছে, কত দ্রুত বড় হচ্ছে এবং কখন বিক্রির জন্য প্রস্তুত তা জানুন।',
				cta: 'কিভাবে কাজ করে দেখুন'
			},
			financial: {
				title: 'লাভ জানুন',
				description:
					'প্রতিটি পুকুরের খরচ, বিক্রি এবং লাভ ট্র্যাক করুন। কোন পুকুর লাভজনক এবং কোনটি নয় তা দেখুন।'
			},
			reports: {
				title: 'ব্যাংক ও পার্টনারদের জন্য রিপোর্ট',
				description:
					'এক ট্যাপে পরিষ্কার PDF এবং Excel রিপোর্ট তৈরি করুন। ব্যাংক লোন, পার্টনারশিপ বা ট্যাক্স ফাইলিং এর জন্য প্রস্তুত।',
				tags: ['PDF', 'EXCEL', 'SYNC']
			}
		},
		cta: {
			title: '১০,০০০+ কৃষক ইতিমধ্যে দেশি ফিশারি ব্যবহার করছেন',
			testimonial: {
				quote: '"দেশি ফিশারি ব্যবহার শুরু করার পর আমার খাবারের অপচয় ৩০% কমে গেছে এবং কোন পুকুর লাভজনক তা এখন আমি জানি।"',
				name: 'রাহাত আলী',
				role: 'খামার মালিক',
				location: 'ময়মনসিংহ জেলা',
				ponds: '১২টি পুকুর, ৮ একর'
			}
		},
		pricing: {
			title: 'বিনামূল্যে। কোনো শর্ত নেই।',
			subtitle: 'সব ফিচার বিনামূল্যে। ক্রেডিট কার্ড লাগবে না। কোনো লুকানো ফি নেই। কোনো পেইড টিয়ার নেই।',
			features: [
				'আনলিমিটেড পুকুর ও স্টক রেকর্ড',
				'সম্পূর্ণ অফলাইন মোড',
				'PDF ও Excel রিপোর্ট',
				'বাংলা ও ইংরেজি',
				'ফোন ও ডেস্কটপ অ্যাক্সেস'
			],
			cta: 'বিনামূল্যে অ্যাকাউন্ট তৈরি করুন'
		},
		footer: {
			brand: 'দেশি ফিশারি',
			copyright: '\u00A9 ২০২৪ দেশি ফিশারি। বাংলাদেশের মাছ চাষিদের জন্য তৈরি।',
			quickLinks: 'দ্রুত লিংক',
			about: 'আমাদের সম্পর্কে',
			support: 'সাহায্য নিন',
			privacy: 'গোপনীয়তা নীতি',
			accessibility: 'অ্যাক্সেসিবিলিটি',
			terms: 'সেবার শর্তাবলী',
			languageToggle: 'English / বাংলা'
		}
	}
} as const;

export type TranslationKey = keyof typeof translations.en;

export function getLandingLang(): Lang {
	if (!browser) return 'en';
	const saved = localStorage.getItem(LANG_KEY);
	return saved === 'bn' || saved === 'en' ? saved : 'en';
}

export function setLandingLang(lang: Lang) {
	if (!browser) return;
	localStorage.setItem(LANG_KEY, lang);
}
