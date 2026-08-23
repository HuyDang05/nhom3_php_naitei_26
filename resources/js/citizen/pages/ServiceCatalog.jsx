import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { fetchServices, fetchCategories } from '../api/services';
import Header from '../components/Header';
import Footer from '../components/Footer';
import { useLanguage } from '../i18n/LanguageContext';
import { localizeCategory, localizeService } from '../i18n/content';
import { formatFee } from '../utils/format';

const SERVICES_PER_PAGE = 6;

export default function ServiceCatalog() {
    const { language, locale, t } = useLanguage();
    const [searchParams] = useSearchParams();
    const [services, setServices] = useState([]);
    const [categories, setCategories] = useState([]);
    const [meta, setMeta] = useState(null);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState(() => searchParams.get('search') ?? '');
    const [debouncedSearch, setDebouncedSearch] = useState(() => searchParams.get('search') ?? '');

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
            setCurrentPage(1);
        }, 300);
        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        let isMounted = true;
        fetchCategories()
            .then((res) => {
                if (!isMounted) {
                    return;
                }

                const raw = res.data?.data;
                const list = Array.isArray(raw)
                    ? raw
                    : Array.isArray(raw?.data)
                        ? raw.data
                        : [];

                setCategories(list);
            })
            .catch(() => {
                if (isMounted) {
                    setCategories([]);
                }
            });
        return () => { isMounted = false; };
    }, []);

    useEffect(() => {
        let isMounted = true;
        setLoading(true);
        fetchServices({
            search: debouncedSearch,
            category_id: selectedCategory?.id || '',
            page: currentPage,
            per_page: SERVICES_PER_PAGE,
        })
            .then((res) => {
                if (isMounted) {
                    const payload = res.data?.data;
                    const list = Array.isArray(payload) ? payload : (payload?.data ?? []);
                    setServices(Array.isArray(list) ? list : []);
                    setMeta(payload?.meta ?? null);
                    setLoading(false);
                }
            })
            .catch(() => {
                if (isMounted) {
                    setServices([]);
                    setMeta(null);
                    setLoading(false);
                }
            });
        return () => {
            isMounted = false;
        };
    }, [currentPage, debouncedSearch, selectedCategory]);

    const lastPage = meta?.last_page ?? 1;

    function selectCategory(category) {
        setSelectedCategory(category);
        setCurrentPage(1);
    }

    function changePage(page) {
        if (page < 1 || page > lastPage || page === currentPage) {
            return;
        }

        setCurrentPage(page);
    }

    return (
        <main className="min-h-screen bg-[#F9FAFB] flex flex-col font-sans">
            <Header />

            <div className="mx-auto flex w-full max-w-[1280px] flex-1 flex-col border-x border-gray-200 bg-white">
                <div className="px-10 py-6">
                    <div className="flex items-center bg-gray-100 border-2 border-gray-200 rounded-xl px-4">
                        <svg className="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input
                            type="text"
                            placeholder={t('services.searchPlaceholder')}
                            className="w-full h-[51px] bg-transparent outline-none text-lg text-gray-900 placeholder-gray-400"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                </div>
                
                <div className="flex flex-1 px-10">
                    <aside className="hidden w-[280px] shrink-0 border-r-2 border-gray-200 py-8 pr-6 md:block lg:w-[320px]">
                        <h3 className="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-4">{t('services.categories')}</h3>
                        <div className="flex max-h-[29.75rem] flex-col gap-1 overflow-y-auto overscroll-contain pr-2">
                            <button
                                onClick={() => selectCategory(null)}
                                className={`flex h-14 shrink-0 items-center gap-3 rounded-xl px-4 text-left text-[15px] font-medium transition ${!selectedCategory ? 'bg-[#E8F0FE] text-blue-600' : 'text-gray-700 hover:bg-gray-50'}`}
                            >
                                <svg className="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <span className="leading-5">{t('services.allCategories')}</span>
                            </button>
                            {(Array.isArray(categories) ? categories : []).map((category) => localizeCategory(category, language)).map(cat => (
                                <button
                                    key={cat.id}
                                    onClick={() => selectCategory(cat)}
                                    className={`flex h-14 shrink-0 items-center gap-3 rounded-xl px-4 text-left text-[15px] font-medium transition ${selectedCategory?.id === cat.id ? 'bg-[#E8F0FE] text-blue-600' : 'text-gray-700 hover:bg-gray-50'}`}
                                >
                                    <svg className="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span className="leading-5">{cat.name}</span>
                                </button>
                            ))}
                        </div>
                    </aside>
                    
                    <div className="flex-1 md:pl-8 py-8">
                        <div className="flex items-center gap-3 mb-8">
                            <div className="w-11 h-11 bg-[#E8F0FE] text-blue-600 rounded-xl flex items-center justify-center">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h2 className="text-[26px] font-bold text-gray-900">{selectedCategory ? selectedCategory.name : t('services.all')}</h2>
                            <span className="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[13px] font-semibold">{t('services.count', { count: meta?.total ?? services.length })}</span>
                        </div>

                        {loading ? (
                            <div className="py-10 text-center text-gray-500">{t('common.loading')}</div>
                        ) : services.length === 0 ? (
                            <div className="py-10 text-center text-gray-500">{t('services.empty')}</div>
                        ) : (
                            <div className="border-[1.5px] border-gray-200 rounded-2xl bg-white overflow-hidden">
                                {services.map((item) => localizeService(item, language)).map((service, index) => (
                                    <Link key={service.id} to={`/services/${service.id}`} className={`flex flex-col md:flex-row md:items-center justify-between p-5 md:p-6 gap-4 md:gap-0 hover:bg-gray-50 transition group cursor-pointer ${index !== 0 ? 'border-t-[1.5px] border-gray-100' : ''}`}>
                                        <div className="flex-1 pr-6">
                                            <h4 className="text-[17px] font-semibold text-gray-900 mb-1 group-hover:text-blue-600 transition">{service.name}</h4>
                                            <p className="text-[15px] text-gray-500">{service.description || t('services.noDescription')}</p>
                                        </div>
                                        <div className="flex items-center gap-6 md:gap-0">
                                            <div className="w-auto md:w-[110px] md:text-right flex flex-col justify-center">
                                                <span className="text-[13px] font-semibold text-gray-400 uppercase tracking-widest mb-1">{t('services.processing')}</span>
                                                <span className="text-[15px] font-semibold text-gray-700">{t('services.days', { count: service.processing_time_days })}</span>
                                            </div>
                                            <div className="w-auto md:w-[80px] md:text-right flex flex-col justify-center md:ml-6 md:mr-6">
                                                <span className="text-[13px] font-semibold text-gray-400 uppercase tracking-widest mb-1">{t('services.fee')}</span>
                                                <span className="text-[15px] font-semibold text-gray-700">{formatFee(service.fee, locale, t('common.free'))}</span>
                                            </div>
                                            <div 
                                                className="hidden md:flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl group-hover:bg-blue-700 transition"
                                            >
                                                <span className="text-[15px] font-semibold">{t('services.apply')}</span>
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                                            </div>
                                        </div>
                                        <div 
                                            className="md:hidden flex items-center justify-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl group-hover:bg-blue-700 transition w-full mt-2"
                                        >
                                            <span className="text-[15px] font-semibold">{t('services.apply')}</span>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}

                        {!loading && services.length > 0 && lastPage > 1 && (
                            <nav className="mt-7 flex items-center justify-center gap-2" aria-label={t('services.pagination')}>
                                <button
                                    type="button"
                                    disabled={currentPage <= 1}
                                    className="btn-secondary rounded-xl px-4 py-2.5 text-sm disabled:cursor-not-allowed disabled:opacity-40"
                                    onClick={() => changePage(currentPage - 1)}
                                >
                                    {t('services.previousPage')}
                                </button>

                                {Array.from({ length: lastPage }, (_, index) => index + 1).map((page) => (
                                    <button
                                        key={page}
                                        type="button"
                                        aria-current={page === currentPage ? 'page' : undefined}
                                        className={`h-10 min-w-10 rounded-xl px-3 text-sm font-semibold transition ${page === currentPage ? 'bg-blue-600 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-600 hover:border-blue-300 hover:text-blue-600'}`}
                                        onClick={() => changePage(page)}
                                    >
                                        {page}
                                    </button>
                                ))}

                                <button
                                    type="button"
                                    disabled={currentPage >= lastPage}
                                    className="btn-secondary rounded-xl px-4 py-2.5 text-sm disabled:cursor-not-allowed disabled:opacity-40"
                                    onClick={() => changePage(currentPage + 1)}
                                >
                                    {t('services.nextPage')}
                                </button>
                            </nav>
                        )}
                    </div>
                </div>
                
                <Footer />
            </div>
        </main>
    );
}
