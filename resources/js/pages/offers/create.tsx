import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/react';
import Select from 'react-select';
import { useState, /* useEffect */ } from 'react';
import { ChevronDown, ChevronUp } from 'lucide-react';


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
        title: '',
        description: '',
        company_id: '',
        reward_total_eur: 0,
        reward_offerer_percent: 0,
        offerer_type: 'referrer',
    });

    const [selectedCompany, setSelectedCompany] = useState<{ value: string; label: string } | null>(null);
    // const [isDark, setIsDark] = useState(false);

    { /*
    useEffect(() => {
        setIsDark(document.documentElement.classList.contains('dark'));
    }, []);
    */ }

    const companyOptions = companies.map(company => ({
        value: company.id.toString(),
        label: company.name
    }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/offers', {
            ...data,
            reward_total_cents: Math.round((data.reward_total_eur || 0) * 100),
            reward_offerer_percent: (data.reward_offerer_percent || 0) / 100,
            offerer_type: data.offerer_type,
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

    // const selectClassNames = {
    //     control: (base) => ({
    //         ...base,
    //         'mt-1 bg-zinc-100 dark:bg-zinc-800'
    //     }),
    // };

    const selectClassNames = {
        control: () => 'mt-1 rounded-md bg-zinc-100 dark:bg-zinc-800 border-none shadow-none min-h-[36px]',
        menu: () => 'rounded-md bg-white dark:bg-zinc-800 mt-1 shadow-lg',
        option: ({ isFocused, isSelected }) =>
          `cursor-pointer px-4 py-2 ${isSelected ? 'bg-sky-100 dark:bg-zinc-700 text-sky-900' : isFocused ? 'bg-zinc-200 dark:bg-zinc-700' : ''}`,
        singleValue: () => 'text-gray-900 dark:text-gray-100',
        input: () => 'text-gray-900 dark:text-gray-100',
        placeholder: () => 'text-gray-400 dark:text-gray-400',
      };

    // const selectStyles = {
    //     control: (base) => ({
    //         ...base,
    //         backgroundColor: isDark ? 'rgb(39 39 42)' : 'rgb(243, 244, 246)', // '#fff', // dark:bg-zinc-800, light:bg-white
    //         border: 'none',
    //         borderColor: isDark ? 'rgb(63 63 70)' : '#d1d5db', // dark:border-zinc-700, light:border-gray-300
    //         color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
    //         '&:hover': {
    //             borderColor: isDark ? 'rgb(82 82 91)' : '#a1a1aa', // dark:hover:border-zinc-600, light:hover:border-gray-400
    //         },
    //         boxShadow: 'none',
    //     }),
    //     menu: (base) => ({
    //         ...base,
    //         backgroundColor: isDark ? 'rgb(39 39 42)' : '#fff', // dark:bg-zinc-800, light:bg-white
    //         color: isDark ? 'rgb(209 213 219)' : '#111827',
    //     }),
    //     option: (base, state) => ({
    //         ...base,
    //         backgroundColor: state.isFocused
    //             ? (isDark ? 'oklch(0.588 0.158 241.966)' : '#f3f4f6') // dark:bg-zinc-700, light:bg-gray-100
    //             : (isDark ? 'oklch(0.2739 0.0055 286.03)' : '#fff'), // dark:bg-zinc-800, light:bg-white
    //         color: isDark ? 'oklch(0.9276 0.0058 264.53)' : '#111827', // dark:text-gray-300, light:text-gray-900
    //         '&:active': {
    //             backgroundColor: isDark ? 'rgb(82 82 91)' : '#e5e7eb', // dark:active:bg-zinc-600, light:active:bg-gray-200
    //         },
    //     }),
    //     singleValue: (base) => ({
    //         ...base,
    //         color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
    //     }),
    //     input: (base) => ({
    //         ...base,
    //         color: isDark ? 'rgb(209 213 219)' : '#111827', // dark:text-gray-300, light:text-gray-900
    //     }),
    //     placeholder: (base) => ({
    //         ...base,
    //         color: isDark ? 'rgb(156 163 175)' : '#6b7280', // dark:text-gray-400, light:text-gray-500
    //     }),
    // };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Offer" />
            <div className="p-5">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        {/* <div className="p-1"> */}
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    { /* ########## OFFERED BY TYPE ########## */ }
                                    <label className="block text-sm font-medium mb-2">
                                        Du bist...
                                    </label>
                                    <div className="space-y-2">
                                        <div
                                            className={`relative flex cursor-pointer rounded-lg border bg-white dark:bg-zinc-800 px-5 py-4 shadow-sm focus:outline-none ${
                                                data.offerer_type === 'referrer'
                                                    ? 'border-[var(--accent)] ring-2 ring-[var(--accent)]'
                                                    : 'border-zinc-800 hover:border-gray-300 transition-all duration-300'
                                            }`}
                                            onClick={() => setData('offerer_type', 'referrer')}
                                            tabIndex={0}
                                            onKeyDown={e => { if (e.key === 'Enter' || e.key === ' ') setData('offerer_type', 'referrer') }}
                                            role="radio"
                                            aria-checked={data.offerer_type === 'referrer'}
                                        >
                                            <span className="flex h-5 items-center">
                                                <span
                                                    className={`inline-block h-4 w-4 rounded-full border-2 ${
                                                        data.offerer_type === 'referrer'
                                                            ? 'border-[var(--accent)] bg-[var(--accent)]'
                                                            : 'border-gray-300 bg-white'
                                                    }`}
                                                />
                                            </span>
                                            <span className="ml-3 flex flex-col">
                                                <span className="block text-sm font-medium">
                                                    Werbender
                                                </span>
                                                <span className="block text-xs">
                                                    Du hast einen Account und möchtest jemanden werben.
                                                </span>
                                            </span>
                                        </div>
                                        <div
                                            className={`relative flex cursor-pointer rounded-lg border bg-white dark:bg-zinc-800 px-5 py-4 shadow-sm focus:outline-none ${
                                                data.offerer_type === 'referred'
                                                    ? 'border-[var(--accent)] ring-2 ring-[var(--accent)]'
                                                    : 'border-zinc-800 hover:border-gray-300 transition-all duration-300'
                                            }`}
                                            onClick={() => setData('offerer_type', 'referred')}
                                            tabIndex={0}
                                            onKeyDown={e => { if (e.key === 'Enter' || e.key === ' ') setData('offerer_type', 'referred') }}
                                            role="radio"
                                            aria-checked={data.offerer_type === 'referred'}
                                        >
                                            <span className="flex h-5 items-center">
                                                <span
                                                    className={`inline-block h-4 w-4 rounded-full border-2 ${
                                                        data.offerer_type === 'referred'
                                                            ? 'border-[var(--accent)] bg-[var(--accent)]'
                                                            : 'border-gray-300 bg-white'
                                                    }`}
                                                />
                                            </span>
                                            <span className="ml-3 flex flex-col">
                                                <span className="block text-sm font-medium">
                                                    Beworbener
                                                </span>
                                                <span className="block text-xs">
                                                    Du hast noch keinen Account und möchtest von jemandem beworben werden.
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    {errors.offerer_type && (
                                        <p className="mt-1 text-sm text-red-600">{errors.offerer_type}</p>
                                    )}
                                </div>
                                <div>
                                    { /* ########## TITLE ########## */ }
                                    <label htmlFor="title" className="block text-sm font-medium">
                                        Titel
                                    </label>
                                    <input
                                        type="text"
                                        id="title"
                                        value={data.title}
                                        onChange={e => setData('title', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-900 shadow-sm bg-zinc-100 dark:bg-zinc-800 h-9 p-2 autofill:!bg-gray-800 dark:autofill:!bg-zinc-800"
                                        required
                                        placeholder="Unter welchem Titel soll dein Angebot angezeigt werden?"
                                    />
                                    {errors.title && (
                                        <p className="mt-1 text-sm text-red-600">{errors.title}</p>
                                    )}
                                </div>
                                <div>
                                    { /* ########## DESCRIPTION ########## */ }
                                    <label htmlFor="description" className="block text-sm font-medium">
                                        Beschreibung
                                    </label>
                                    <textarea
                                        id="description"
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        rows={4}
                                        className="mt-1 block w-full rounded-md shadow-sm bg-zinc-100 dark:bg-zinc-800 dark:border-gray-600 p-2"
                                        required
                                        placeholder="Beschreibe dein Angebot"
                                    />
                                    {errors.description && (
                                        <p className="mt-1 text-sm text-red-600">{errors.description}</p>
                                    )}
                                </div>
                                <div>
                                    { /* ########## COMPANY ########## */ }
                                    <label htmlFor="company" className="block text-sm font-medium">
                                        Anbieter
                                    </label>
                                    <Select
                                        id="company"
                                        value={selectedCompany}
                                        onChange={(option) => {
                                            setSelectedCompany(option);
                                            setData('company_id', option?.value || '');
                                        }}
                                        options={companyOptions}
                                        classNames= {selectClassNames} // 'mt-1'
                                        classNamePrefix="select"
                                        isClearable
                                        required
                                        // styles={selectStyles}
                                    />
                                    {errors.company_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.company_id}</p>
                                    )}
                                </div>
                                <div>
                                    { /* ########## REWARD ########## */ }
                                    <label htmlFor="reward_total_eur" className="block text-sm font-medium">
                                        Reward
                                    </label>
                                    <div className="relative mt-1 flex rounded-md shadow-sm">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 border-r border-gray-300 dark:border-zinc-600 pr-2 w-14">
                                            <span className="text-zinc-400 sm:text-sm">EUR</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="reward_total_eur"
                                            min="0"
                                            max="100000"
                                            step="0.01"
                                            value={data.reward_total_eur}
                                            onChange={e => setData('reward_total_eur', parseFloat(e.target.value) || 0)}
                                            className="block w-full rounded-md border-gray-300 pl-16 pr-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100 dark:bg-zinc-800 dark:text-gray-300 h-9"
                                            required
                                            placeholder="0.00"
                                        />
                                        <div className="absolute right-1 top-1 flex flex-col h-7 justify-center">
                                            <button
                                                type="button"
                                                tabIndex={-1}
                                                className="bg-zinc-100 dark:bg-zinc-800 text-gray-900 dark:text-gray-300 rounded-t w-7 h-3 flex items-center justify-center"
                                                onMouseDown={e => { e.preventDefault(); setData('reward_total_eur', Math.round((data.reward_total_eur + 1) * 100) / 100); }}
                                            >
                                            <ChevronUp className="hover:text-[var(--accent)] dark:hover:text-gray-400 flex-none w-5 h-5 mr-1" />
                                            </button>
                                            <button
                                                type="button"
                                                tabIndex={-1}
                                                className="bg-zinc-100 dark:bg-zinc-800 text-gray-900 dark:text-gray-300 rounded-t w-7 h-3 flex items-center justify-center"
                                                onMouseDown={e => { e.preventDefault(); setData('reward_total_eur', Math.max(0, Math.round((data.reward_total_eur - 1) * 100) / 100)); }}
                                            >
                                            <ChevronDown className="hover:text-[var(--accent)] dark:hover:text-gray-400 flex-none w-5 h-5 mr-1" />
                                            </button>
                                        </div>
                                    </div>
                                    {errors.reward_total_eur && (
                                        <p className="mt-1 text-sm text-red-600">{errors.reward_total_eur}</p>
                                    )}
                                </div>
                                <div>
                                    { /* ########## REWARD OFFERER PERCENT ########## */ }
                                    <label htmlFor="reward_offerer_percent" className="block text-sm font-medium">
                                        Dein einbehaltener Anteil
                                    </label>
                                    <div className="relative mt-1 flex rounded-md shadow-sm">
                                        { /* ##### % Zeichen ##### */ }
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 border-r border-gray-300 dark:border-zinc-600 pr-2 w-14">
                                            <span className="text-zinc-400 sm:text-sm">%</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="reward_offerer_percent"
                                            min="0"
                                            max="100"
                                            step="1"
                                            value={data.reward_offerer_percent}
                                            onChange={e => setData('reward_offerer_percent', parseInt(e.target.value) || 0)}
                                            className="block w-full rounded-md pl-16 pr-10 shadow-sm bg-zinc-100 dark:bg-zinc-800 dark:text-gray-300 h-9"
                                            required
                                            placeholder="z.B. 20"
                                        />
                                        <div className="absolute right-1 top-1 flex flex-col h-7 justify-center">
                                            <button
                                                type="button"
                                                tabIndex={-1}
                                                className="bg-zinc-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-300 rounded-t w-7 h-3 flex items-center justify-center"
                                                onMouseDown={e => { e.preventDefault(); setData('reward_offerer_percent', Math.min(100, data.reward_offerer_percent + 1)); }}
                                            >
                                            <ChevronUp className="hover:text-[var(--accent)] dark:hover:text-gray-400 flex-none w-5 h-5 mr-1" />
                                            </button>
                                            <button
                                                type="button"
                                                tabIndex={-1}
                                                className="bg-zinc-100 dark:bg-zinc-800 text-gray-900 dark:text-gray-300 rounded-b w-7 h-3 flex items-center justify-center"
                                                onMouseDown={e => { e.preventDefault(); setData('reward_offerer_percent', Math.max(0, data.reward_offerer_percent - 1)); }}
                                            >
                                            <ChevronDown className="hover:text-[var(--accent)] dark:hover:text-gray-400 flex-none w-5 h-5 mr-1" />
                                            </button>
                                        </div>
                                    </div>
                                    {errors.reward_offerer_percent && (
                                        <p className="mt-1 text-sm text-red-600">{errors.reward_offerer_percent}</p>
                                    )}
                                </div>

                                <div className="flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex justify-center rounded-md border border-transparent bg-sky-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 cursor-pointer"
                                    >
                                        {processing ? 'Creating...' : 'Create Offer'}
                                    </button>
                                </div>
                            </form>
                    </div>
            </div>
        </AppLayout>
    );
}
