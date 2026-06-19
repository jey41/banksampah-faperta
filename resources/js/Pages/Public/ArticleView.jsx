import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function ArticleView({ article, recentArticles = [] }) {
    return (
        <>
            <Head title={`${article.title} - Bank Sampah Digital`} />
            
            <div className="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">
                {/* Header */}
                <header className="bg-white border-b border-outline-variant/30 sticky top-0 z-50 shadow-sm">
                    <div className="flex justify-between items-center w-full px-lg md:px-xl max-w-container-max mx-auto h-16">
                        <Link href="/" className="text-[20px] font-bold text-primary flex items-center gap-xs">
                            <span className="material-symbols-outlined text-[28px]">arrow_back</span>
                            Artikel Edukasi
                        </Link>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-grow py-xl px-lg max-w-container-max mx-auto w-full grid grid-cols-1 lg:grid-cols-3 gap-2xl">
                    {/* Article Detail */}
                    <div className="lg:col-span-2 space-y-lg bg-white border border-outline-variant/30 rounded-3xl p-xl shadow-sm">
                        <div className="space-y-sm">
                            <span className="inline-block bg-primary-container text-on-primary-container text-xs font-bold px-md py-xs rounded-full">
                                Edukasi Lingkungan
                            </span>
                            <h1 className="text-[28px] md:text-[36px] font-bold text-primary leading-tight">
                                {article.title}
                            </h1>
                            <p className="text-xs text-on-surface-variant">
                                Dipublikasikan pada {new Date(article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                            </p>
                        </div>

                        {article.image_url && (
                            <div className="rounded-2xl overflow-hidden max-h-[400px]">
                                <img
                                    alt={article.title}
                                    className="w-full h-full object-cover"
                                    src={article.image_url}
                                />
                            </div>
                        )}

                        <div className="text-[15px] leading-relaxed text-on-surface space-y-md whitespace-pre-line">
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
                                            className="block py-md group first:pt-0 last:pb-0"
                                        >
                                            <h4 className="text-[14px] font-bold text-on-surface group-hover:text-primary leading-snug line-clamp-2 transition-colors">
                                                {ra.title}
                                            </h4>
                                            <p className="text-[11px] text-on-surface-variant mt-xs">
                                                {new Date(ra.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                            </p>
                                        </Link>
                                    ))
                                ) : (
                                    <p className="text-sm text-on-surface-variant py-sm">Tidak ada artikel lain.</p>
                                )}
                            </div>
                        </div>

                        <div className="bg-primary-container text-on-primary rounded-3xl p-lg shadow-sm space-y-sm">
                            <h4 className="text-[16px] font-bold text-white">Ingin mulai menabung sampah?</h4>
                            <p className="text-[13px] text-on-primary-container leading-relaxed">
                                Daur ulang sampah anorganik Anda dan saksikan tabungan Anda bertumbuh bersama Bank Sampah Digital!
                            </p>
                            <Link
                                href="/register"
                                className="inline-block bg-white text-primary font-bold text-[12px] px-lg py-sm rounded-full shadow-sm hover:bg-opacity-90 transition-all"
                            >
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="bg-white border-t border-outline-variant/20 text-on-surface-variant py-md text-center text-xs mt-auto">
                    <p>© 2026 Bank Sampah Digital. Mengelola Sampah, Menabung Kebaikan.</p>
                </footer>
            </div>
        </>
    );
}
