<x-app-layout>
    @section('title', 'Import ข้อมูลพนักงาน')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Import ข้อมูลพนักงาน</h3>
                    <p class="text-sm text-slate-400">นำเข้าข้อมูลพนักงานจากไฟล์ Excel</p>
                </div>
            </div>

            <div class="p-8 space-y-8">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-emerald-700 font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <span class="text-red-700 font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('import_errors') && count(session('import_errors')) > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                            <span class="font-bold text-amber-800">รายละเอียดข้อผิดพลาด ({{ count(session('import_errors')) }} รายการ)</span>
                        </div>
                        <div class="text-sm text-amber-700 max-h-60 overflow-y-auto space-y-1">
                            @foreach(session('import_errors') as $err)
                                <div class="p-2 bg-white/50 rounded-lg">{{ $err }}</div>
                            @endforeach
                        </div>
                        <p class="text-xs text-amber-500 mt-3">
                            <i data-lucide="lightbulb" class="w-5 h-5 mr-1"></i>
                            ตรวจสอบว่า header ใน Excel ตรงกับ Template ที่กำหนด (ยศ, ชื่อ_นามสกุล, แผนก, ตำแหน่ง, บทบาท, สิทธิ์วันลา)
                        </p>
                    </div>
                @endif

                <!-- Step 1: Download Template -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl flex-shrink-0">
                            1
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-slate-800 text-lg mb-2">ดาวน์โหลด Template</h4>
                            <p class="text-slate-500 text-sm mb-4">
                                ดาวน์โหลด Template Excel แล้วกรอกข้อมูลพนักงานลงไป
                            </p>
                            <a href="{{ route('employees.template') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg transition-all">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                ดาวน์โหลด Template
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-amber-500 mt-1"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-bold mb-2">ข้อมูลใน Template:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-700">
                                <li><strong>ยศ</strong> - เช่น น.อ., น.ท., ร.อ.</li>
                                <li><strong>ชื่อ_นามสกุล</strong> - ชื่อเต็มของพนักงาน (จำเป็น)</li>
                                <li><strong>แผนก</strong> - ชื่อแผนก</li>
                                <li><strong>ตำแหน่ง</strong> - ตำแหน่งงาน</li>
                                <li><strong>บทบาท</strong> - employee, department_head, admin ฯลฯ</li>
                                <li><strong>สิทธิ์วันลา</strong> - จำนวนวันลาพักผ่อนต่อปี (default: 10)</li>
                                <li><strong>หัวหน้าแผนก</strong> - ชื่อหัวหน้าแผนก (ต้องมีในระบบ)</li>
                                <li><strong>ผู้บังคับบัญชา (Approver 2)</strong> - ชื่อผู้บังคับบัญชา (ต้องมีในระบบ)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Upload File -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl flex-shrink-0">
                            2
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-slate-800 text-lg mb-2">Upload ไฟล์ Excel</h4>
                            <p class="text-slate-500 text-sm mb-4">
                                เลือกไฟล์ Excel ที่กรอกข้อมูลเรียบร้อยแล้ว
                            </p>
                            
                            <form id="import-form" action="{{ route('employees.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div class="flex items-center gap-4">
                                    <label class="flex-grow">
                                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all" id="dropzone">
                                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="file-input" required>
                                            <i data-lucide="cloud-upload" class="w-8 h-8 text-4xl text-slate-300 mb-3"></i>
                                            <p class="text-slate-500 font-medium" id="file-name">คลิกเพื่อเลือกไฟล์ หรือ ลากไฟล์มาวาง</p>
                                            <p class="text-xs text-slate-400 mt-2">รองรับ .xlsx, .xls, .csv (ไม่เกิน 10MB)</p>
                                        </div>
                                    </label>
                                </div>

                                @error('file')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                                <div class="flex gap-3">
                                    <button type="button" id="preview-btn" class="flex-1 px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        ดูตัวอย่างข้อมูล (Debug)
                                    </button>
                                    <button type="submit" class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="upload" class="w-4 h-4"></i>
                                        Import ข้อมูลพนักงาน
                                    </button>
                                </div>
                            </form>

                            <!-- Preview Result -->
                            <div id="preview-result" class="hidden mt-4">
                                <div class="bg-slate-100 rounded-xl p-4">
                                    <h5 class="font-bold text-slate-700 mb-3">
                                        <i data-lucide="table" class="w-4 h-4 mr-2"></i>
                                        ข้อมูลที่อ่านได้จาก Excel:
                                    </h5>
                                    <div id="preview-content" class="text-sm text-slate-600 overflow-x-auto">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-blue-500 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-2">หลังจาก Import:</p>
                            <ol class="list-decimal list-inside space-y-1 text-blue-700">
                                <li>พนักงานจะถูกเพิ่มเข้าระบบ (ยังไม่มี email/password)</li>
                                <li>แชร์ลิงก์ <strong>{{ url('/employee-register') }}</strong> ให้พนักงาน</li>
                                <li>พนักงานเลือกชื่อตัวเอง แล้วตั้ง email & password</li>
                                <li>Admin อนุมัติ → พนักงานเข้าสู่ระบบได้</li>
                            </ol>
                        </div>
                    </div>
                </div>

            </div>

            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
                <a href="{{ route('employees.index') }}" class="text-slate-500 hover:text-slate-700 font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    กลับไปหน้าจัดการพนักงาน
                </a>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle file input display
            const fileInput = document.getElementById('file-input');
            const fileName = document.getElementById('file-name');
            const dropzone = document.getElementById('dropzone');

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                    dropzone.classList.add('border-emerald-400', 'bg-emerald-50/50');
                }
            });

            // Handle drag and drop
            dropzone.addEventListener('click', () => fileInput.click());
            
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-emerald-400', 'bg-emerald-50/50');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-emerald-400', 'bg-emerald-50/50');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    fileName.textContent = e.dataTransfer.files[0].name;
                }
            });

            // Preview button handler
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
                        let html = `<p class="mb-2 text-emerald-600"><strong>จำนวนแถวทั้งหมด:</strong> ${data.total_rows}</p>`;
                        
                        if (data.preview && data.preview.length > 0) {
                            html += '<div class="overflow-x-auto"><table class="min-w-full border border-slate-300 text-xs">';
                            
                            // Header row
                            html += '<thead><tr class="bg-slate-200">';
                            data.preview[0].forEach((col, idx) => {
                                html += `<th class="border border-slate-300 px-2 py-1">Col ${idx}: ${col || '(ว่าง)'}</th>`;
                            });
                            html += '</tr></thead>';
                            
                            // Data rows
                            html += '<tbody>';
                            data.preview.slice(1, 6).forEach((row, rowIdx) => {
                                html += '<tr>';
                                row.forEach(cell => {
                                    html += `<td class="border border-slate-300 px-2 py-1">${cell || ''}</td>`;
                                });
                                html += '</tr>';
                            });
                            html += '</tbody></table></div>';
                            
                            html += '<p class="mt-3 text-amber-600 text-xs"><i data-lucide="lightbulb" class="w-5 h-5 mr-1"></i><strong>แถวแรก (Row 0)</strong> คือ Header - ควรมี: ยศ, ชื่อ_นามสกุล, แผนก, ตำแหน่ง, บทบาท, สิทธิ์วันลา</p>';
                        } else {
                            html += '<p class="text-red-600">ไม่พบข้อมูลในไฟล์!</p>';
                        }
                        
                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = `<p class="text-red-600">Error: ${data.error}</p>`;
                    }
                    
                    resultDiv.classList.remove('hidden');
                    
                } catch (err) {
                    alert('เกิดข้อผิดพลาด: ' + err.message);
                }

                this.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i> ดูตัวอย่างข้อมูล (Debug)';
                this.disabled = false;
            });
        });
    </script>
</x-app-layout>

