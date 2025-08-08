import { ref } from 'vue';

// in session: 'isAdmin'
export function checkIfAdmin() {
  const isAdmin = ref(Boolean(window.LaravelUser?.isAdmin));
  return { isAdmin };
}

// in session: 'software_id'
export function checkProject() {
  const projectType = ref(window.LaravelUser?.project ?? 1);
  return { projectType };
}

export function useToast() {
  const showToast = ref(false)
  const toastMessage = ref('')
  const toastType = ref('info')
  const toastTitle = ref('Notification')

  const show = (message, type = 'info', title = 'Notification', duration = 3000) => {
    toastMessage.value = message
    toastType.value = type
    toastTitle.value = title
    showToast.value = false

    setTimeout(() => {
      showToast.value = true
    }, 10)

    setTimeout(() => {
      showToast.value = false
    }, duration)
  }

  return {
    showToast,
    toastMessage,
    toastType,
    toastTitle,
    show,
  }
}
