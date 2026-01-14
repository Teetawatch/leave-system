<x-app-layout>
    @section('title', 'จัดการข้อมูลส่วนตัว')

    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-slate-800">ข้อมูลส่วนตัว</h2>
            <p class="text-slate-500">จัดการข้อมูลบัญชีผู้ใช้และรหัสผ่านของคุณ</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Profile Info & Avatar -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="font-bold text-slate-800 text-lg">ข้อมูลทั่วไป & รูปโปรไฟล์</h3>
                        <p class="text-sm text-slate-500">อัปเดตชื่อ อีเมล และรูปภาพของคุณ</p>
                    </div>
                    <div class="p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Right Column: Password & Delete -->
            <div class="space-y-8">
                <!-- Update Password Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">เปลี่ยนรหัสผ่าน</h3>
                        <p class="text-xs text-slate-500">แนะนำให้ใช้รหัสผ่านที่รัดกุม</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account Card (Optional, maybe specific for Admin or just keep hidden/small) -->
                <!-- 
                <div class="bg-white rounded-2xl shadow-sm border border-red-50 overflow-hidden">
                    <div class="px-6 py-5 border-b border-red-50 bg-red-50/30">
                        <h3 class="font-bold text-red-600">ลบบัญชีผู้ใช้</h3>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div> 
                -->
            </div>
        </div>
    </div>
</x-app-layout>
