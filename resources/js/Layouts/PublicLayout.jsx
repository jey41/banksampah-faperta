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

        const sections = ['home', 'alur', 'jadwal', 'harga', 'artikel'];
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
        { id: 'artikel', label: 'Edukasi', href: '/artikel', isAnchor: false }
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
                                        href="/admin"
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
                </motion.header>

                {/* Page Content */}
                <main className="flex-grow">
                    {children}
                </main>

                {/* Footer */}
                <footer className="bg-surface-container-highest border-t border-outline-variant/20 text-on-surface mt-auto">
                    <div className="max-w-container-max mx-auto px-lg md:px-xl py-xl flex flex-col md:flex-row justify-between items-start gap-lg">
                        <div className="space-y-xs max-w-sm">
                            <div className="text-[18px] font-bold text-primary flex items-center gap-xs mb-sm">
                                <span className="material-symbols-outlined text-[24px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                Bank Sampah
                            </div>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                © 2026 Bank Sampah Faperta Unmul. Mengelola sampah dengan bijak, mengubah sampah menjadi tabungan kebaikan.
                            </p>
                        </div>
                        <nav className="flex flex-col md:flex-row gap-lg md:gap-2xl text-[13px] font-semibold">
                            <div className="flex flex-col gap-sm">
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Tentang Kami</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Kebijakan Privasi</a>
                            </div>
                            <div className="flex flex-col gap-sm">
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Syarat &amp; Ketentuan</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Hubungi Kami</a>
                            </div>
                        </nav>
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
