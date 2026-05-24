# Guide de Test FedaPay - Novauto

## Configuration Ngrok (pour tester le callback webhook)

### 1. Installer ngrok
- Télécharger sur https://ngrok.com/download
- Extraire et ajouter au PATH

### 2. Démarrer ngrok
```bash
ngrok http 8000
```

Vous obtiendrez une URL publique comme : `https://abc123.ngrok.io`

### 3. Modifier temporairement le .env
```env
APP_URL=https://abc123.ngrok.io
FRONTEND_URL=http://localhost:4200
```

### 4. Redémarrer Laravel
```bash
php artisan config:clear
php artisan serve
```

Le callback FedaPay pointera maintenant vers : `https://abc123.ngrok.io/api/paiements/callback`

---

## Numéros de Test FedaPay Sandbox

### Mobile Money
- **Succès** : +22997000001 ou 97000001
- **Échec** : +22997000002 ou 97000002

### Carte Bancaire
- **Numéro** : 4242424242424242
- **CVV** : 123
- **Expiration** : 12/28

---

## Flux de Test Complet

### Sans ngrok (callback ne fonctionnera pas)
1. Créer réservation → Payer
2. Sur FedaPay, payer avec numéro test
3. Retour sur `/acheteur/paiement-retour`
4. La page vérifiera manuellement le statut (appel API `verifier`)

**Avantage** : Simple, pas de configuration
**Inconvénient** : Délai de 3-6 secondes pour voir le statut "APPROUVE"

### Avec ngrok (callback instantané)
1. Démarrer ngrok + modifier APP_URL
2. Créer réservation → Payer
3. FedaPay envoie callback immédiatement après paiement
4. Réservation confirmée + notifications envoyées instantanément
5. Page de retour affiche statut immédiatement

**Avantage** : Flux complet comme en production
**Inconvénient** : Nécessite ngrok

---

## Vérifications Après Paiement

### Base de données
```sql
-- Vérifier le paiement créé
SELECT * FROM paiements ORDER BY created_at DESC LIMIT 1;

-- Vérifier la réservation confirmée
SELECT * FROM reservations WHERE statut = 'CONFIRMEE' ORDER BY created_at DESC LIMIT 1;

-- Vérifier les notifications envoyées
SELECT * FROM notifications WHERE type = 'PAIEMENT' ORDER BY created_at DESC LIMIT 2;
```

### Logs Laravel
```bash
tail -f storage/logs/laravel.log
```

Rechercher :
- `"FedaPay initiation error"` (si échec initiation)
- `"FedaPay callback received"` (callback reçu)
- `"Payment approved"` (paiement confirmé)
- `"Creating notification for"` (notifications créées)

---

## Dépannage

### Paiement bloqué sur "EN_ATTENTE"
- Le callback n'a pas été reçu (normal en localhost sans ngrok)
- La page `paiement-retour` vérifiera automatiquement toutes les 3 secondes
- Si après 15-20 secondes toujours EN_ATTENTE, vérifier les logs FedaPay

### Erreur "Impossible d'initialiser le paiement"
- Vérifier que les clés FedaPay sont bien dans le .env
- Vérifier que composer require fedapay/fedapay-php est installé
- Vérifier les logs : `storage/logs/laravel.log`

### Transaction FedaPay non trouvée
- Vérifier sur le dashboard FedaPay : https://sandbox.fedapay.com/dashboard
- Connexion avec vos identifiants sandbox
- Onglet "Transactions" pour voir toutes les transactions créées

---

## URLs Importantes

- **Dashboard FedaPay Sandbox** : https://sandbox.fedapay.com/dashboard
- **Documentation FedaPay** : https://docs.fedapay.com
- **API FedaPay Status** : https://status.fedapay.com

---

## Test Rapide (Sans ngrok)

1. Démarrer Laravel + Angular
2. Créer compte acheteur (ou se connecter)
3. Aller sur une annonce → "Réserver"
4. Choisir date → "Payer X FCFA"
5. Sur FedaPay : entrer **97000001**
6. Valider le paiement
7. Attendre sur la page de retour (vérifiera automatiquement)
8. Après 3-6 secondes → "Paiement Confirmé ✅"

✅ **Le flux fonctionne même sans ngrok grâce à la vérification manuelle !**
