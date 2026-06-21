import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import { motion } from 'framer-motion';

export default function ArticleDirectory({ articles = {} }) {
    const articleList = articles.data || [];
    const paginationLinks = articles.links || [];

    const containerVariants = {
        hidden: {},
        visible: {
            transition: {
                staggerChildren: 0.1
            }
        }
    };

    const cardVariants = {
        hidden: { opacity: 0, y: 30 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.6, ease: "easeOut" }
        }
    };

    return (
        <>
            <Head title="Artikel &amp; Edukasi Lingkungan - Bank Sampah Faperta Unmul" />
            
            <div className="py-2xl bg-background min-h-screen">
                <main className="max-w-container-max mx-auto px-lg md:px-xl w-full">
                    {/* Title Header */}
                    <div className="text-center mb-2xl space-y-sm">
                        <h1 className="text-[32px] md:text-[40px] font-bold text-primary">Artikel &amp; Edukasi</h1>
                        <p className="text-on-surface-variant max-w-2xl mx-auto text-[14px] md:text-[16px] leading-relaxed">
                            Tips praktis, panduan pemilihan, dan kabar terbaru seputar pengelolaan sampah mandiri demi lestarinya lingkungan kita.
                        </p>
                    </div>

                    {/* Article Grid */}
                    {articleList.length > 0 ? (
                        <>
                            <motion.div 
                                variants={containerVariants}
                                initial="hidden"
                                animate="visible"
                                className="grid grid-cols-1 md:grid-cols-3 gap-lg max-w-6xl mx-auto"
                            >
                                {articleList.map((art) => (
                                    <motion.article 
                                        key={art.id} 
                                        variants={cardVariants}
                                        whileHover={{ y: -8, scale: 1.01, boxShadow: "0 10px 25px -5px rgba(0,0,0,0.08)" }}
                                        className="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm flex flex-col transition-all duration-300"
                                    >
                                        <div className="h-48 overflow-hidden bg-primary/5 relative">
                                            {art.image_url ? (
                                                <img 
                                                    alt={art.title} 
                                                    className="w-full h-full object-cover" 
                                                    src={art.image_url} 
                                                />
                                            ) : (
                                                <div className="absolute inset-0 flex items-center justify-center text-primary/40">
                                                    <span className="material-symbols-outlined text-[64px]" style={{ fontVariationSettings: "'FILL' 0" }}>photo</span>
                                                </div>
                                            )}
                                        </div>
                                        <div className="p-lg flex-grow flex flex-col justify-between gap-md">
                                            <div className="space-y-sm">
                                                <h3 className="text-[16px] font-bold text-on-surface line-clamp-2 leading-snug hover:text-primary transition-colors">
                                                    {art.title}
                                                </h3>
                                                <p className="text-[13px] text-on-surface-variant line-clamp-3 leading-relaxed">
                                                    {art.content}
                                                </p>
                                            </div>
                                            <Link
                                                href={`/artikel/${art.slug}`}
                                                className="text-[13px] font-bold text-primary hover:text-secondary flex items-center gap-xs mt-sm self-start transition-colors no-underline"
                                            >
                                                Baca Selengkapnya
                                                <span className="material-symbols-outlined text-[16px]">arrow_forward</span>
                                            </Link>
                                        </div>
                                    </motion.article>
                                ))}
                            </motion.div>

                            {/* Pagination links */}
                            {paginationLinks.length > 3 && (
                                <div className="flex justify-center items-center gap-xs mt-2xl flex-wrap">
                                    {paginationLinks.map((link, idx) => {
                                        if (!link.url) {
                                            return (
                                                <span 
                                                    key={idx} 
                                                    className="px-lg py-sm text-on-surface-variant/40 text-[13px] font-bold select-none cursor-default"
                                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                                />
                                            );
                                        }
                                        return (
                                            <Link
                                                key={idx}
                                                href={link.url}
                                                className={`px-lg py-sm rounded-full text-[13px] font-bold transition-all no-underline ${
                                                    link.active
                                                        ? 'bg-primary text-white shadow-md'
                                                        : 'bg-white text-on-surface-variant hover:bg-surface-container-low border border-outline-variant/30'
                                                }`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        );
                                    })}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-xl text-on-surface-variant font-medium">
                            Belum ada artikel edukasi yang dipublikasikan saat ini.
                        </div>
                    )}
                </main>
            </div>
        </>
    );
}

ArticleDirectory.layout = (page) => <PublicLayout children={page} />;
