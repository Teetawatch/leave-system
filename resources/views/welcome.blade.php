<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบลาออนไลน์ รพธ.พธ.ทร. | Smart Navy E-Leave</title>

    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Prompt', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), url('/images/welcome-bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px border rgba(255, 255, 255, 0.05);
        }

        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>

<body class="antialiased bg-slate-900 text-slate-200 overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 px-6 py-8 transition-all duration-500" x-data="{ scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-7xl mx-auto flex justify-between items-center bg-white/5 backdrop-blur-xl px-8 py-4 rounded-[2rem] border border-white/10 shadow-2xl shadow-black/20"
            :class="{ 'py-3 px-6 rounded-[1.5rem] bg-slate-900/90': scrolled }">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-lg shadow-brand-500/30">
                    <i data-lucide="anchor" class="w-7 h-7 text-white"></i>
                </div>
                <div class="hidden md:block">
                    <h1 class="text-xl font-black text-white leading-none tracking-tight">E-LEAVE SYSTEM</h1>
                    <p class="text-[9px] font-black text-brand-400 uppercase tracking-[0.2em] mt-1">รพธ.พธ.ทร. | Smart
                        Navy</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-8 py-3 bg-white text-slate-900 font-black rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl shadow-white/5 uppercase tracking-widest text-xs">ไปยังแผงควบคุม</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-xs font-black text-white/70 hover:text-white uppercase tracking-widest transition-colors">Log
                            In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-8 py-3 bg-brand-600 text-white font-black rounded-2xl hover:bg-brand-500 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-brand-500/20 uppercase tracking-widest text-xs">สมัครใช้งาน</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-bg min-h-screen pt-48 pb-32 flex items-center relative overflow-hidden">
            <!-- Abstract Decor -->
            <div
                class="absolute top-1/4 -left-20 w-80 h-80 bg-brand-500 rounded-full blur-[120px] opacity-20 animate-pulse">
            </div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-500 rounded-full blur-[150px] opacity-20">
            </div>

            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center relative z-10">
                <div class="space-y-10">
                    <div
                        class="inline-flex items-center gap-3 px-5 py-2 rounded-full glass-card border-white/5 text-[10px] font-black uppercase tracking-[0.3em] text-brand-400">
                        <span class="w-2 h-2 rounded-full bg-brand-500 animate-ping"></span>
                        Next-Gen Leave Management
                    </div>
                    <h2 class="text-6xl md:text-8xl font-black text-white tracking-tighter leading-[0.9] prose-shadow">
                        นวัตกรรมการลา <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">กองทัพเรือ</span>
                        ยุคใหม่
                    </h2>
                    <p class="text-xl text-slate-400 font-bold leading-relaxed max-w-xl italic">
                        ระบบบริหารจัดการการลาปฏิบัติราชการของข้าราชการ รพธ.พธ.ทร. ที่รวมความรวดเร็ว ความโปร่งใส
                        และความทันสมัยเข้าไว้ด้วยกันอย่างสมบูรณ์แบบ
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-6 pt-6">
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-12 py-5 bg-white text-slate-900 font-black text-lg rounded-[2.5rem] shadow-2xl hover:scale-105 transition-all text-center">
                            เข้าสู่ระบบทันที
                        </a>
                        <a href="#features"
                            class="flex items-center gap-4 text-white font-black uppercase tracking-widest text-sm group">
                            ดูความสามารถระบบ
                            <span
                                class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center group-hover:bg-white group-hover:text-slate-900 transition-all">
                                <i data-lucide="arrow-down" class="w-5 h-5"></i>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="hidden lg:block relative">
                    <!-- Floating Tablet Preview Mockup -->
                    <div class="animate-float relative z-20">
                        <div
                            class="bg-slate-900/50 backdrop-blur-3xl rounded-[3rem] p-4 border border-white/10 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)]">
                            <img src="/images/welcome-bg.png" class="rounded-[2.5rem] opacity-80"
                                alt="Dashboard Preview">
                            <!-- Status Overlay Cards -->
                            <div
                                class="absolute -top-10 -right-10 glass-card p-6 rounded-3xl border-white/10 shadow-2xl">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                                        <i data-lucide="check" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-black text-slate-400">Status Approved</p>
                                        <p class="text-xs font-black text-white tracking-widest">ดำเนินการเรียบร้อย</p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="absolute -bottom-10 -left-10 glass-card p-6 rounded-3xl border-white/10 shadow-2xl">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-brand-500 flex items-center justify-center text-white">
                                        <i data-lucide="clock" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-black text-slate-400">Total Leaves</p>
                                        <p class="text-xs font-black text-white tracking-widest">กำลังดำเนินการ 03
                                            รายการ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-32 bg-slate-900 relative">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center space-y-4 mb-24">
                    <h3 class="text-brand-500 font-black uppercase tracking-[0.4em] text-xs">Systems & Efficiency</h3>
                    <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">ออกแบบเพื่อประสิทธิภาพขีดสุด
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Feature 1 -->
                    <div
                        class="glass-card rounded-[3.5rem] p-10 border-white/5 hover:bg-white/5 hover:-translate-y-4 transition-all duration-500 group">
                        <div
                            class="w-20 h-20 rounded-3xl bg-brand-500/20 text-brand-400 flex items-center justify-center mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all shadow-inner">
                            <i data-lucide="send" class="w-10 h-10"></i>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-4">ขอลาออนไลน์ 100%</h4>
                        <p class="text-slate-400 font-bold leading-relaxed">ยกเลิกขั้นตอนกระดาษที่ยุ่งยาก
                            ยื่นคำขอได้จากทั่วทุกมุมโลกผ่านอุปกรณ์พกพาของท่านเอง</p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="glass-card rounded-[3.5rem] p-10 border-white/5 hover:bg-white/5 hover:-translate-y-4 transition-all duration-500 group">
                        <div
                            class="w-20 h-20 rounded-3xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all shadow-inner">
                            <i data-lucide="shield-check" class="w-10 h-10"></i>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-4">อนุมัติตามสายงาน</h4>
                        <p class="text-slate-400 font-bold leading-relaxed">
                            รักษาระเบียบวินัยกองทัพด้วยระบบอนุมัติตามลำดับขีดความรับผิดชอบที่โปร่งใส</p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="glass-card rounded-[3.5rem] p-10 border-white/5 hover:bg-white/5 hover:-translate-y-4 transition-all duration-500 group">
                        <div
                            class="w-20 h-20 rounded-3xl bg-amber-500/20 text-amber-400 flex items-center justify-center mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all shadow-inner">
                            <i data-lucide="bar-chart-3" class="w-10 h-10"></i>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-4">สรุปรายงานอัจฉริยะ</h4>
                        <p class="text-slate-400 font-bold leading-relaxed">ผู้ประเมินและธุรการสามารถเรียกดูสถิติการลา
                            เพื่อวางแผนบริหารกำลังพลได้อย่างมีประสิทธิภาพ</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-32 bg-slate-900 overflow-hidden relative">
            <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
                <div
                    class="bg-gradient-to-br from-brand-600 to-indigo-700 rounded-[5rem] p-20 shadow-[0_50px_100px_-20px_rgba(37,99,235,0.4)] relative overflow-hidden group">
                    <div
                        class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-white mb-8 tracking-tighter">พร้อมยกระดับหน่วยงาน
                        <br /> เข้าสู่โลกดิจิทัลแล้วหรือยัง?</h2>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-4 px-12 py-5 bg-white text-slate-900 font-black text-xl rounded-[2.5rem] hover:scale-110 transition-all shadow-2xl">
                        เข้าสู่ระบบ E-LEAVE
                        <i data-lucide="arrow-right" class="w-6 h-6"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 bg-slate-900 text-center">
        <p class="text-slate-500 text-xs font-black uppercase tracking-[0.3em] italic">© {{ date('Y') }} Smart Navy |
            โรงพยาบาลธนบุรี พัสดุ/ธุรการ กองทัพเรือ</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>