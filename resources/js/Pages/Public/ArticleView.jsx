import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { motion } from 'framer-motion';

export default function ArticleView({ article, recentArticles = [] }) {
    return (
        <>
            <Head title={`${article.title} - Bank Sampah Faperta Unmul`} />
            
            <div className="py-xl bg-background min-h-screen">
                <main className="max-w-container-max mx-auto px-lg md:px-xl w-full">
                    {/* Breadcrumbs */}
                    <div className="mb-lg">
                        <Link 
                            href="/artikel" 
                            className="inline-flex items-center gap-xs text-[13px] font-bold text-primary hover:text-secondary transition-colors no-underline"
                        >
                            <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali ke Daftar Artikel
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-xl items-start">
                        {/* Article Detail */}
                        <div className="lg:col-span-2 space-y-lg bg-white border border-outline-variant/30 rounded-3xl p-xl shadow-sm">
                            <div className="space-y-sm">
                                <span className="inline-block bg-primary-container text-on-primary text-xs font-bold px-md py-xs rounded-full">
                                    Edukasi Lingkungan
                                </span>
                                <h1 className="text-[28px] md:text-[36px] font-bold text-primary leading-tight">
                                    {article.title}
                                </h1>
                                <p className="text-xs text-on-surface-variant font-medium">
                                    Dipublikasikan pada {new Date(article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                </p>
                            </div>

                            {article.image_url && (
                                <div className="rounded-2xl overflow-hidden max-h-[400px] shadow-sm">
                                    <img
                                        alt={article.title}
                                        className="w-full h-full object-cover"
                                        src={article.image_url}
                                    />
                                </div>
                            )}

                            <div className="text-[15px] leading-relaxed text-on-surface space-y-md whitespace-pre-line font-normal">
                                {article.content}
                            </div>
                        </div>

                        {/* Sidebar / Recent Articles */}
                        <div className="space-y-lg">
                            <div className="bg-white border border-outline-variant/30 rounded-3xl p-lg shadow-sm">
                                <h3 className="text-[18px] font-bold text-primary mb-md pb-xs border-b border-outline-variant/20">
                                    Artikel Terbaru
                                </h3>
                                <div className="divide-y divide-outline-variant/10">
                                    {recentArticles.length > 0 ? (
                                        recentArticles.map((ra) => (
                                            <Link
                                                key={ra.id}
                                                href={`/artikel/${ra.slug}`}
                                                className="block py-md group first:pt-0 last:pb-0 no-underline"
                                            >
                                                <h4 className="text-[14px] font-bold text-on-surface group-hover:text-primary leading-snug line-clamp-2 transition-colors">
                                                    {ra.title}
                                                </h4>
                                                <p className="text-[11px] text-on-surface-variant mt-xs font-medium">
                                                    {new Date(ra.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </p>
                                            </Link>
                                        ))
                                    ) : (
                                        <p className="text-sm text-on-surface-variant py-sm">Tidak ada artikel lain.</p>
                                    )}
                                </div>
                            </div>

                            {/* Sidebar CTA Card */}
                            <div className="bg-gradient-to-br from-primary-container to-primary text-white rounded-3xl p-lg shadow-md space-y-sm">
                                <h4 className="text-[16px] font-bold text-white">Ingin mulai menabung sampah?</h4>
                                <p className="text-[13px] text-white/90 leading-relaxed font-medium">
                                    Daur ulang sampah anorganik Anda dan saksikan tabungan Anda bertumbuh bersama Bank Sampah Faperta Unmul!
                                </p>
                                <button
                                    onClick={() => window.dispatchEvent(new CustomEvent('open-register'))}
                                    className="inline-block bg-white text-primary font-bold text-[12px] px-lg py-sm rounded-full shadow-sm hover:bg-opacity-90 transition-all border-0 cursor-pointer"
                                >
                                    Daftar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}

ArticleView.layout = (page) => <PublicLayout children={page} />;
