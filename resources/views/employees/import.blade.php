<x-app-layout>
    @section('title', 'Import ข้อมูลพนักงาน')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .dropzone-area {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .dropzone-area:hover,
            .dropzone-area.active {
                border-color: #10b981;
                background: rgba(16, 185, 129, 0.04);
                transform: scale(1.01);
            }

            .step-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .step-card:hover {
                transform: translateY(-4px);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-emerald-100/30 rounded-full blur-[120px] -mr-80 -mt-80"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[100px] -ml-40 -mb-40"></div>

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            ระบบนำเข้าข้อมูล
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            Import <span class="text-emerald-600">ข้อมูลพนักงาน</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                            นำเข้าข้อมูลกำลังพลจากไฟล์ Excel อย่างรวดเร็ว<br class="hidden md:block">
                            พร้อมระบบตรวจสอบและแจ้งข้อผิดพลาดอัตโนมัติ
                        </p>
                    </div>
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white border border-slate-200 text-slate-600 font-black rounded-2xl hover:bg-slate-50 transition-all active:scale-95 shadow-sm uppercase tracking-widest text-xs gap-3">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        กลับหน้ารายชื่อ
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-10">

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="glass-panel rounded-[2rem] p-6 flex items-center gap-5 animate-slide-up border-l-4 border-emerald-500">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <span class="text-emerald-800 font-black text-lg">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="glass-panel rounded-[2rem] p-6 flex items-center gap-5 animate-slide-up border-l-4 border-rose-500">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm border border-rose-100">
                        <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                    </div>
                    <span class="text-rose-800 font-black text-lg">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="glass-panel rounded-[2rem] p-8 animate-slide-up border-l-4 border-amber-500">
                    <div class="flex items-center gap-3 mb-4">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
                        <span class="font-black text-amber-800 text-lg">รายละเอียดข้อผิดพลาด ({{ count(session('import_errors')) }} รายการ)</span>
                    </div>
                    <div class="text-sm text-amber-700 max-h-60 overflow-y-auto space-y-2">
                        @foreach(session('import_errors') as $err)
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 font-medium">{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Step 1: Download -->
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up step-card" style="animation-delay: 0.1s">
                <div class="bg-emerald-600 px-10 py-8 flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-white/20 text-white flex items-center justify-center font-black text-2xl border border-white/10 backdrop-blur-md">
                        01
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">ดาวน์โหลดข้อมูลพนักงานปัจจุบัน</h3>
                        <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest mt-1">ส่งออกข้อมูลเพื่อตรวจสอบและแก้ไข</p>
                    </div>
                </div>
                <div class="p-10">
                    <p class="text-slate-500 font-medium mb-6 leading-relaxed">
                        ดาวน์โหลด Excel ที่มีข้อมูลพนักงานทั้งหมดในระบบ แก้ไขข้อมูล Approver แล้วอัปโหลดกลับ
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('employees.export') }}" class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 active:scale-95 gap-3 uppercase tracking-widest text-xs">
                            <i data-lucide="download" class="w-5 h-5"></i>
                            ดาวน์โหลดข้อมูลพนักงาน
                        </a>
                        <a href="{{ route('employees.template') }}" class="inline-flex items-center px-8 py-4 bg-white border border-slate-200 text-slate-700 font-black rounded-2xl shadow-sm hover:bg-slate-50 transition-all hover:-translate-y-1 active:scale-95 gap-3 uppercase tracking-widest text-xs">
                            <i data-lucide="file-plus" class="w-5 h-5"></i>
                            ดาวน์โหลด Template เปล่า
                        </a>
                    </div>
                </div>
            </div>

            <!-- Template Info -->
            <div class="glass-panel rounded-[2.5rem] p-8 animate-slide-up" style="animation-delay: 0.15s">
                <div class="flex items-start gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 shadow-sm flex-shrink-0">
                        <i data-lucide="lightbulb" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-black text-slate-800 text-lg mb-4 tracking-tight">คอลัมน์ใน Excel (9 คอลัมน์)</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-slate-600">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-slate-400"></span><span><strong>ยศ</strong> — เช่น น.อ., น.ท.</span></div>
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-slate-400"></span><span><strong>ชื่อ_นามสกุล</strong> — จำเป็น</span></div>
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-slate-400"></span><span><strong>แผนก</strong> — ชื่อแผนก</span></div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-indigo-400"></span><span><strong>ตำแหน่ง</strong> — ตำแหน่งงาน</span></div>
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-indigo-400"></span><span><strong>บทบาท</strong> — ข้าราชการ ฯลฯ</span></div>
                                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100"><span class="w-2 h-2 rounded-full bg-indigo-400"></span><span><strong>สิทธิ์วันลา</strong> — วัน/ปี</span></div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl border border-emerald-100"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-emerald-700"><strong>หัวหน้าแผนก</strong> — Approver 1</span></div>
                                <div class="flex items-center gap-2 p-3 bg-blue-50 rounded-xl border border-blue-100"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-blue-700"><strong>รอง ผบ.</strong> — Acknowledgement</span></div>
                                <div class="flex items-center gap-2 p-3 bg-violet-50 rounded-xl border border-violet-100"><span class="w-2 h-2 rounded-full bg-violet-500"></span><span class="text-violet-700"><strong>ผู้บังคับบัญชา</strong> — Approver 2</span></div>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-slate-900 rounded-2xl flex items-center gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
                            <span class="text-[11px] font-bold text-slate-400">พบชื่อที่มีอยู่ → <span class="text-emerald-400">อัปเดต</span> | ไม่พบ → <span class="text-amber-400">เพิ่มใหม่</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Upload File -->
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up step-card" style="animation-delay: 0.2s">
                <div class="bg-slate-900 px-10 py-8 flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-white/10 text-emerald-400 flex items-center justify-center font-black text-2xl border border-white/10 backdrop-blur-md">
                        02
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">Upload ไฟล์ Excel</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">เลือกไฟล์ Excel ที่กรอกข้อมูลเรียบร้อยแล้ว</p>
                    </div>
                </div>

                <div class="p-10">
                    <form id="import-form" action="{{ route('employees.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <label class="block cursor-pointer">
                            <div class="dropzone-area border-2 border-dashed border-slate-200 rounded-[2.5rem] p-16 text-center" id="dropzone">
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="file-input" required>
                                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                                    <i data-lucide="cloud-upload" class="w-10 h-10 text-slate-300"></i>
                                </div>
                                <p class="text-lg font-black text-slate-600" id="file-name">คลิกเพื่อเลือกไฟล์ หรือ ลากไฟล์มาวาง</p>
                                <p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest">รองรับ .xlsx, .xls, .csv (ไม่เกิน 10MB)</p>
                            </div>
                        </label>

                        @error('file')
                            <p class="text-rose-500 font-bold text-sm">{{ $message }}</p>
                        @enderror

                        <div class="flex gap-4">
                            <button type="button" id="preview-btn" class="flex-1 py-5 bg-white border border-slate-200 text-slate-700 rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-sm hover:bg-slate-50 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                                ดูตัวอย่างข้อมูล (Debug)
                            </button>
                            <button type="submit" class="flex-1 py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                                <i data-lucide="upload" class="w-5 h-5"></i>
                                Import ข้อมูลพนักงาน
                            </button>
                        </div>
                    </form>

                    <!-- Preview Result -->
                    <div id="preview-result" class="hidden mt-8">
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                            <h5 class="font-black text-slate-700 mb-4 flex items-center gap-2 uppercase tracking-widest text-xs">
                                <i data-lucide="table" class="w-4 h-4"></i>
                                ข้อมูลที่อ่านได้จาก Excel
                            </h5>
                            <div id="preview-content" class="text-sm text-slate-600 overflow-x-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Info -->
            <div class="glass-panel rounded-[2.5rem] p-8 animate-slide-up" style="animation-delay: 0.3s">
                <div class="flex items-start gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100 shadow-sm flex-shrink-0">
                        <i data-lucide="info" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="font-black text-slate-800 text-lg mb-4 tracking-tight">หลังจาก Import</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs font-black flex-shrink-0">1</span>
                                <p class="text-sm text-slate-600 font-medium">พนักงานจะถูกเพิ่มเข้าระบบ (ยังไม่มี email/password)</p>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs font-black flex-shrink-0">2</span>
                                <p class="text-sm text-slate-600 font-medium">แชร์ลิงก์ <strong class="text-indigo-600">{{ url('/employee-register') }}</strong> ให้พนักงาน</p>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs font-black flex-shrink-0">3</span>
                                <p class="text-sm text-slate-600 font-medium">พนักงานเลือกชื่อตัวเอง แล้วตั้ง email & password</p>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs font-black flex-shrink-0">4</span>
                                <p class="text-sm text-emerald-700 font-medium">Admin อนุมัติ → พนักงานเข้าสู่ระบบได้ ✓</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-input');
            const fileName = document.getElementById('file-name');
            const dropzone = document.getElementById('dropzone');

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                    dropzone.classList.add('active');
                }
            });

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('active');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('active');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    fileName.textContent = e.dataTransfer.files[0].name;
                    dropzone.classList.add('active');
                }
            });

            document.getElementById('preview-btn').addEventListener('click', async function() {
                if (!fileInput.files.length) {
                    alert('กรุณาเลือกไฟล์ก่อน');
                    return;
                }

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                this.textContent = 'กำลังอ่านไฟล์...';
                this.disabled = true;

                try {
                    const response = await fetch('{{ route("employees.import.preview") }}', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    const resultDiv = document.getElementById('preview-result');
                    const contentDiv = document.getElementById('preview-content');

                    if (data.success) {
                        let html = `<p class="mb-3 text-emerald-600 font-black"><strong>จำนวนแถวทั้งหมด:</strong> ${data.total_rows}</p>`;

                        if (data.preview && data.preview.length > 0) {
                            html += '<div class="overflow-x-auto rounded-xl border border-slate-200"><table class="min-w-full text-xs">';
                            html += '<thead><tr class="bg-slate-100">';
                            data.preview[0].forEach((col, idx) => {
                                html += `<th class="border-b border-slate-200 px-3 py-2 text-left font-black text-slate-500 uppercase tracking-wider">Col ${idx}: ${col || '(ว่าง)'}</th>`;
                            });
                            html += '</tr></thead><tbody>';
                            data.preview.slice(1, 6).forEach((row) => {
                                html += '<tr class="hover:bg-slate-50">';
                                row.forEach(cell => {
                                    html += `<td class="border-b border-slate-100 px-3 py-2 font-medium">${cell || ''}</td>`;
                                });
                                html += '</tr>';
                            });
                            html += '</tbody></table></div>';
                        } else {
                            html += '<p class="text-rose-600 font-bold">ไม่พบข้อมูลในไฟล์!</p>';
                        }

                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = `<p class="text-rose-600 font-bold">Error: ${data.error}</p>`;
                    }

                    resultDiv.classList.remove('hidden');
                } catch (err) {
                    alert('เกิดข้อผิดพลาด: ' + err.message);
                }

                this.innerHTML = '<i data-lucide="eye" class="w-5 h-5"></i> ดูตัวอย่างข้อมูล (Debug)';
                this.disabled = false;
                if (window.lucide) window.lucide.createIcons();
            });
        });
    </script>
</x-app-layout>
