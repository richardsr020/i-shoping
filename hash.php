<?php
// generate_hash.php
echo "🔐 Générateur de hash de mot de passe\n";
echo "===================================\n";

if (isset($argv[1])) {
    $password = $argv[1];
} else {
    echo "Entrez le mot de passe à hasher: ";
    $password = trim(fgets(STDIN));
}

if (empty($password)) {
    echo "❌ Mot de passe vide\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\n📋 RÉSULTAT :\n";
echo "─────────────\n";
echo "Mot de passe : " . $password . "\n";
echo "Hash Bcrypt  : " . $hash . "\n";
echo "Longueur     : " . strlen($hash) . " caractères\n";

// Test de vérification
echo "\n🧪 VÉRIFICATION :\n";
if (password_verify($password, $hash)) {
    echo "✅ Le hash est valide et peut être vérifié\n";
} else {
    echo "❌ Erreur : le hash ne peut pas être vérifié\n";
}

echo "\n💡 POUR LA BASE DE DONNÉES :\n";
echo "INSERT INTO users (email, password) VALUES ('email@example.com', '" . $hash . "');\n";
?>