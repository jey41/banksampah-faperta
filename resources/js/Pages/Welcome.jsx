import React, { useState, useEffect, useRef } from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import PublicLayout from '@/Layouts/PublicLayout';
import { motion, useScroll, useTransform, useInView, animate } from 'framer-motion';

export default function Welcome({ prices = [], articles = [], totalCarbonContribution = 0, totalWaste = 0 }) {
    const { auth } = usePage().props;
    const [isMobile, setIsMobile] = useState(false);

    const heroRef = useRef(null);
    const { scrollY } = useScroll();
    const backgroundY = useTransform(scrollY, [0, 500], [0, isMobile ? 0 : 100]);

    useEffect(() => {
        const handleResize = () => setIsMobile(window.innerWidth < 768);
        handleResize();
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.12
            }
        }
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 24 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.6, ease: [0.25, 1, 0.5, 1] }
        }
    };

    const handleOpenRegisterModal = () => {
        window.dispatchEvent(new CustomEvent('open-register'));
    };

    return (
        <>
            <Head title="Bank Sampah Faperta Unmul - Kelola Sampah Jadi Berkah" />
            
            {/* Hero Section */}
            <section ref={heroRef} id="home" className="relative bg-white overflow-hidden py-2xl md:py-32 border-b border-outline-variant/20">
                <motion.div style={{ y: backgroundY }} className="absolute inset-0 z-0">
                    <img 
                        alt="Hero Background" 
                        className="w-full h-full object-cover opacity-15 object-center" 
                        src="/images/hero-bfsp.jpeg"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-background via-background/5 to-transparent"></div>
                </motion.div>
                <div className="relative z-10 max-w-container-max mx-auto px-lg md:px-xl grid grid-cols-1 md:grid-cols-2 gap-2xl items-center">
                    <motion.div 
                        variants={containerVariants} 
                        initial="hidden" 
                        animate="visible" 
                        className="space-y-md"
                    >
                        <motion.h1 variants={itemVariants} className="text-[36px] md:text-[48px] leading-[1.1] font-bold text-primary">
                            Kelola Sampah <br/><span className="text-secondary">Jadi Berkah</span>
                        </motion.h1>
                        <motion.p variants={itemVariants} className="text-[16px] md:text-[18px] text-on-surface-variant max-w-lg leading-relaxed">
                            Ubah kebiasaan membuang menjadi menabung. Bergabunglah dengan kami untuk melestarikan lingkungan sambil membangun simpanan yang bermanfaat bagi masa depan Anda.
                        </motion.p>
                        <motion.div variants={itemVariants} className="pt-sm">
                            {auth.user ? (
                                auth.user.role === 'nasabah' ? (
                                    <Link
                                        href="/nasabah/dashboard"
                                        className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all no-underline"
                                    >
                                        Pergi ke Dashboard
                                    </Link>
                                ) : (
                                    <a
                                        href="/admin"
                                        className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all no-underline"
                                    >
                                        Pergi ke Dashboard
                                    </a>
                                )
                            ) : (
                                <button
                                    onClick={handleOpenRegisterModal}
                                    className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all cursor-pointer border-0 text-center"
                                >
                                    Daftar Sekarang
                                </button>
                            )}
                        </motion.div>
                    </motion.div>
                    <div className="hidden md:block relative">
                        <motion.div
                            initial={{ opacity: 0, scale: 0.9, rotate: 3, y: 0 }}
                            animate={{ 
                                opacity: 1, 
                                scale: 1, 
                                y: [0, -10, 0] 
                            }}
                            transition={{
                                opacity: { duration: 0.6 },
                                scale: { duration: 0.6 },
                                y: {
                                    repeat: Infinity,
                                    repeatType: "mirror",
                                    duration: 3.5,
                                    ease: "easeInOut"
                                }
                            }}
                            className="bg-white/90 backdrop-blur-md border border-white/50 rounded-3xl p-xl shadow-[0px_10px_30px_rgba(45,90,39,0.08)] rotate-2 hover:rotate-0 transition-all duration-500 max-w-lg mx-auto"
                        >
                            <h4 className="text-[14px] font-bold text-primary mb-md flex items-center gap-xs">
                                <span className="material-symbols-outlined text-[18px]">analytics</span>
                                Dampak Lingkungan Kita
                            </h4>
                            
                            <div className="grid grid-cols-3 gap-xs mb-lg">
                                {/* Total Sampah */}
                                <div className="space-y-xs border-r border-outline-variant/20 pr-xs">
                                    <div className="w-9 h-9 rounded-xl bg-primary-container/40 flex items-center justify-center text-primary mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                    </div>
                                    <p className="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wider">Sampah Terkelola</p>
                                    <p className="text-[16px] text-primary font-bold">
                                        <Counter value={Number(totalWaste || 0)} /> kg
                                    </p>
                                </div>
                                
                                {/* Reduksi Karbon */}
                                <div className="space-y-xs border-r border-outline-variant/20 px-xs">
                                    <div className="w-9 h-9 rounded-xl bg-secondary-container/40 flex items-center justify-center text-secondary mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>eco</span>
                                    </div>
                                    <p className="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wider">Reduksi Karbon</p>
                                    <p className="text-[16px] text-secondary font-bold">
                                        <Counter value={Number(totalCarbonContribution || 0)} /> kg CO₂e
                                    </p>
                                </div>

                                {/* Setara Pohon */}
                                <div className="space-y-xs pl-xs">
                                    <div className="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center text-green-700 mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>forest</span>
                                    </div>
                                    <p className="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wider">Setara Penyerapan</p>
                                    <p className="text-[16px] text-green-700 font-bold">
                                        <Counter value={Math.round(Number(totalCarbonContribution || 0) / 21)} /> pohon/thn
                                    </p>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                </div>
            </section>

            {/* Workflow Section (Alur Kerja) */}
            <section id="alur" className="py-2xl bg-white border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-2xl space-y-sm">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Alur Kerja Kami</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">Tiga langkah mudah untuk mulai berkontribusi pada lingkungan dan ekonomi Anda.</p>
                    </div>
                    <motion.div 
                        variants={{
                            hidden: {},
                            visible: {
                                transition: {
                                    staggerChildren: 0.15
                                }
                            }
                        }}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-100px" }}
                        className="grid grid-cols-1 md:grid-cols-3 gap-lg"
                    >
                        {/* Step 1 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300"
                        >
                            <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>app_registration</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">1. Daftar Akun</h3>
                            <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                Buat akun dengan mudah melalui platform kami untuk mulai melacak kontribusi tabungan sampah Anda.
                            </p>
                        </motion.div>
                        {/* Step 2 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300"
                        >
                            <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">2. Setor Sampah</h3>
                            <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                Bawa sampah terpilah Anda ke depo penampungan. Petugas akan menimbang dan menginput nilai rupiahnya secara riil.
                            </p>
                        </motion.div>
                        {/* Step 3 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300"
                        >
                            <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>account_balance_wallet</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">3. Tarik Saldo</h3>
                            <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                Nikmati hasil kerja keras Anda. Tarik saldo tabungan langsung ke rekening bank atau e-wallet kapan saja Anda inginkan.
                            </p>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* Operational Schedule */}
            <section id="jadwal" className="py-2xl bg-background border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <motion.div 
                        initial={{ opacity: 0, y: 40 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, margin: "-100px" }}
                        transition={{ duration: 0.8, ease: "easeOut" }}
                        className="flex flex-col md:flex-row items-stretch gap-2xl"
                    >
                        {/* Schedule Card */}
                        <div className="flex-1 space-y-lg flex flex-col justify-center">
                            <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Jadwal Operasional</h2>
                            <p className="text-[14px] md:text-[16px] text-on-surface-variant leading-relaxed">
                                Kunjungi depo kami pada jam operasional untuk menyetorkan sampah terpilah Anda. Tim petugas kami siap membantu proses penimbangan dan pencatatan saldo dengan cepat dan transparan.
                            </p>
                            <div className="bg-white rounded-2xl p-xl shadow-sm border border-outline-variant/30">
                                <div className="flex items-center gap-md border-b border-outline-variant/20 pb-md mb-md">
                                    <span className="material-symbols-outlined text-primary text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>event_available</span>
                                    <div>
                                        <h4 className="text-[16px] font-bold text-on-surface">Hari Layanan</h4>
                                        <p className="text-[14px] text-on-surface-variant font-medium">Senin - Sabtu</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-md">
                                    <span className="material-symbols-outlined text-primary text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>schedule</span>
                                    <div>
                                        <h4 className="text-[16px] font-bold text-on-surface">Jam Layanan</h4>
                                        <p className="text-[14px] text-on-surface-variant font-medium">08:00 - 16:00 WIB</p>
                                    </div>
                                </div>
                                <div className="mt-lg pt-md border-t border-outline-variant/20 flex items-center gap-xs text-error font-semibold text-[12px]">
                                    <span className="material-symbols-outlined text-[16px]">info</span>
                                    Tutup pada hari Minggu dan Hari Libur Nasional.
                                </div>
                            </div>
                        </div>
                        {/* Image Card */}
                        <div className="flex-1 w-full min-h-[300px] md:min-h-full rounded-2xl overflow-hidden shadow-md relative">
                            <img 
                                alt="Fasilitas Depo" 
                                className="absolute inset-0 w-full h-full object-cover" 
                                src="/images/bsfpxwcid.png"
                            />
                        </div>
                    </motion.div>
                </div>
            </section>

            {/* Price Catalog Section (Preview) */}
            <section id="harga" className="py-2xl bg-white border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-xl space-y-xs">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Katalog Harga Sampah</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                            Daftar harga beli per unit sampah terpilah (Pratinjau). Klik tombol di bawah untuk melihat seluruh daftar harga secara rinci.
                        </p>
                    </div>
                    
                    <motion.div 
                        initial={{ opacity: 0, y: 30 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, margin: "-100px" }}
                        transition={{ duration: 0.8, ease: "easeOut" }}
                        className="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm max-w-3xl mx-auto p-md"
                    >
                        <Table>
                            <TableHeader>
                                <TableRow className="bg-background hover:bg-background">
                                    <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md">Nama Sampah</TableHead>
                                    <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md">Kategori</TableHead>
                                    <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md text-right">Harga Beli Nasabah</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody className="text-[14px]">
                                {prices.length > 0 ? (
                                    prices.map((p) => (
                                        <TableRow key={p.id} className="hover:bg-background/40 transition-colors">
                                            <TableCell className="px-lg py-md font-semibold text-on-surface">{p.name}</TableCell>
                                            <TableCell className="px-lg py-md capitalize text-on-surface-variant">{p.category.replace('_', ' ')}</TableCell>
                                            <TableCell className="px-lg py-md text-right font-bold text-primary">
                                                Rp {numberFormat(p.price_buy)} / {p.unit}
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan="3" className="px-lg py-xl text-center text-on-surface-variant font-medium">
                                            Katalog harga sampah belum tersedia.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <div className="mt-md text-center">
                            <Link
                                href="/harga"
                                className="inline-flex items-center gap-xs text-[14px] font-bold text-primary hover:text-secondary transition-colors no-underline"
                            >
                                Lihat Seluruh Katalog Harga Lengkap
                                <span className="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </Link>
                        </div>
                    </motion.div>
                </div>
            </section>

            {/* Articles Section (Preview) */}
            <section id="artikel" className="py-2xl bg-background">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-xl space-y-xs">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Artikel &amp; Edukasi</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                            Dapatkan tips dan panduan terkini seputar cara memilah sampah dan melestarikan lingkungan.
                        </p>
                    </div>

                    <motion.div 
                        variants={{
                            hidden: {},
                            visible: {
                                transition: {
                                    staggerChildren: 0.15
                                }
                            }
                        }}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-100px" }}
                        className="grid grid-cols-1 md:grid-cols-3 gap-lg max-w-6xl mx-auto"
                    >
                        {articles.length > 0 ? (
                            articles.map((art) => (
                                <motion.article 
                                    key={art.id} 
                                    variants={{
                                        hidden: { opacity: 0, y: 30 },
                                        visible: { opacity: 1, y: 0, transition: { duration: 0.6 } }
                                    }}
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
                            ))
                        ) : (
                            <div className="col-span-3 text-center py-lg text-on-surface-variant">
                                Belum ada artikel yang dipublikasikan.
                            </div>
                        )}
                    </motion.div>

                    <div className="mt-xl text-center">
                        <Link
                            href="/artikel"
                            className="inline-flex items-center gap-xs bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-md hover:bg-secondary transition-all no-underline"
                        >
                            Lihat Semua Artikel Edukasi
                            <span className="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </Link>
                    </div>
                </div>
            </section>
        </>
    );
}

Welcome.layout = (page) => <PublicLayout children={page} />;

function numberFormat(val) {
    return new Intl.NumberFormat('id-ID').format(val);
}

function Counter({ value }) {
    const ref = useRef(null);
    const inView = useInView(ref, { once: true });

    useEffect(() => {
        if (inView) {
            const controls = animate(0, value, {
                duration: 1.5,
                ease: "easeOut",
                onUpdate: (latest) => {
                    if (ref.current) {
                        ref.current.textContent = latest.toFixed(1);
                    }
                }
            });
            return () => controls.stop();
        }
    }, [value, inView]);

    return <span ref={ref}>0.0</span>;
}
