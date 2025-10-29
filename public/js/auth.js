/**
 * Auth Manager - Client-side authentication using localStorage
 * Matches the React version's auth.ts implementation
 */

const AuthManager = {
    /**
     * Simulate network delay
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    },

    /**
     * Get all users from localStorage
     */
    getUsers() {
        const users = localStorage.getItem('users');
        return users ? JSON.parse(users) : [];
    },

    /**
     * Save users to localStorage
     */
    saveUsers(users) {
        localStorage.setItem('users', JSON.stringify(users));
    },

    /**
     * Generate a simple token
     */
    generateToken(userId) {
        return btoa(`${userId}:${Date.now()}`);
    },

    /**
     * Generate UUID
     */
    generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    /**
     * Sign up a new user
     */
    async signUp(username, email, password) {
        await this.delay(800); // Simulate network delay

        const users = this.getUsers();

        // Check if user already exists
        if (users.some(u => u.email === email)) {
            return {
                success: false,
                message: 'An account with this email already exists'
            };
        }

        if (users.some(u => u.username === username)) {
            return {
                success: false,
                message: 'This username is already taken'
            };
        }

        // Create new user
        const newUser = {
            id: this.generateUUID(),
            username,
            email,
            createdAt: new Date().toISOString()
        };

        users.push(newUser);
        this.saveUsers(users);

        // Store password separately (in real app, this would be hashed server-side)
        const passwords = JSON.parse(localStorage.getItem('passwords') || '{}');
        passwords[newUser.id] = password;
        localStorage.setItem('passwords', JSON.stringify(passwords));

        const token = this.generateToken(newUser.id);
        localStorage.setItem('authToken', token);
        localStorage.setItem('currentUser', JSON.stringify(newUser));

        return {
            success: true,
            user: newUser,
            token,
            message: 'Account created successfully'
        };
    },

    /**
     * Sign in existing user
     */
    async signIn(email, password) {
        await this.delay(800); // Simulate network delay

        const users = this.getUsers();
        const user = users.find(u => u.email === email);

        if (!user) {
            return {
                success: false,
                message: 'Invalid email or password'
            };
        }

        // Check password
        const passwords = JSON.parse(localStorage.getItem('passwords') || '{}');
        if (passwords[user.id] !== password) {
            return {
                success: false,
                message: 'Invalid email or password'
            };
        }

        const token = this.generateToken(user.id);
        localStorage.setItem('authToken', token);
        localStorage.setItem('currentUser', JSON.stringify(user));

        return {
            success: true,
            user,
            token,
            message: 'Logged in successfully'
        };
    },

    /**
     * Sign out
     */
    signOut() {
        localStorage.removeItem('authToken');
        localStorage.removeItem('currentUser');
    },

    /**
     * Get current user
     */
    getCurrentUser() {
        const userStr = localStorage.getItem('currentUser');
        return userStr ? JSON.parse(userStr) : null;
    },

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!localStorage.getItem('authToken');
    },

    /**
     * Redirect to login if not authenticated
     */
    requireAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = '/sign-in';
            return false;
        }
        return true;
    }
};

// Make it available globally
window.AuthManager = AuthManager;
