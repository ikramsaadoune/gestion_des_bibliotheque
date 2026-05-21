import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    withCredentials: true,
    withXSRFToken: true,
});

api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.hash = '#/login';
        }
        return Promise.reject(error);
    }
);

export default api;

export const authAPI = {
    login: (data) => api.post('/login', data),
    register: (data) => api.post('/register', data),
    logout: () => api.post('/logout'),
    user: () => api.get('/user'),
    updateProfile: (data) => api.post('/user/profile', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
    changePassword: (data) => api.put('/user/password', data),
    forgotPassword: (data) => api.post('/forgot-password', data),
    resetPassword: (data) => api.post('/reset-password', data),
};

export const adminAPI = {
    logout: () => api.post('/admin/logout'),
    me: () => api.get('/admin/me'),
    dashboard: () => api.get('/admin/dashboard'),
    recentActivity: () => api.get('/admin/dashboard/recent'),
};

export const adminUsersAPI = {
    all: (params) => api.get('/admin/users', { params }),
    show: (id) => api.get(`/admin/users/${id}`),
    create: (data) => api.post('/admin/users', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
    update: (id, data) => api.post(`/admin/users/${id}`, { ...data, _method: 'PUT' }, { headers: { 'Content-Type': 'multipart/form-data' } }),
    delete: (id) => api.delete(`/admin/users/${id}`),
    toggleActive: (id) => api.patch(`/admin/users/${id}/toggle-active`),
};

export const adminBooksAPI = {
    all: (params) => api.get('/admin/books', { params }),
    show: (id) => api.get(`/admin/books/${id}`),
    create: (data) => api.post('/admin/books', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
    update: (id, data) => api.post(`/admin/books/${id}`, data, { headers: { 'Content-Type': 'multipart/form-data' } }),
    delete: (id) => api.delete(`/admin/books/${id}`),
};

export const booksAPI = {
    all: (params) => api.get('/books', { params }),
    recent: () => api.get('/books/recent'),
    search: (params) => api.get('/books/search', { params }),
    show: (id) => api.get(`/books/${id}`),
};

export const reviewsAPI = {
    bookReviews: (id) => api.get(`/books/${id}/reviews`),
    create: (data) => api.post('/reviews', data),
    update: (id, data) => api.put(`/reviews/${id}`, data),
    delete: (id) => api.delete(`/reviews/${id}`),
    all: (params) => api.get('/admin/reviews', { params }),
    updateAdmin: (id, data) => api.put(`/admin/reviews/${id}`, data),
    deleteAdmin: (id) => api.delete(`/admin/reviews/${id}`),
};

export const favoritesAPI = {
    all: () => api.get('/favorites'),
    toggle: (bookId) => api.post(`/favorites/${bookId}/toggle`),
    check: (bookId) => api.get(`/favorites/${bookId}/check`),
};

export const borrowingsAPI = {
    borrow: (data) => api.post('/borrowings', data),
    myBorrowings: (params) => api.get('/my-borrowings', { params }),
    myHistory: (params) => api.get('/my-borrowings/history', { params }),
};

export const adminBorrowingsAPI = {
    all: (params) => api.get('/admin/borrowings', { params }),
    history: (params) => api.get('/admin/borrowings/history', { params }),
    show: (id) => api.get(`/admin/borrowings/${id}`),
    create: (data) => api.post('/admin/borrowings', data),
    confirm: (id, data) => api.post(`/admin/borrowings/${id}/confirm`, data),
    refuse: (id, data) => api.post(`/admin/borrowings/${id}/refuse`, data),
    updateDueDate: (id, data) => api.put(`/admin/borrowings/${id}/due-date`, data),
    return: (id) => api.post(`/admin/borrowings/${id}/return`),
};

export const finesAPI = {
    myFines: () => api.get('/my-fines'),
    pay: (id) => api.post(`/fines/${id}/pay`),
    payAdmin: (id) => api.post(`/admin/fines/${id}/pay`),
    all: (params) => api.get('/admin/fines', { params }),
    stats: () => api.get('/admin/fines/stats'),
    delete: (id) => api.delete(`/admin/fines/${id}`),
};

export const notificationsAPI = {
    all: (params) => api.get('/notifications', { params }),
    unreadCount: () => api.get('/notifications/unread-count'),
    markRead: (id) => api.patch(`/notifications/${id}/read`),
    markAllRead: () => api.patch('/notifications/read-all'),
    delete: (id) => api.delete(`/notifications/${id}`),
    deleteAll: () => api.delete('/notifications'),
    deleteAllRead: () => api.delete('/notifications/read-all'),
    allAdmin: (params) => api.get('/admin/notifications', { params }),
    unreadCountAdmin: () => api.get('/admin/notifications/unread-count'),
    markReadAdmin: (id) => api.patch(`/admin/notifications/${id}/read`),
    markAllReadAdmin: () => api.patch('/admin/notifications/read-all'),
    deleteAdmin: (id) => api.delete(`/admin/notifications/${id}`),
    deleteAllReadAdmin: () => api.delete('/admin/notifications/read-all'),
};

export const settingsAPI = {
    all: () => api.get('/admin/settings'),
    update: (data) => api.put('/admin/settings', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
};

export const categoriesAPI = {
    all: (params) => api.get('/categories', { params }),
    allAdmin: (params) => api.get('/admin/categories', { params }),
    create: (data) => api.post('/admin/categories', data),
    update: (id, data) => api.put(`/admin/categories/${id}`, data),
    delete: (id) => api.delete(`/admin/categories/${id}`),
};
