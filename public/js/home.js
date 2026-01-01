/**
 * JavaScript pour la page d'accueil
 * Gère le menu flottant, les modals et le chargement des produits via API
 */

class HomePage {
    constructor() {
        this.products = [];
        this.cart = this.loadCart();
        this.notifications = this.loadNotifications();
        this.filters = {
            category: null,
            minPrice: null,
            maxPrice: null,
            brand: null,
            search: null
        };
        
        this.init();
    }
    
    init() {
        this.initFloatingMenu();
        this.initSearchBar();
        this.loadProducts();
        this.updateCartBadge();
        this.updateNotificationBadge();
    }

    initSearchBar() {
        const input = document.getElementById('home-search-input');
        const button = document.getElementById('home-search-button');

        if (!input) return;

        let timer = null;
        const apply = () => {
            const q = (input.value || '').trim();
            this.filters.search = q !== '' ? q : null;
            this.loadProducts();
        };

        input.addEventListener('input', () => {
            if (timer) clearTimeout(timer);
            timer = setTimeout(apply, 350);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (timer) clearTimeout(timer);
                apply();
            }
        });

        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                if (timer) clearTimeout(timer);
                apply();
            });
        }
    }
    
    // ============================================
    // MENU FLOTTANT
    // ============================================
    initFloatingMenu() {
        const menuBtn = document.getElementById('floating-menu-btn');
        const menuItems = document.getElementById('floating-menu-items');
        
        if (!menuBtn || !menuItems) return;
        
        // Animation de vibration au chargement
        menuBtn.classList.add('vibrating');
        setTimeout(() => {
            menuBtn.classList.remove('vibrating');
        }, 500);
        
        // Toggle menu
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menuBtn.classList.toggle('active');
            menuItems.classList.toggle('active');
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!menuBtn.contains(e.target) && !menuItems.contains(e.target)) {
                menuBtn.classList.remove('active');
                menuItems.classList.remove('active');
            }
        });
        
        // Actions des items du menu
        document.getElementById('menu-user').addEventListener('click', () => {
            window.location.href = `${window.BASE_URL}/index.php?page=dashboard_shop`;
        });
        
        document.getElementById('menu-cart').addEventListener('click', () => {
            window.location.href = `${window.BASE_URL}/index.php?page=orders`;
        });
        
        document.getElementById('menu-filters').addEventListener('click', () => {
            this.openFiltersModal();
        });
        
        document.getElementById('menu-notifications').addEventListener('click', () => {
            this.openNotificationsModal();
        });
        
        document.getElementById('menu-add').addEventListener('click', () => {
            window.location.href = `${window.BASE_URL}/index.php?page=dashboard_shop`;
        });
    }
    
    // ============================================
    // CHARGEMENT DES PRODUITS
    // ============================================
    async loadProducts() {
        try {
            const params = new URLSearchParams();
            if (this.filters.category) params.append('category', this.filters.category);
            if (this.filters.minPrice) params.append('min_price', this.filters.minPrice);
            if (this.filters.maxPrice) params.append('max_price', this.filters.maxPrice);
            if (this.filters.brand) params.append('brand', this.filters.brand);
            if (this.filters.search) params.append('search', this.filters.search);
            
            const response = await fetch(`${window.BASE_URL}/api/products.php?${params.toString()}`);
            const data = await response.json();
            
            if (data.success) {
                this.products = data.products;
                this.renderProducts();
                this.updateFiltersData(data.filters);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des produits:', error);
        }
    }
    
    renderProducts() {
        const container = document.getElementById('products-container');
        if (!container) return;
        
        if (this.products.length === 0) {
            container.innerHTML = '<p class="text-muted" style="text-align: center; padding: var(--spacing-2xl);">Aucun produit trouvé</p>';
            return;
        }
        
        container.innerHTML = this.products.map(product => {
            const price = Number(product.price || 0);
            const promo = Number(product.promo_price || 0);
            const hasPromo = promo > 0 && promo < price;
            const currency = (product.shop_currency || 'XOF');
            const minOrderQty = Math.max(1, parseInt(product.min_order_qty || '1', 10) || 1);
            const viewUrl = `${window.BASE_URL}/index.php?page=product_detail&id=${product.id}`;
            const img = this.resolveImage(product.image);
            const starsPercent = Number(product.shop_stars || 0);
            const starsCount = Math.max(0, Math.min(5, Math.round((starsPercent / 100) * 5)));
            const starsHtml = `
                <div class="rating" title="${starsPercent.toFixed(0)}%">
                    ${[1,2,3,4,5].map(i => `<span class="star ${i <= starsCount ? 'filled' : ''}"><i class="fas fa-star"></i></span>`).join('')}
                </div>
            `;

            return `
                <div class="product-card">
                    <a href="${viewUrl}" style="display:block;">
                        <img src="${img}" alt="${this.escapeHtml(product.name)}" class="product-image" onerror="this.src='https://via.placeholder.com/300'">
                    </a>
                    <div class="product-info">
                        ${starsHtml}
                        <h3 class="product-name" title="${this.escapeHtml(product.name)}">${this.escapeHtml(product.name)}</h3>
                        <p class="text-muted" style="font-size: 14px; margin-bottom: var(--spacing-xs);">${this.escapeHtml(product.shop_name || '')}</p>
                        <p class="text-muted" style="font-size: 12px; margin-bottom: var(--spacing-xs);">
                            <span style="font-weight: 800;">Min:</span> ${minOrderQty}
                            <span style="margin-left: 8px;">•</span>
                            <span style="margin-left: 8px;">Prix: par unité</span>
                        </p>
                        <div class="product-price-row">
                            <span class="product-price-regular">${this.formatPrice(hasPromo ? promo : price, currency)}</span>
                            ${hasPromo ? `<span class="product-price-old">${this.formatPrice(price, currency)}</span>` : ``}
                        </div>
                        <div class="product-cta-row">
                            <a class="btn btn-cta-discreet" href="${viewUrl}">Voir</a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // ============================================
    // PANIER
    // ============================================
    loadCart() {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : [];
    }
    
    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
        this.updateCartBadge();
    }
    
    async addToCart(productId) {
        const product = this.products.find(p => p.id === productId);
        if (!product) return;

        const minOrderQty = Math.max(1, parseInt(product.min_order_qty || '1', 10) || 1);

        try {
            const res = await fetch(`${window.BASE_URL}/api/create_order.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: minOrderQty })
            });

            if (res.status === 401) {
                // Pas connecté => fallback panier local
                throw new Error('not_logged_in');
            }

            const data = await res.json();
            if (data && data.success) {
                this.showNotification(`Commande #${data.order_id} créée`);
                return;
            }
        } catch (e) {
            // Fallback : panier local
        }

        const cartItem = this.cart.find(item => item.id === productId);
        if (cartItem) {
            cartItem.quantity += minOrderQty;
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                shop_name: product.shop_name,
                quantity: minOrderQty
            });
        }

        this.saveCart();
        this.showNotification('Produit ajouté au panier');
    }
    
    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        if (document.getElementById('cart-modal').classList.contains('active')) {
            this.renderCartModal();
        }
    }
    
    updateCartBadge() {
        const badge = document.querySelector('#menu-cart .badge');
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        if (badge) {
            badge.textContent = totalItems > 0 ? totalItems : '';
            badge.style.display = totalItems > 0 ? 'flex' : 'none';
        }
    }
    
    openCartModal() {
        this.renderCartModal();
        document.getElementById('cart-modal').classList.add('active');
    }
    
    renderCartModal() {
        const modalBody = document.getElementById('cart-modal-body');
        if (!modalBody) return;
        
        if (this.cart.length === 0) {
            modalBody.innerHTML = '<div class="cart-empty"><i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: var(--spacing-md); opacity: 0.3;"></i><p>Votre panier est vide</p></div>';
            return;
        }
        
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        modalBody.innerHTML = `
            <div class="cart-items">
                ${this.cart.map(item => `
                    <div class="cart-item">
                        <img src="${item.image || 'https://via.placeholder.com/60'}" alt="${this.escapeHtml(item.name)}" class="cart-item-image" onerror="this.src='https://via.placeholder.com/60'">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${this.escapeHtml(item.name)}</div>
                            <div class="cart-item-price">${this.formatPrice(item.price)} x ${item.quantity}</div>
                        </div>
                        <button class="cart-item-remove" onclick="homePage.removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `).join('')}
            </div>
            <div class="cart-total">
                <span>Total:</span>
                <span>${this.formatPrice(total)}</span>
            </div>
        `;
    }
    
    // ============================================
    // FILTRES
    // ============================================
    updateFiltersData(filters) {
        this.availableCategories = filters.categories || [];
        this.availableBrands = filters.brands || [];
    }
    
    openFiltersModal() {
        this.renderFiltersModal();
        document.getElementById('filters-modal').classList.add('active');
    }
    
    renderFiltersModal() {
        const modalBody = document.getElementById('filters-modal-body');
        if (!modalBody) return;
        
        modalBody.innerHTML = `
            <div class="filter-section">
                <h3>Catégories</h3>
                <div class="filter-options">
                    <label class="filter-checkbox">
                        <input type="checkbox" value="" ${!this.filters.category ? 'checked' : ''} onchange="homePage.applyFilter('category', null)">
                        <span>Toutes</span>
                    </label>
                    ${(this.availableCategories || []).map(cat => `
                        <label class="filter-checkbox">
                            <input type="checkbox" value="${this.escapeHtml(cat)}" ${this.filters.category === cat ? 'checked' : ''} onchange="homePage.applyFilter('category', '${this.escapeHtml(cat)}')">
                            <span>${this.escapeHtml(cat)}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Marques</h3>
                <div class="filter-options">
                    <label class="filter-checkbox">
                        <input type="checkbox" value="" ${!this.filters.brand ? 'checked' : ''} onchange="homePage.applyFilter('brand', null)">
                        <span>Toutes</span>
                    </label>
                    ${(this.availableBrands || []).map(brand => `
                        <label class="filter-checkbox">
                            <input type="checkbox" value="${this.escapeHtml(brand)}" ${this.filters.brand === brand ? 'checked' : ''} onchange="homePage.applyFilter('brand', '${this.escapeHtml(brand)}')">
                            <span>${this.escapeHtml(brand)}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Prix</h3>
                <div class="price-range">
                    <input type="number" class="form-input price-input" placeholder="Min" value="${this.filters.minPrice || ''}" onchange="homePage.applyFilter('minPrice', this.value)">
                    <span>-</span>
                    <input type="number" class="form-input price-input" placeholder="Max" value="${this.filters.maxPrice || ''}" onchange="homePage.applyFilter('maxPrice', this.value)">
                </div>
            </div>
            
            <div class="filter-actions">
                <button class="btn btn-secondary btn-full" onclick="homePage.resetFilters()">Réinitialiser</button>
            </div>
        `;
    }
    
    applyFilter(type, value) {
        if (value === '' || value === null) {
            this.filters[type] = null;
        } else {
            this.filters[type] = value;
        }
        this.loadProducts();
    }
    
    resetFilters() {
        this.filters = {
            category: null,
            minPrice: null,
            maxPrice: null,
            brand: null,
            search: null
        };
        this.loadProducts();
        this.renderFiltersModal();
    }
    
    // ============================================
    // NOTIFICATIONS
    // ============================================
    loadNotifications() {
        const notifications = localStorage.getItem('notifications');
        return notifications ? JSON.parse(notifications) : [];
    }
    
    saveNotifications() {
        localStorage.setItem('notifications', JSON.stringify(this.notifications));
        this.updateNotificationBadge();
    }
    
    updateNotificationBadge() {
        const badge = document.querySelector('#menu-notifications .badge');
        const unreadCount = this.notifications.filter(n => !n.read).length;
        if (badge) {
            badge.textContent = unreadCount > 0 ? unreadCount : '';
            badge.style.display = unreadCount > 0 ? 'flex' : 'none';
        }
    }
    
    openNotificationsModal() {
        this.renderNotificationsModal();
        document.getElementById('notifications-modal').classList.add('active');
    }
    
    renderNotificationsModal() {
        const modalBody = document.getElementById('notifications-modal-body');
        if (!modalBody) return;
        
        if (this.notifications.length === 0) {
            modalBody.innerHTML = '<div class="notifications-empty"><i class="fas fa-bell" style="font-size: 48px; margin-bottom: var(--spacing-md); opacity: 0.3;"></i><p>Aucune notification</p></div>';
            return;
        }
        
        modalBody.innerHTML = `
            <div class="notification-list">
                ${this.notifications.map(notif => `
                    <div class="notification-item ${notif.read ? '' : 'unread'}" onclick="homePage.markNotificationAsRead(${notif.id})">
                        <div class="notification-title">${this.escapeHtml(notif.title)}</div>
                        <div class="notification-message">${this.escapeHtml(notif.message)}</div>
                        <div class="notification-time">${this.formatTime(notif.time)}</div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    markNotificationAsRead(id) {
        const notif = this.notifications.find(n => n.id === id);
        if (notif) {
            notif.read = true;
            this.saveNotifications();
            this.renderNotificationsModal();
        }
    }
    
    showNotification(message) {
        const notification = {
            id: Date.now(),
            title: 'Notification',
            message: message,
            time: new Date().toISOString(),
            read: false
        };
        this.notifications.unshift(notification);
        this.saveNotifications();
    }
    
    // ============================================
    // UTILITAIRES
    // ============================================
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    resolveImage(imagePath) {
        if (!imagePath) return 'https://via.placeholder.com/300';
        if (typeof imagePath !== 'string') return 'https://via.placeholder.com/300';
        if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath;
        if (imagePath.startsWith('/')) return `${window.BASE_URL}${imagePath}`;
        return `${window.BASE_URL}/${imagePath}`;
    }
    
    formatPrice(price, currency = 'XOF') {
        let cur = (currency || 'XOF').toString().trim().toUpperCase();
        if (cur === '') cur = 'XOF';
        try {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: cur
            }).format(Number(price || 0));
        } catch (e) {
            return `${Number(price || 0).toLocaleString('fr-FR')} ${cur}`;
        }
    }
    
    formatTime(timeString) {
        const date = new Date(timeString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'À l\'instant';
        if (minutes < 60) return `Il y a ${minutes} min`;
        if (hours < 24) return `Il y a ${hours}h`;
        if (days < 7) return `Il y a ${days}j`;
        return date.toLocaleDateString('fr-FR');
    }
}

// Initialiser la page
let homePage;

// Fermer les modals
document.addEventListener('DOMContentLoaded', () => {
    homePage = new HomePage();
    
    // Fermer les modals au clic sur l'overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });
    
    // Fermer les modals avec le bouton close
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal-overlay').classList.remove('active');
        });
    });
});

