import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function GlobalMascotTransition() {
    // status can be: 'idle', 'entering', 'exiting'
    const [status, setStatus] = useState('idle');

    useEffect(() => {
        let unbindStart;
        let unbindFinish;

        unbindStart = router.on('start', (event) => {
            const path = event.detail.visit.url.pathname;
            const method = event.detail.visit.method.toLowerCase();
            
            // Only trigger transition for POST login/register submissions
            if ((path === '/login' || path === '/register') && method === 'post') {
                setStatus('entering');
            }
        });

        unbindFinish = router.on('finish', (event) => {
            // Give a small delay for smooth visual transition
            setTimeout(() => {
                setStatus((currentStatus) => {
                    if (currentStatus === 'entering') {
                        // Check if there are validation errors on the page
                        const pageErrors = router.page?.props?.errors || {};
                        const hasErrors = Object.keys(pageErrors).length > 0;
                        
                        if (hasErrors) {
                            // If there are validation errors, slide back down (idle)
                            return 'idle';
                        } else {
                            // If successful, slide up and out (exiting)
                            return 'exiting';
                        }
                    }
                    return currentStatus;
                });
            }, 300);
        });

        return () => {
            if (unbindStart) unbindStart();
            if (unbindFinish) unbindFinish();
        };
    }, []);

    // When exiting transition is done, return to idle
    const handleExitComplete = () => {
        if (status === 'exiting') {
            setStatus('idle');
        }
    };

    if (status === 'idle') return null;

    // y: 100vh (bottom) -> 0vh (center) -> -100vh (top)
    const panelVariants = {
        entering: { y: '0vh' },
        exiting: { y: '-100vh' }
    };

    const currentPath = window.location.pathname;
    const isRegister = currentPath.startsWith('/register') || (router.page?.url && router.page.url.startsWith('/register'));

    return (
        <div className="fixed inset-0 z-[9999] pointer-events-none flex flex-col items-center justify-center">
            {/* The full-screen overlay panel */}
            <motion.div
                initial={{ y: '100vh' }}
                animate={status}
                variants={panelVariants}
                onAnimationComplete={handleExitComplete}
                transition={{ duration: 0.6, ease: [0.76, 0, 0.24, 1] }}
                className="absolute inset-0 bg-primary flex flex-col items-center justify-center pointer-events-auto"
            >
                {/* Rays background effect */}
                <div className="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,white_30%,transparent_70%)] animate-pulse"></div>
                
                {/* Floating Mascot */}
                <motion.img
                    src="/images/maskot-login.png"
                    alt="Maskot Transition"
                    className="w-64 h-auto z-10 select-none pointer-events-none"
                    animate={{ y: [0, -15, 0] }}
                    transition={{
                        repeat: Infinity,
                        repeatType: "mirror",
                        duration: 2.5,
                        ease: "easeInOut"
                    }}
                />

                {/* Text Indicator */}
                <div className="text-center text-white mt-lg z-10 px-md">
                    <h2 className="text-[24px] font-bold text-white tracking-wide">
                        {isRegister ? 'Pendaftaran Anda Sedang Diproses...' : 'Menghubungkan ke Akun Anda...'}
                    </h2>
                    <p className="text-xs text-white/70 mt-sm max-w-sm mx-auto leading-relaxed">
                        Mohon tunggu sebentar, Tani sedang mengamankan data kontribusi tabungan Anda.
                    </p>
                </div>
            </motion.div>
        </div>
    );
}
