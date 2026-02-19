(function () {
    class NotificationCenter {
        constructor(options = {}) {
            this.baseUrl = String(options.baseUrl || '').replace(/\/$/, '');
            this.scope = this.normalizeScope(options.scope || 'auto');
            this.shopId = Number(options.shopId || 0) || 0;
            this.limit = Math.max(1, Math.min(200, Number(options.limit || 100) || 100));
            this.pollIntervalMs = Math.max(3000, Number(options.pollIntervalMs || 10000) || 10000);
            this.onUpdate = typeof options.onUpdate === 'function' ? options.onUpdate : null;
            this.onError = typeof options.onError === 'function' ? options.onError : null;

            this.notifications = [];
            this.counts = { unread_total: 0, unread_orders: 0 };
            this.latestId = 0;
            this.initialized = false;
            this.timer = null;

            this.audioContext = null;
            this.audioUnlocked = false;
            this.setupAudioUnlock();
        }

        normalizeScope(scope) {
            const val = String(scope || '').toLowerCase().trim();
            if (val === 'shop' || val === 'user' || val === 'auto') {
                return val;
            }
            return 'auto';
        }

        setupAudioUnlock() {
            const unlock = () => {
                this.unlockAudio();
                document.removeEventListener('click', unlock, true);
                document.removeEventListener('keydown', unlock, true);
                document.removeEventListener('touchstart', unlock, true);
            };
            document.addEventListener('click', unlock, true);
            document.addEventListener('keydown', unlock, true);
            document.addEventListener('touchstart', unlock, true);
        }

        unlockAudio() {
            if (this.audioUnlocked) return;
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            try {
                this.audioContext = this.audioContext || new Ctx();
                const promise = this.audioContext.resume ? this.audioContext.resume() : null;
                if (promise && typeof promise.then === 'function') {
                    promise.then(() => {
                        this.audioUnlocked = this.audioContext.state === 'running';
                    }).catch(() => {});
                } else {
                    this.audioUnlocked = this.audioContext.state === 'running';
                }
            } catch (e) {
                // No-op: son non disponible sur ce navigateur/appareil.
            }
        }

        start() {
            this.refresh({ playSound: false }).catch((error) => this.emitError(error));
            if (this.timer) return;
            this.timer = window.setInterval(() => {
                this.refresh().catch((error) => this.emitError(error));
            }, this.pollIntervalMs);
        }

        stop() {
            if (!this.timer) return;
            window.clearInterval(this.timer);
            this.timer = null;
        }

        emitError(error) {
            if (this.onError) {
                this.onError(error);
            }
        }

        buildGetUrl() {
            const params = new URLSearchParams();
            params.set('scope', this.scope);
            params.set('limit', String(this.limit));
            if (this.shopId > 0) {
                params.set('shop_id', String(this.shopId));
            }
            params.set('_', String(Date.now()));
            return `${this.baseUrl}/api/notifications.php?${params.toString()}`;
        }

        normalizeNotification(raw) {
            return {
                id: Number(raw.id || 0) || 0,
                type: String(raw.type || ''),
                title: String(raw.title || ''),
                body: String(raw.body || ''),
                is_read: !!raw.is_read,
                created_at: String(raw.created_at || ''),
                data: raw.data && typeof raw.data === 'object' ? raw.data : null,
            };
        }

        setStateFromPayload(payload) {
            const list = Array.isArray(payload.notifications) ? payload.notifications : [];
            this.notifications = list.map((item) => this.normalizeNotification(item));
            const counts = payload.counts && typeof payload.counts === 'object' ? payload.counts : {};
            this.counts = {
                unread_total: Number(counts.unread_total || 0) || 0,
                unread_orders: Number(counts.unread_orders || 0) || 0,
            };
            this.latestId = Number(payload.latest_id || 0) || 0;
        }

        getState() {
            return {
                notifications: this.notifications.slice(),
                counts: { ...this.counts },
                latestId: this.latestId,
            };
        }

        async refresh(options = {}) {
            const playSound = options.playSound !== false;
            const prevLatest = this.latestId;
            const prevInitialized = this.initialized;

            const response = await fetch(this.buildGetUrl(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });
            const payload = await response.json();
            if (!response.ok || !payload || payload.success !== true) {
                throw new Error((payload && payload.error) || 'Erreur API notifications');
            }

            this.setStateFromPayload(payload);

            if (prevInitialized && playSound) {
                const newUnread = this.notifications.filter((item) => !item.is_read && item.id > prevLatest);
                this.playSoundsForNew(newUnread);
            }

            this.initialized = true;
            if (this.onUpdate) {
                this.onUpdate(this.getState());
            }
            return this.getState();
        }

        async postAction(action, extra = {}) {
            const body = {
                action,
                scope: this.scope,
                limit: this.limit,
                ...extra,
            };
            if (this.shopId > 0) {
                body.shop_id = this.shopId;
            }
            const response = await fetch(`${this.baseUrl}/api/notifications.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });
            const payload = await response.json();
            if (!response.ok || !payload || payload.success !== true) {
                throw new Error((payload && payload.error) || 'Erreur API notifications');
            }
            this.setStateFromPayload(payload);
            this.initialized = true;
            if (this.onUpdate) {
                this.onUpdate(this.getState());
            }
            return this.getState();
        }

        markRead(ids) {
            const normalized = Array.isArray(ids)
                ? ids.map((id) => Number(id || 0)).filter((id) => id > 0)
                : [];
            if (normalized.length === 0) {
                return Promise.resolve(this.getState());
            }
            return this.postAction('mark_read', { ids: normalized });
        }

        markAllRead() {
            return this.postAction('mark_all_read');
        }

        isOrderType(type) {
            return /^order_/i.test(String(type || ''));
        }

        playSoundsForNew(newUnread) {
            if (!Array.isArray(newUnread) || newUnread.length === 0) {
                return;
            }
            const hasOrder = newUnread.some((n) => this.isOrderType(n.type));
            const hasOther = newUnread.some((n) => !this.isOrderType(n.type));
            if (hasOrder) {
                this.playOrderSound();
            }
            if (hasOther) {
                const delay = hasOrder ? 180 : 0;
                window.setTimeout(() => this.playNotificationSound(), delay);
            }
        }

        playTone(frequency, startOffsetSec, durationSec, type = 'sine', gainValue = 0.03) {
            this.unlockAudio();
            if (!this.audioContext || this.audioContext.state !== 'running') {
                return;
            }
            const now = this.audioContext.currentTime;
            const osc = this.audioContext.createOscillator();
            const gain = this.audioContext.createGain();
            osc.type = type;
            osc.frequency.setValueAtTime(frequency, now + startOffsetSec);
            gain.gain.setValueAtTime(0.0001, now + startOffsetSec);
            gain.gain.exponentialRampToValueAtTime(gainValue, now + startOffsetSec + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + startOffsetSec + durationSec);
            osc.connect(gain);
            gain.connect(this.audioContext.destination);
            osc.start(now + startOffsetSec);
            osc.stop(now + startOffsetSec + durationSec + 0.02);
        }

        playNotificationSound() {
            this.playTone(880, 0.00, 0.10, 'sine', 0.028);
            this.playTone(1175, 0.12, 0.10, 'sine', 0.024);
        }

        playOrderSound() {
            this.playTone(330, 0.00, 0.11, 'triangle', 0.030);
            this.playTone(392, 0.12, 0.11, 'triangle', 0.028);
            this.playTone(523, 0.24, 0.12, 'triangle', 0.026);
        }

        formatRelativeTime(input) {
            const raw = String(input || '').trim();
            const normalized = raw.includes(' ') && !raw.includes('T')
                ? raw.replace(' ', 'T')
                : raw;
            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) {
                return '';
            }
            const now = new Date();
            const diffMs = Math.max(0, now.getTime() - date.getTime());
            const min = Math.floor(diffMs / 60000);
            const hr = Math.floor(diffMs / 3600000);
            const day = Math.floor(diffMs / 86400000);

            if (min < 1) return "A l'instant";
            if (min < 60) return `Il y a ${min} min`;
            if (hr < 24) return `Il y a ${hr} h`;
            if (day < 7) return `Il y a ${day} j`;
            return date.toLocaleDateString('fr-FR');
        }
    }

    window.NotificationCenter = NotificationCenter;
})();
