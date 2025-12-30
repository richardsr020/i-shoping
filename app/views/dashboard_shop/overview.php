<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Bon retour !</h2>
        <p>Voici un aperçu de votre activité aujourd'hui</p>
    </div>
    <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=product_create">Ajouter un produit</a>
</div>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Commandes</h3>
            <div class="stat-value">142</div>
            <div class="stat-change"><i class="fas fa-arrow-up"></i> 12% ce mois</div>
        </div>
        <div class="stat-icon orders">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Revenus</h3>
            <div class="stat-value">$9,284</div>
            <div class="stat-change"><i class="fas fa-arrow-up"></i> 8% ce mois</div>
        </div>
        <div class="stat-icon revenue">
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Clients</h3>
            <div class="stat-value">1,248</div>
            <div class="stat-change"><i class="fas fa-arrow-up"></i> 5% ce mois</div>
        </div>
        <div class="stat-icon customers">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Produits</h3>
            <div class="stat-value">86</div>
            <div class="stat-change down"><i class="fas fa-arrow-down"></i> 2% ce mois</div>
        </div>
        <div class="stat-icon products">
            <i class="fas fa-box"></i>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="chart-container">
        <div class="chart-header">
            <h3>Ventes des 30 derniers jours</h3>
            <select>
                <option>30 derniers jours</option>
                <option>7 derniers jours</option>
                <option>90 derniers jours</option>
            </select>
        </div>
        <div class="chart-placeholder">
            Graphique des ventes - Intégration avec une bibliothèque de graphiques
        </div>
    </div>

    <div class="recent-orders">
        <div class="orders-header">
            <h3>Commandes récentes</h3>
            <a href="#">Voir tout</a>
        </div>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ORD-7842</td>
                    <td>Sophie Martin</td>
                    <td>$247.00</td>
                    <td><span class="status completed">Complétée</span></td>
                </tr>
                <tr>
                    <td>#ORD-7841</td>
                    <td>Thomas Bernard</td>
                    <td>$189.50</td>
                    <td><span class="status processing">En cours</span></td>
                </tr>
                <tr>
                    <td>#ORD-7840</td>
                    <td>Laura Petit</td>
                    <td>$312.75</td>
                    <td><span class="status pending">En attente</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="quick-actions">
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-plus"></i>
        </div>
        <h4>Ajouter un produit</h4>
        <p>Créez une nouvelle fiche produit pour votre boutique</p>
        <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=product_create">Commencer</a>
    </div>
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-cog"></i>
        </div>
        <h4>Paramètres boutique</h4>
        <p>Modifiez les paramètres de votre boutique</p>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=settings">Configurer</a>
    </div>
</div>
