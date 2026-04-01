// Simple toast using browser alert as fallback
export function toastSuccess(msg) {
  if (window?.toastr) {
    window.toastr.success(msg);
  } else {
    alert(msg);
  }
}

export function toastError(msg) {
  if (window?.toastr) {
    window.toastr.error(msg);
  } else {
    alert(msg);
  }
}
