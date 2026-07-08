import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import LoginModal from '@/Components/Auth/LoginModal';
import RegisterModal from '@/Components/Auth/RegisterModal';

export default function PublicLayout({ children }) {
    const { auth } = usePage().props;
    const { url } = usePage(); // Gets current path (e.g. /harga or /)

    const [isLoginOpen, setIsLoginOpen] = useState(false);
    const [isRegisterOpen, setIsRegisterOpen] = useState(false);
    const [activeSection, setActiveSection] = useState('home');
    const [clickedId, setClickedId] = useState(null);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    useEffect(() => {
        const handleOpenRegister = () => setIsRegisterOpen(true);
        const handleOpenLogin = () => setIsLoginOpen(true);
        
        window.addEventListener('open-register', handleOpenRegister);
        window.addEventListener('open-login', handleOpenLogin);
        
        return () => {
            window.removeEventListener('open-register', handleOpenRegister);
            window.removeEventListener('open-login', handleOpenLogin);
        };
    }, []);


    // Track which section is active if we are on the homepage
    useEffect(() => {
        if (url !== '/' && !url.startsWith('/?')) {
            // If not on homepage, determine active link based on URL path
            if (url.startsWith('/harga')) {
                setActiveSection('harga');
            } else if (url.startsWith('/artikel')) {
                setActiveSection('artikel');
            } else {
                setActiveSection('');
            }
            return;
        }

        const observerOptions = {
            root: null,
            rootMargin: '-50% 0px -50% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setActiveSection(entry.target.id);
                }
            });
        }, observerOptions);

        const sections = ['home', 'alur', 'jadwal', 'harga', 'artikel', 'kontak'];
        sections.forEach((id) => {
            const el = document.getElementById(id);
            if (el) observer.observe(el);
        });

        return () => observer.disconnect();
    }, [url]);

    // Handle scroll to hash if user navigated from another page
    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const goto = params.get('goto');
        if (goto && (url === '/' || url.startsWith('/?'))) {
            setTimeout(() => {
                const element = document.getElementById(goto);
                if (element) {
                    const yOffset = -64;
                    const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }, 300);
        }
    }, [url]);

    const handleNavClick = (e, sectionId, isAnchor) => {
        setClickedId(sectionId);
        setTimeout(() => setClickedId(null), 300);

        if (isAnchor) {
            if (url !== '/' && !url.startsWith('/?')) {
                // If we are not on the homepage, let Inertia navigate to homepage with a query parameter
                return; // Let standard Link handle navigation
            }
            
            // If we are already on homepage, prevent hard reload and scroll smoothly
            e.preventDefault();
            setActiveSection(sectionId);
            const element = document.getElementById(sectionId);
            if (element) {
                const yOffset = -64; // header height offset
                const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }
        }
    };

    const handleSwitchToRegister = () => {
        setIsLoginOpen(false);
        setTimeout(() => setIsRegisterOpen(true), 250);
    };

    const handleSwitchToLogin = () => {
        setIsRegisterOpen(false);
        setTimeout(() => setIsLoginOpen(true), 250);
    };

    const navItems = [
        { id: 'home', label: 'Beranda', href: '/', isAnchor: true },
        { id: 'alur', label: 'Alur Kerja', href: '/?goto=alur', isAnchor: true },
        { id: 'jadwal', label: 'Jadwal', href: '/?goto=jadwal', isAnchor: true },
        { id: 'harga', label: 'Katalog Harga', href: '/harga', isAnchor: false },
        { id: 'artikel', label: 'Edukasi', href: '/artikel', isAnchor: false },
        { id: 'kontak', label: 'Kontak', href: '/?goto=kontak', isAnchor: true }
    ];

    return (
        <>
            <style>{`
                @keyframes nav-click-bounce {
                    0% { transform: scale(1); }
                    30% { transform: scale(0.92); }
                    100% { transform: scale(1); }
                }
                .nav-clicked {
                    animation: nav-click-bounce 0.3s cubic-bezier(0.25, 1, 0.5, 1);
                }
                
                .nav-link-underline::after {
                    content: '';
                    display: block;
                    width: 0;
                    height: 2px;
                    background-color: #2d5a27;
                    transition: width 0.3s cubic-bezier(0.25, 1, 0.5, 1);
                    margin-top: 2px;
                    border-radius: 9999px;
                }
                
                .nav-link-active {
                    color: #2d5a27 !important;
                }
                
                .nav-link-active::after {
                    width: 100%;
                }
            `}</style>

            <div className="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">
                {/* TopNavBar */}
                <motion.header
                    initial={{ y: -64, opacity: 0 }}
                    animate={{ y: 0, opacity: 1 }}
                    transition={{ duration: 0.5, ease: "easeOut" }}
                    className="bg-white border-b border-outline-variant/30 sticky top-0 z-50 shadow-sm"
                >
                    <div className="flex justify-between items-center w-full px-lg md:px-xl max-w-container-max mx-auto h-16">
                        <Link href="/" className="text-[20px] font-bold text-primary flex items-center gap-xs no-underline">
                            <span className="material-symbols-outlined text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                            BSFP Unmul
                        </Link>
                        
                        <nav className="hidden md:flex items-center gap-lg font-semibold text-[14px]">
                            {navItems.map((item) => (
                                <Link
                                    key={item.id}
                                    href={item.href}
                                    onClick={(e) => handleNavClick(e, item.id, item.isAnchor)}
                                    className={`relative text-on-surface-variant hover:text-primary transition-all duration-300 nav-link-underline ${
                                        activeSection === item.id ? 'nav-link-active' : ''
                                    } ${
                                        clickedId === item.id ? 'nav-clicked' : ''
                                    }`}
                                >
                                    {item.label}
                                </Link>
                            ))}
                        </nav>
                        
                        {/* Mobile Menu Button */}
                        <button
                            className="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-surface-container-low transition-colors bg-transparent border-0 cursor-pointer"
                            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                            aria-label={isMobileMenuOpen ? 'Tutup menu' : 'Buka menu'}
                            aria-expanded={isMobileMenuOpen}
                        >
                            <svg className="w-6 h-6 text-on-surface" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {isMobileMenuOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>

                        <div className="flex items-center gap-sm">
                            {auth.user ? (
                                auth.user.role === 'nasabah' ? (
                                    <Link
                                        href="/nasabah/dashboard"
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md no-underline"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <a
                                        href="/cms"
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md no-underline"
                                    >
                                        Dashboard
                                    </a>
                                )
                            ) : (
                                <>
                                    <button
                                        onClick={() => setIsLoginOpen(true)}
                                        className="text-primary font-semibold text-[14px] px-md py-sm rounded-full hover:bg-surface-container-low transition-all bg-transparent border-0 cursor-pointer"
                                    >
                                        Masuk
                                    </button>
                                    <button
                                        onClick={() => setIsRegisterOpen(true)}
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md cursor-pointer border-0"
                                    >
                                        Daftar
                                    </button>
                                </>
                            )}
                        </div>
                    </div>

                    {/* Mobile Navigation Menu */}
                    {isMobileMenuOpen && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            className="md:hidden bg-white border-t border-outline-variant/30 shadow-lg"
                        >
                            <nav className="flex flex-col py-sm">
                                {navItems.map((item) => (
                                    <Link
                                        key={item.id}
                                        href={item.href}
                                        onClick={(e) => {
                                            handleNavClick(e, item.id, item.isAnchor);
                                            setIsMobileMenuOpen(false);
                                        }}
                                        className={`px-lg py-sm text-[14px] font-semibold no-underline transition-colors ${
                                            activeSection === item.id
                                                ? 'text-primary bg-primary/5'
                                                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'
                                        }`}
                                    >
                                        {item.label}
                                    </Link>
                                ))}
                                {!auth.user && (
                                    <div className="flex flex-col gap-xs px-lg pt-sm border-t border-outline-variant/30 mt-sm">
                                        <button
                                            onClick={() => {
                                                setIsLoginOpen(true);
                                                setIsMobileMenuOpen(false);
                                            }}
                                            className="text-primary font-semibold text-[14px] px-md py-sm rounded-full hover:bg-surface-container-low transition-all bg-transparent border border-primary cursor-pointer"
                                        >
                                            Masuk
                                        </button>
                                        <button
                                            onClick={() => {
                                                setIsRegisterOpen(true);
                                                setIsMobileMenuOpen(false);
                                            }}
                                            className="bg-primary text-white font-semibold text-[14px] px-md py-sm rounded-full hover:bg-opacity-90 transition-all cursor-pointer border-0"
                                        >
                                            Daftar
                                        </button>
                                    </div>
                                )}
                            </nav>
                        </motion.div>
                    )}
                </motion.header>

                {/* Page Content */}
                <main className="flex-grow">
                    {children}
                </main>

                {/* Footer */}
                <footer className="bg-surface-container-highest border-t border-outline-variant/20 text-on-surface mt-auto">
                    <div className="max-w-container-max mx-auto px-lg md:px-xl py-xl flex flex-col md:flex-row justify-between items-start gap-xl">
                        <div className="space-y-xs max-w-sm">
                            <div className="text-[18px] font-bold text-primary flex items-center gap-xs mb-sm">
                                <span className="material-symbols-outlined text-[24px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                Bank Sampah Faperta
                            </div>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                © 2026 Bank Sampah Faperta Unmul. Mengelola sampah dengan bijak, mengubah sampah menjadi tabungan kebaikan.
                            </p>
                        </div>
                        <div className="flex flex-col sm:flex-row gap-lg md:gap-2xl text-[13px] font-semibold">
                            <nav className="flex flex-col gap-sm min-w-[140px]">
                                <span className="text-[12px] uppercase tracking-wider text-primary font-bold mb-xs">Navigasi</span>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors no-underline" href="#">Tentang Kami</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors no-underline" href="#">Kebijakan Privasi</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors no-underline" href="#">Syarat &amp; Ketentuan</a>
                            </nav>
                            <div className="flex flex-col gap-sm">
                                <span className="text-[12px] uppercase tracking-wider text-primary font-bold mb-xs">Media Sosial &amp; Lokasi</span>
                                <a 
                                    className="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-sm no-underline" 
                                    href="https://www.instagram.com/asah_fapertaunmul?igsh=MWFsbm1ndGluYTQ0eA==" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                >
                                    <svg className="w-4 h-4 fill-current text-primary" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                    <span>@asah_fapertaunmul</span>
                                </a>
                                <a 
                                    className="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-sm no-underline" 
                                    href="https://wa.me/6281234567890" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                >
                                    <svg className="w-4 h-4 fill-current text-primary" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202 0 6.206 1.248 8.461 3.507 2.254 2.259 3.497 5.267 3.495 8.469-.005 6.545-5.344 11.884-11.954 11.884h-.005c-2.008-.002-3.984-.543-5.736-1.579L0 24zm6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c5.449 0 9.882-4.434 9.885-9.884.002-2.64-1.027-5.122-2.894-6.99A9.825 9.825 0 0012.008 2.03c-5.451 0-9.887 4.436-9.889 9.886-.001 2.096.547 4.142 1.588 5.945L2.83 22.338l3.562-.936zm11.137-7.956c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                                <a 
                                    className="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-sm no-underline" 
                                    href="https://maps.app.goo.gl/ACJTNrZJUyUqfh4c8?g_st=ic" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                >
                                    <span className="material-symbols-outlined text-[16px] text-primary" style={{ fontVariationSettings: "'FILL' 1" }}>location_on</span>
                                    <span>Agroekoteknologi, Unmul</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>

            <LoginModal 
                isOpen={isLoginOpen} 
                onClose={() => setIsLoginOpen(false)} 
                onSwitchToRegister={handleSwitchToRegister}
            />
            <RegisterModal 
                isOpen={isRegisterOpen} 
                onClose={() => setIsRegisterOpen(false)} 
                onSwitchToLogin={handleSwitchToLogin}
            />
        </>
    );
}
