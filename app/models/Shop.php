
<?php
require_once __DIR__ . '/../config.php';

class Shop {
    public const CERTIFIED_STARS_THRESHOLD = 85.0;
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    private function slugify(string $value): string {
        $value = trim($value);
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = strtolower((string)$value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim((string)$value, '-');
        return $value !== '' ? $value : 'shop';
    }

    private function generateUniqueSlug(string $name, ?int $excludeShopId = null): string {
        $base = $this->slugify($name);
        $slug = $base;
        $i = 2;

        while (true) {
            if ($excludeShopId !== null) {
                $stmt = $this->db->prepare('SELECT 1 FROM shops WHERE slug = ? AND id != ? LIMIT 1');
                $stmt->execute([$slug, $excludeShopId]);
            } else {
                $stmt = $this->db->prepare('SELECT 1 FROM shops WHERE slug = ? LIMIT 1');
                $stmt->execute([$slug]);
            }
            if (!$stmt->fetchColumn()) {
                return $slug;
            }
            $slug = $base . '-' . $i;
            $i++;
        }
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function countByUserId(int $userId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM shops WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function findById(int $shopId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE id = ?');
        $stmt->execute([$shopId]);
        $shop = $stmt->fetch();
        return $shop ?: null;
    }

    public function findOwnedByUser(int $shopId, int $userId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE id = ? AND user_id = ?');
        $stmt->execute([$shopId, $userId]);
        $shop = $stmt->fetch();
        return $shop ?: null;
    }

    public function create(int $userId, array $data): int {
        $name = trim((string)($data['name'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        $logo = trim((string)($data['logo'] ?? ''));
        $banner = trim((string)($data['banner'] ?? ''));
        $emailContact = trim((string)($data['email_contact'] ?? ''));
        $phone = trim((string)($data['phone'] ?? ''));
        $address = trim((string)($data['address'] ?? ''));
        $city = trim((string)($data['city'] ?? ''));
        $country = trim((string)($data['country'] ?? ''));
        $currency = trim((string)($data['currency'] ?? ''));
        $paymentMethodsJson = $data['payment_methods_json'] ?? null;
        $status = trim((string)($data['status'] ?? ''));

        if ($name === '') {
            throw new Exception('Le nom de la boutique est requis.');
        }

        if ($description === '') {
            throw new Exception('La description de la boutique est requise.');
        }
        if ($logo === '') {
            throw new Exception('Le logo de la boutique est requis.');
        }
        if ($banner === '') {
            throw new Exception('La bannière de la boutique est requise.');
        }
        if ($emailContact === '') {
            throw new Exception('L\'email de contact est requis.');
        }
        if (!filter_var($emailContact, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email de contact invalide.');
        }
        if ($phone === '') {
            throw new Exception('Le téléphone de la boutique est requis.');
        }
        if (!preg_match('/^[0-9+()\\-\\.\\s]{6,30}$/', $phone)) {
            throw new Exception('Téléphone de contact invalide.');
        }
        if ($address === '') {
            throw new Exception('L\'adresse de la boutique est requise.');
        }
        if ($city === '') {
            throw new Exception('La ville de la boutique est requise.');
        }
        if ($country === '') {
            throw new Exception('Le pays de la boutique est requis.');
        }
        if ($currency === '') {
            throw new Exception('La devise de la boutique est requise.');
        }

        $allowedStatus = ['active', 'inactive'];
        if ($status === '') {
            throw new Exception('Le statut de la boutique est requis.');
        }
        if (!in_array($status, $allowedStatus, true)) {
            throw new Exception('Statut de boutique invalide.');
        }

        if ($paymentMethodsJson !== null) {
            if (!is_string($paymentMethodsJson)) {
                $paymentMethodsJson = null;
            } else {
                $decoded = json_decode($paymentMethodsJson, true);
                if (!is_array($decoded) || count($decoded) === 0) {
                    $paymentMethodsJson = null;
                } else {
                    $paymentMethodsJson = json_encode(array_values($decoded));
                }
            }
        }
        if ($paymentMethodsJson === null) {
            throw new Exception('Au moins un moyen de paiement est requis.');
        }

        $slug = $this->generateUniqueSlug($name);
        $url = BASE_URL . '/index.php?page=profile_shop&id=';

        $stmt = $this->db->prepare('
            INSERT INTO shops (
                user_id, name, slug, url, description, logo, banner, email_contact, phone, address, city, country, currency, status, payment_methods_json
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $userId,
            $name,
            $slug,
            $url,
            $description !== '' ? $description : null,
            $logo !== '' ? $logo : null,
            $banner !== '' ? $banner : null,
            $emailContact !== '' ? $emailContact : null,
            $phone !== '' ? $phone : null,
            $address !== '' ? $address : null,
            $city !== '' ? $city : null,
            $country !== '' ? $country : null,
            $currency,
            $status,
            $paymentMethodsJson
        ]);
        $newId = (int)$this->db->lastInsertId();

        $finalUrl = BASE_URL . '/index.php?page=profile_shop&id=' . $newId;
        $update = $this->db->prepare('UPDATE shops SET url = ? WHERE id = ?');
        $update->execute([$finalUrl, $newId]);

        return $newId;
    }

    public function update(int $shopId, int $userId, array $data): bool {
        $shop = $this->findOwnedByUser($shopId, $userId);
        if (!$shop) {
            throw new Exception('Boutique introuvable.');
        }

        $updates = [];
        $params = [];

        if (array_key_exists('name', $data)) {
            $name = trim((string)$data['name']);
            if ($name === '') {
                throw new Exception('Le nom de la boutique est requis.');
            }
            $updates[] = 'name = ?';
            $params[] = $name;

            $slug = $this->generateUniqueSlug($name, $shopId);
            $url = BASE_URL . '/index.php?page=profile_shop&id=' . $shopId;
            $updates[] = 'slug = ?';
            $params[] = $slug;
            $updates[] = 'url = ?';
            $params[] = $url;
        }

        if (array_key_exists('description', $data)) {
            $desc = trim((string)$data['description']);
            $updates[] = 'description = ?';
            $params[] = $desc !== '' ? $desc : null;
        }

        if (array_key_exists('logo', $data)) {
            $logo = trim((string)$data['logo']);
            $updates[] = 'logo = ?';
            $params[] = $logo !== '' ? $logo : null;
        }

        if (array_key_exists('banner', $data)) {
            $banner = trim((string)$data['banner']);
            $updates[] = 'banner = ?';
            $params[] = $banner !== '' ? $banner : null;
        }

        if (array_key_exists('email_contact', $data)) {
            $emailContact = trim((string)$data['email_contact']);
            if ($emailContact !== '' && !filter_var($emailContact, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email de contact invalide.');
            }
            $updates[] = 'email_contact = ?';
            $params[] = $emailContact !== '' ? $emailContact : null;
        }

        if (array_key_exists('phone', $data)) {
            $phone = trim((string)$data['phone']);
            $updates[] = 'phone = ?';
            $params[] = $phone !== '' ? $phone : null;
        }

        if (array_key_exists('address', $data)) {
            $address = trim((string)$data['address']);
            $updates[] = 'address = ?';
            $params[] = $address !== '' ? $address : null;
        }

        if (array_key_exists('city', $data)) {
            $city = trim((string)$data['city']);
            $updates[] = 'city = ?';
            $params[] = $city !== '' ? $city : null;
        }

        if (array_key_exists('country', $data)) {
            $country = trim((string)$data['country']);
            $updates[] = 'country = ?';
            $params[] = $country !== '' ? $country : null;
        }

        if (array_key_exists('currency', $data)) {
            $currency = trim((string)$data['currency']);
            if ($currency === '') {
                $currency = 'USD';
            }
            $updates[] = 'currency = ?';
            $params[] = $currency;
        }

        if (array_key_exists('status', $data)) {
            $status = trim((string)$data['status']);
            $allowedStatus = ['active', 'inactive'];
            if ($status === '' || !in_array($status, $allowedStatus, true)) {
                $status = 'active';
            }
            $updates[] = 'status = ?';
            $params[] = $status;
        }

        if (array_key_exists('payment_methods_json', $data)) {
            $pm = $data['payment_methods_json'];
            $pmJson = null;
            if (is_string($pm) && trim($pm) !== '') {
                $decoded = json_decode($pm, true);
                if (is_array($decoded)) {
                    $pmJson = json_encode(array_values($decoded));
                }
            } elseif (is_string($pm) && trim($pm) === '') {
                $pmJson = null;
            }
            $updates[] = 'payment_methods_json = ?';
            $params[] = $pmJson;
        }

        if (!$updates) {
            return false;
        }

        $updates[] = 'updated_at = CURRENT_TIMESTAMP';
        $params[] = $shopId;
        $params[] = $userId;

        $sql = 'UPDATE shops SET ' . implode(', ', $updates) . ' WHERE id = ? AND user_id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $shopId, int $userId): bool {
        $stmt = $this->db->prepare('DELETE FROM shops WHERE id = ? AND user_id = ?');
        return $stmt->execute([$shopId, $userId]);
    }

    public function updateStars(int $shopId, float $stars): bool {
        if ($shopId <= 0) {
            return false;
        }
        if ($stars < 0) {
            $stars = 0;
        }
        if ($stars > 100) {
            $stars = 100;
        }

        $stmt = $this->db->prepare('UPDATE shops SET stars = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        return $stmt->execute([$stars, $shopId]);
    }

    public function recalculateStars(int $shopId): float {
        if ($shopId <= 0) {
            return 0.0;
        }

        $stmt = $this->db->prepare('
            SELECT
                SUM(CASE WHEN satisfied = 1 AND COALESCE(canceled, 0) = 0 THEN 1 ELSE 0 END) AS satisfied_count,
                SUM(CASE WHEN COALESCE(canceled, 0) = 1 THEN 1 ELSE 0 END) AS canceled_count
            FROM orders
            WHERE shop_id = ?
        ');
        $stmt->execute([$shopId]);
        $row = $stmt->fetch() ?: [];

        $satisfiedCount = (int)($row['satisfied_count'] ?? 0);
        $canceledCount = (int)($row['canceled_count'] ?? 0);

        // Score (0..100):
        // +6 points par commande satisfaite, -10 points par commande annulée.
        // Ainsi, les annulations importantes font redescendre rapidement la note.
        $score = ($satisfiedCount * 6.0) - ($canceledCount * 10.0);
        if ($score < 0) {
            $score = 0.0;
        }
        if ($score > 100) {
            $score = 100.0;
        }
        $score = round($score, 2);
        $this->updateStars($shopId, $score);

        return $score;
    }

    public static function isCertifiedByStars(float $stars): bool {
        return $stars >= self::CERTIFIED_STARS_THRESHOLD;
    }
}
