import React from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function NasabahLayout({ children }) {
    const { auth } = usePage().props;
    const url = usePage().url;

    // Check active navigation link
    const isHome = url === '/nasabah/dashboard';
    const isDeposit = url === '/nasabah/setor';
    const isWithdraw = url === '/nasabah/tarik';
    const isHistory = url === '/nasabah/riwayat';

    return (
        <div className="font-sans text-on-surface antialiased bg-[#F4F6F4] min-h-screen flex flex-col">
            {/* Top Navigation Header - Full Width for Desktop */}
            <header className="bg-white/90 backdrop-blur-md border-b border-outline-variant/30 sticky top-0 z-50 shadow-sm">
                <div className="flex justify-between items-center w-full px-lg md:px-xl max-w-7xl mx-auto h-16">
                    {/* Left: Brand Logo & Title */}
                    <Link href="/nasabah/dashboard" className="text-[18px] md:text-[20px] font-bold text-primary flex items-center gap-2">
                        <div className="w-9 h-9 rounded-xl bg-primary flex items-center justify-center shadow-md">
                            <span className="material-symbols-outlined text-white text-[22px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                        </div>
                        <span className="hidden sm:inline font-bold tracking-tight">Bank Sampah Faperta</span>
                        <span className="sm:hidden font-bold tracking-tight">BSF</span>
                    </Link>

                    {/* Middle: Desktop Nav Links (Hidden on Mobile) */}
                    <nav className="hidden md:flex items-center gap-md font-semibold text-[14px]">
                        <Link 
                            className={`px-sm py-1.5 rounded-full transition-all ${
                                isHome 
                                ? 'bg-primary/10 text-primary font-bold shadow-sm' 
                                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'
                            }`} 
                            href="/nasabah/dashboard"
                        >
                            Dasbor
                        </Link>
                        <Link 
                            className={`px-sm py-1.5 rounded-full transition-all ${
                                isDeposit 
                                ? 'bg-primary/10 text-primary font-bold shadow-sm' 
                                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'
                            }`} 
                            href="/nasabah/setor"
                        >
                            Setor Sampah
                        </Link>
                        <Link 
                            className={`px-sm py-1.5 rounded-full transition-all ${
                                isWithdraw 
                                ? 'bg-primary/10 text-primary font-bold shadow-sm' 
                                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'
                            }`} 
                            href="/nasabah/tarik"
                        >
                            Tarik Saldo
                        </Link>
                        <Link 
                            className={`px-sm py-1.5 rounded-full transition-all ${
                                isHistory 
                                ? 'bg-primary/10 text-primary font-bold shadow-sm' 
                                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'
                            }`} 
                            href="/nasabah/riwayat"
                        >
                            Riwayat
                        </Link>
                    </nav>

                    {/* Right: User Profile & Logout */}
                    <div className="flex items-center gap-md">
                        <div className="flex items-center gap-sm bg-surface-container-low/55 pl-sm pr-md py-xs rounded-full border border-outline-variant/20">
                            <div className="w-8 h-8 rounded-full border border-outline-variant/40 overflow-hidden bg-primary flex items-center justify-center text-white font-bold text-[14px] shadow-sm">
                                {auth.user.name.charAt(0)}
                            </div>
                            <div className="hidden lg:block text-left">
                                <p className="text-[9px] leading-[11px] text-on-surface-variant font-medium">Nasabah</p>
                                <h1 className="text-[13px] leading-[15px] font-bold text-primary truncate max-w-[120px]">{auth.user.name}</h1>
                            </div>
                        </div>
                        
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button" 
                            className="p-2 rounded-full hover:bg-red-50 text-error transition-all active:scale-95 flex items-center gap-xs"
                            title="Keluar"
                        >
                            <span className="material-symbols-outlined block text-[22px]">logout</span>
                            <span className="hidden md:inline font-semibold text-[13px]">Keluar</span>
                        </Link>
                    </div>
                </div>
            </header>

            {/* Main Content Area - Full Responsive Width */}
            <main className="flex-grow w-full max-w-7xl mx-auto px-md md:px-lg py-md md:py-lg flex flex-col gap-md pb-24 md:pb-8">
                {children}
            </main>

            {/* BottomNavBar - ONLY shown on Mobile (Hidden on Desktop) */}
            <nav className="md:hidden fixed bottom-0 left-0 right-0 w-full z-50 flex justify-around items-center px-sm pb-md pt-sm bg-white shadow-[0px_-4px_20px_rgba(45,90,39,0.08)] rounded-t-2xl border-t border-outline-variant/20">
                {/* Home Link */}
                <Link
                    href="/nasabah/dashboard"
                    className={`flex flex-col items-center justify-center px-4 py-1.5 transition-all duration-300 rounded-full active:scale-90 ${
                        isHome 
                        ? 'bg-primary/10 text-primary font-bold' 
                        : 'text-on-surface-variant hover:bg-surface-container-low'
                    }`}
                >
                    <span className="material-symbols-outlined block text-[22px]" style={{ fontVariationSettings: isHome ? "'FILL' 1" : "'FILL' 0" }}>home</span>
                    <span className="text-[10px] font-semibold mt-1">Dasbor</span>
                </Link>

                {/* Deposit Link */}
                <Link
                    href="/nasabah/setor"
                    className={`flex flex-col items-center justify-center px-4 py-1.5 transition-all duration-300 rounded-full active:scale-90 ${
                        isDeposit 
                        ? 'bg-primary/10 text-primary font-bold' 
                        : 'text-on-surface-variant hover:bg-surface-container-low'
                    }`}
                >
                    <span className="material-symbols-outlined block text-[22px]" style={{ fontVariationSettings: isDeposit ? "'FILL' 1" : "'FILL' 0" }}>recycling</span>
                    <span className="text-[10px] font-semibold mt-1">Setor</span>
                </Link>

                {/* Withdraw Link */}
                <Link
                    href="/nasabah/tarik"
                    className={`flex flex-col items-center justify-center px-4 py-1.5 transition-all duration-300 rounded-full active:scale-90 ${
                        isWithdraw 
                        ? 'bg-primary/10 text-primary font-bold' 
                        : 'text-on-surface-variant hover:bg-surface-container-low'
                    }`}
                >
                    <span className="material-symbols-outlined block text-[22px]" style={{ fontVariationSettings: isWithdraw ? "'FILL' 1" : "'FILL' 0" }}>payments</span>
                    <span className="text-[10px] font-semibold mt-1">Tarik</span>
                </Link>

                {/* History Link */}
                <Link
                    href="/nasabah/riwayat"
                    className={`flex flex-col items-center justify-center px-4 py-1.5 transition-all duration-300 rounded-full active:scale-90 ${
                        isHistory 
                        ? 'bg-primary/10 text-primary font-bold' 
                        : 'text-on-surface-variant hover:bg-surface-container-low'
                    }`}
                >
                    <span className="material-symbols-outlined block text-[22px]" style={{ fontVariationSettings: isHistory ? "'FILL' 1" : "'FILL' 0" }}>history</span>
                    <span className="text-[10px] font-semibold mt-1">Riwayat</span>
                </Link>
            </nav>
        </div>
    );
}
