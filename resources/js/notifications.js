document.addEventListener('alpine:init', () => {
    Alpine.data('notifications', () => ({
        notifications: [],
        unreadCount: 0,
        loading: false,

        init() {
            this.fetchNotifications();
            this.listenForNotifications();
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await axios.get('/notifications');
                this.notifications = response.data.notifications;
                this.unreadCount = response.data.unread_count;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        listenForNotifications() {
            if (!window.Echo || !window.userId) return;

            window.Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    this.notifications.unshift({
                        id: notification.id,
                        data: {
                            title: notification.title,
                            message: notification.message,
                            type: notification.type,
                            action_url: notification.action_url
                        },
                        created_at: notification.created_at,
                        read_at: null
                    });
                    this.unreadCount++;
                    
                    // Show a toast if available
                    if (window.showToast) {
                        window.showToast(notification.message, notification.type);
                    }
                });
        },

        async markAsRead(id) {
            try {
                await axios.post(`/notifications/${id}/read`);
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1 && !this.notifications[index].read_at) {
                    this.notifications[index].read_at = new Date();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await axios.post('/notifications/read-all');
                this.notifications.forEach(n => {
                    if (!n.read_at) n.read_at = new Date();
                });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Failed to mark all notifications as read:', error);
            }
        },

        clearUnreadCount() {
            // When opening the drawer, we might want to mark all as read automatically
            // or just hide the badge. The requirement says "if opened, the numbers go away"
            if (this.unreadCount > 0) {
                this.markAllAsRead();
            }
        }
    }));
});
