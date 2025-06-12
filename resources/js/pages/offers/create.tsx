import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import Select from 'react-select';
import { useState, useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Offers',
        href: '/offers',
    },
    {
        title: 'Create Offer',
        href: '/offers/create',
    },
];

type Company = {
    id: number;
    name: string;
};

export default function Create({ companies }: { companies: Company[] }) {
    const { data, setData, processing, errors } = useForm({
        offer_title: '',
        offer_description: '',
        company_id: '',
        reward_total_eur: 0,
        reward_offerer_percent: 0,
    });

    const [selectedCompany, setSelectedCompany] = useState<{ value: string; label: string } | null>(null);
    const [isDark, setIsDark] = useState(false);

    useEffect(() => {
        setIsDark(document.documentElement.classList.contains('dark'));
    }, []);

    const companyOptions = companies.map(company => ({
        value: company.id.toString(),
        label: company.name
    }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        Inertia.post('/offers', {
            ...data,
            reward_total_cents: Math.round((data.reward_total_eur || 0) * 100),
            reward_offerer_percent: (data.reward_offerer_percent || 0) / 100,
        }, {
            onSuccess: () => {
                router.visit('/offers');
            },
        });
    };

    // const formatCurrency = (value: number) => {
    //     // const number = parseFloat(value);
    //     // if (isNaN(number)) return '';
    //     return (value / 100).toFixed(2);
    // };

    // const parseCurrency = (value: string) => {
    //     const number = parseFloat(value);
    //     if (isNaN(number)) return '';
    //     return Math.round(number * 100).toString();
    // };

    // const formatPercent = (value: number) => {
    //     // const number = parseFloat(value);
    //     if (isNaN(value)) return '';
    //     return (value * 100).toFixed(0);
    // };

    // const parsePercent = (value: string) => {
    //     const number = parseFloat(value);
    //     if (isNaN(number)) return '';
    //     return Math.round(number / 100).toString();
    // };

    const selectStyles = {
        control: (base) => ({
            ...base,
            backgroundColor: isDark ? 'rgb(39 39 42)' : '#fff', // dark:bg-zinc-800, light:bg-white
            borderColor: isDark ? 'rgb(63 63 70)' : '#d1d5db', // dark:border-zinc-700, light:border-gray-300
            color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
            '&:hover': {
                borderColor: isDark ? 'rgb(82 82 91)' : '#a1a1aa', // dark:hover:border-zinc-600, light:hover:border-gray-400
            },
            boxShadow: 'none',
        }),
        menu: (base) => ({
            ...base,
            backgroundColor: isDark ? 'rgb(39 39 42)' : '#fff', // dark:bg-zinc-800, light:bg-white
            color: isDark ? 'rgb(209 213 219)' : '#111827',
        }),
        option: (base, state) => ({
            ...base,
            backgroundColor: state.isFocused
                ? (isDark ? 'rgb(63 63 70)' : '#f3f4f6') // dark:bg-zinc-700, light:bg-gray-100
                : (isDark ? 'rgb(39 39 42)' : '#fff'), // dark:bg-zinc-800, light:bg-white
            color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
            '&:active': {
                backgroundColor: isDark ? 'rgb(82 82 91)' : '#e5e7eb', // dark:active:bg-zinc-600, light:active:bg-gray-200
            },
        }),
        singleValue: (base) => ({
            ...base,
            color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
        }),
        input: (base) => ({
            ...base,
            color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
        }),
        placeholder: (base) => ({
            ...base,
            color: isDark ? 'rgb(156 163 175)' : '#6b7280', // dark:text-gray-400, light:text-gray-500
        }),
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Offer" />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-white/10 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <label htmlFor="offer_title" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Title
                                    </label>
                                    <input
                                        type="text"
                                        id="offer_title"
                                        value={data.offer_title}
                                        onChange={e => setData('offer_title', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-gray-600 h-9"
                                        required
                                    />
                                    {errors.offer_title && (
                                        <p className="mt-1 text-sm text-red-600">{errors.offer_title}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="offer_description" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Description
                                    </label>
                                    <textarea
                                        id="offer_description"
                                        value={data.offer_description}
                                        onChange={e => setData('offer_description', e.target.value)}
                                        rows={4}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-gray-600"
                                        required
                                    />
                                    {errors.offer_description && (
                                        <p className="mt-1 text-sm text-red-600">{errors.offer_description}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="company" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Company
                                    </label>
                                    <Select
                                        id="company"
                                        value={selectedCompany}
                                        onChange={(option) => {
                                            setSelectedCompany(option);
                                            setData('company_id', option?.value || '');
                                        }}
                                        options={companyOptions}
                                        className="mt-1"
                                        classNamePrefix="select"
                                        isClearable
                                        required
                                        styles={selectStyles}
                                    />
                                    {errors.company_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.company_id}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="reward_total_eur" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reward
                                    </label>
                                    <div className="relative mt-1">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span className="text-gray-500 dark:text-gray-400 sm:text-sm">EUR</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="reward_total_eur"
                                            min="0"
                                            max="100000"
                                            step="0.01"
                                            value={data.reward_total_eur}
                                            onChange={e => setData('reward_total_eur', parseFloat(e.target.value) || 0)}
                                            className="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 h-9"
                                            required
                                        />
                                    </div>
                                    {errors.reward_total_eur && (
                                        <p className="mt-1 text-sm text-red-600">{errors.reward_total_eur}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="reward_offerer_percent" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Dein einbehaltener Anteil
                                    </label>
                                    <div className="relative mt-1">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span className="text-gray-500 dark:text-gray-400 sm:text-sm">%</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="reward_offerer_percent"
                                            min="0"
                                            max="100"
                                            step="1"
                                            value={data.reward_offerer_percent}
                                            onChange={e => setData('reward_offerer_percent', parseInt(e.target.value) || 0)}
                                            className="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 h-9"
                                            required
                                        />
                                    </div>
                                    {errors.reward_offerer_percent && (
                                        <p className="mt-1 text-sm text-red-600">{errors.reward_offerer_percent}</p>
                                    )}
                                </div>

                                <div className="flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                    >
                                        {processing ? 'Creating...' : 'Create Offer'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
