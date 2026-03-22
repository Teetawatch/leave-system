import Swal from 'sweetalert2';

export const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

export function toastSuccess(message) {
    Toast.fire({ icon: 'success', title: message });
}

export function toastError(message) {
    Toast.fire({ icon: 'error', title: message });
}

export function toastInfo(message) {
    Toast.fire({ icon: 'info', title: message });
}

export function toastWarning(message) {
    Toast.fire({ icon: 'warning', title: message });
}

export function confirmDialog({ title, text, icon = 'warning', confirmText = 'ยืนยัน', cancelText = 'ยกเลิก', confirmColor = '#4f46e5' } = {}) {
    return Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#94a3b8',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        focusCancel: true,
    });
}

export function confirmDelete({ title = 'ยืนยันการลบ?', text = 'การกระทำนี้ไม่สามารถย้อนกลับได้', confirmText = 'ลบเลย' } = {}) {
    return confirmDialog({ title, text, icon: 'warning', confirmText, confirmColor: '#ef4444' });
}

export function confirmCancel({ title = 'ยืนยันการยกเลิก?', text = '', confirmText = 'ยกเลิกรายการ' } = {}) {
    return confirmDialog({ title, text, icon: 'question', confirmText, confirmColor: '#f59e0b' });
}

export { Swal };
