<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Novauto</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #000000;
            color: #ffffff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0a0a0a;
        }
        .header {
            background-color: #000000;
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #1a1a1a;
        }
        .logo {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 2px;
        }
        .logo-nova {
            color: #ffffff;
        }
        .logo-auto {
            color: #C9A84C;
        }
        .tagline {
            font-size: 10px;
            color: #7e7e7e;
            letter-spacing: 2px;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #b0b0b0;
            margin-bottom: 20px;
        }
        .cta-button {
            display: inline-block;
            background-color: #C9A84C;
            color: #000000;
            padding: 14px 32px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
            border-radius: 2px;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .features {
            background-color: #000000;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #1a1a1a;
        }
        .feature-item {
            margin-bottom: 20px;
            padding-left: 25px;
            position: relative;
        }
        .feature-item:before {
            content: "◆";
            position: absolute;
            left: 0;
            color: #C9A84C;
            font-size: 12px;
        }
        .feature-title {
            font-weight: 700;
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .feature-desc {
            font-size: 13px;
            color: #7e7e7e;
            line-height: 1.5;
        }
        .footer {
            background-color: #000000;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #1a1a1a;
        }
        .footer-text {
            font-size: 12px;
            color: #7e7e7e;
            line-height: 1.6;
        }
        .footer-links {
            margin-top: 20px;
        }
        .footer-link {
            color: #C9A84C;
            text-decoration: none;
            font-size: 12px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <span class="logo-nova">NOV</span><span class="logo-auto">AUTO</span>
            </div>
            <div class="tagline">Plateforme Automobile Premium du Bénin</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Bienvenue, {{ $user->nom }} !</div>

            <p class="text">
                Nous sommes ravis de vous accueillir sur <strong>Novauto</strong>, la plateforme automobile de référence au Bénin.
            </p>

            <p class="text">
                Votre compte <strong>{{ strtoupper($user->role) }}</strong> a été créé avec succès. Vous pouvez dès maintenant accéder à toutes les fonctionnalités de la plateforme.
            </p>

            <center>
                <a href="http://localhost:4200/auth/login" class="cta-button">Accéder à mon compte</a>
            </center>

            <!-- Features -->
            <div class="features">
                <div class="feature-item">
                    <div class="feature-title">Vendeurs Certifiés</div>
                    <div class="feature-desc">Tous nos vendeurs sont vérifiés (IFU, identité) pour garantir des transactions sûres.</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Réservation Sécurisée</div>
                    <div class="feature-desc">Réservez votre véhicule en ligne avec un système de paiement sécurisé via FedaPay.</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Transparence Totale</div>
                    <div class="feature-desc">Simulation des frais de douane, historique vérifié, inspection avant achat.</div>
                </div>
            </div>

            <p class="text">
                <strong>Vos identifiants de connexion :</strong><br>
                Email : <span style="color: #C9A84C;">{{ $user->email }}</span><br>
                Mot de passe : celui que vous avez défini lors de l'inscription
            </p>

            <p class="text">
                Si vous avez des questions, notre équipe est à votre disposition.
            </p>

            <p class="text" style="margin-top: 30px;">
                Cordialement,<br>
                <strong style="color: #C9A84C;">L'équipe Novauto</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                © 2026 Novauto. Tous droits réservés.<br>
                Plateforme automobile premium du Bénin.
            </p>
            <div class="footer-links">
                <a href="http://localhost:4200/catalogue" class="footer-link">Catalogue</a>
                <a href="http://localhost:4200/simulateur" class="footer-link">Simulateur</a>
                <a href="http://localhost:4200" class="footer-link">À propos</a>
            </div>
        </div>
    </div>
</body>
</html>
