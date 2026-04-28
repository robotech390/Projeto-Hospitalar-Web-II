import { toast as sonnerToast } from 'sonner';

// Toast utility using sonner
export const toast = {
  success: (msg) => {
    sonnerToast.success(msg);
  },
  error: (msg) => {
    sonnerToast.error(msg);
  }
};
