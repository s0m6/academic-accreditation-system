document.addEventListener('alpine:init', () => {
    // Create a global store to keep notifications synchronized across all components
    Alpine.store('notificationStore', {
        list: [],
        unreadCount: 0,
        loading: false,

        async fetch() {
            this.loading = true;
            try {
                const response = await axios.get('/notifications');
                this.list = response.data.notifications;
                this.unreadCount = response.data.unread_count;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        add(notification) {
            // Priority:
            // 1. notif_level (from real-time broadcast)
            // 2. data.type (from database/standard notification)
            // 3. type (fallback if not a class name)
            const validTypes = ['success', 'info', 'warning', 'error'];
            let type = 'info';

            if (notification.notif_level && validTypes.includes(notification.notif_level)) {
                type = notification.notif_level;
            } else if (notification.data && validTypes.includes(notification.data.type)) {
                type = notification.data.type;
            } else if (notification.type && validTypes.includes(notification.type)) {
                type = notification.type;
            }

            const newItem = {
                id: notification.id,
                isNew: true, // Mark as new for real-time highlighting
                data: {
                    title:      notification.title      || notification.data?.title      || '',
                    message:    notification.message    || notification.data?.message    || '',
                    type:       type,
                    action_url: notification.action_url || notification.data?.action_url || null,
                },
                created_at: notification.created_at || 'الآن',
                read_at: null
            };
            this.list.unshift(newItem);
            this.unreadCount++;
        },

        async markAsRead(id) {
            try {
                await axios.post(`/notifications/${id}/read`);
                const notification = this.list.find(n => n.id === id);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        async markAllAsRead() {
            if (this.unreadCount === 0) return;
            try {
                // We notify the server so the badge disappears on next refresh
                await axios.post('/notifications/read-all');
                
                // We clear the counter so the badge disappears NOW
                this.unreadCount = 0;

                // IMPORTANT: We do NOT loop through this.list to set read_at = now() here.
                // This keeps the items looking "bright" and "new" in the current session.
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        }
    });

    Alpine.data('notifications', () => ({
        init() {
            // Only the first instance (like in navbar) needs to trigger fetch/listen
            if (Alpine.store('notificationStore').list.length === 0 && !Alpine.store('notificationStore').loading) {
                Alpine.store('notificationStore').fetch();
                this.listenForNotifications();
            }
        },

        get notifications() { return Alpine.store('notificationStore').list },
        get unreadCount() { return Alpine.store('notificationStore').unreadCount },
        get loading() { return Alpine.store('notificationStore').loading },

        listenForNotifications() {
            if (!window.Echo || !window.userId) return;

            window.Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    Alpine.store('notificationStore').add(notification);
                    
                    if (window.showToast) {
                        const type = notification.notif_level || notification.data?.type || 'info';
                        window.showToast(notification.message, type);
                    }
                });
        },

        markAsRead(id) { Alpine.store('notificationStore').markAsRead(id) },
        markAllAsRead() { Alpine.store('notificationStore').markAllAsRead() },
        clearUnreadCount() { this.markAllAsRead() },

        getTypeIcon(type) {
            const icons = {
                success: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#16a34a" width="22" height="22"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>`,
                info:    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563eb" width="22" height="22"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5A.75.75 0 0 0 12 9Z" clip-rule="evenodd"/></svg>`,
                warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#d97706" width="22" height="22"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>`,
                error:   `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#dc2626" width="22" height="22"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd"/></svg>`,
            };
            return icons[type] || icons.info;
        }
    }));
});
