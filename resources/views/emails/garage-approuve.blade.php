<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Garage Partenaire Approuvé</title>
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
        .logo-nova { color: #ffffff; }
        .logo-auto { color: #C9A84C; }
        .tagline {
            font-size: 10px;
            color: #7e7e7e;
            letter-spacing: 2px;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .content { padding: 40px 30px; }
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
        .success-banner {
            background: rgba(15, 163, 54, 0.1);
            border: 1px solid #0fa336;
            border-left: 4px solid #0fa336;
            padding: 20px 24px;
            margin: 24px 0;
        }
        .success-banner-title {
            font-size: 16px;
            font-weight: 700;
            color: #4ade80;
            margin-bottom: 6px;
        }
        .success-banner-text {
            font-size: 13px;
            color: #b0b0b0;
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
        .info-box {
            background-color: #000000;
            border: 1px solid #1a1a1a;
            padding: 24px;
            margin: 24px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #1a1a1a;
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #7e7e7e; }
        .info-value { color: #ffffff; font-weight: 600; }
        .info-value-gold { color: #C9A84C; font-weight: 700; }
        .features {
            background-color: #000000;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #1a1a1a;
        }
        .feature-item {
            margin-bottom: 16px;
            padding-left: 20px;
            position: relative;
        }
        .feature-item:before {
            content: "◆";
            position: absolute;
            left: 0;
            color: #C9A84C;
            font-size: 10px;
            top: 2px;
        }
        .feature-title {
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .feature-desc {
            font-size: 12px;
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
            <div class="greeting">Félicitations, {{ $garage->nom }} !</div>

            <div class="success-banner">
                <div class="success-banner-title">✓ Votre demande a été approuvée</div>
                <div class="success-banner-text">
                    Votre candidature en tant que Garage Partenaire NOVAuto a été examinée et approuvée par notre équipe.
                    Vous pouvez dès maintenant vous connecter à votre espace garage.
                </div>
            </div>

            <p class="text">
                Bienvenue dans le réseau des garages partenaires NOVAuto. En tant que partenaire agréé,
                vous pouvez désormais recevoir et traiter les demandes d'inspection de véhicules soumises
                par les vendeurs de la plateforme.
            </p>

            <!-- Infos de connexion -->
            <div class="info-box">
                <p style="font-size: 11px; font-weight: 700; color: #7e7e7e; letter-spacing: 1.5px; text-transform: uppercase; margin: 0 0 16px;">
                    Vos identifiants de connexion
                </p>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #1a1a1a; color: #7e7e7e; font-size: 13px;">Email</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #1a1a1a; color: #C9A84C; font-weight: 700; font-size: 13px; text-align: right;">{{ $garage->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #1a1a1a; color: #7e7e7e; font-size: 13px;">Mot de passe</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #1a1a1a; color: #ffffff; font-size: 13px; text-align: right;">Celui défini lors de votre inscription</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #7e7e7e; font-size: 13px;">Prix inspection</td>
                        <td style="padding: 8px 0; color: #C9A84C; font-weight: 700; font-size: 13px; text-align: right;">{{ number_format($garage->prix_inspection, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </table>
            </div>

            <center>
                <a href="http://localhost:4200/garage/login" class="cta-button">
                    Accéder à mon espace garage
                </a>
            </center>

            <!-- Ce que vous pouvez faire -->
            <div class="features">
                <p style="font-size: 11px; font-weight: 700; color: #7e7e7e; letter-spacing: 1.5px; text-transform: uppercase; margin: 0 0 16px;">
                    Ce que vous pouvez faire
                </p>
                <div class="feature-item">
                    <div class="feature-title">Gérer vos disponibilités</div>
                    <div class="feature-desc">Définissez vos créneaux horaires pour recevoir les demandes d'inspection.</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Recevoir des demandes d'inspection</div>
                    <div class="feature-desc">Les vendeurs peuvent vous soumettre leurs véhicules pour inspection et certification.</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Fixer votre tarif</div>
                    <div class="feature-desc">Vous pouvez personnaliser votre prix d'inspection depuis votre tableau de bord.</div>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Générer des rapports d'inspection</div>
                    <div class="feature-desc">Soumettez des rapports détaillés (carrosserie, moteur, freins, pneus) avec photos.</div>
                </div>
            </div>

            <p class="text">
                Si vous avez des questions, contactez notre équipe.
            </p>

            <p class="text" style="margin-top: 30px;">
                Cordialement,<br>
                <strong style="color: #C9A84C;">L'équipe NOVAuto</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                © 2026 NOVAuto. Tous droits réservés.<br>
                Plateforme automobile premium du Bénin.
            </p>
        </div>

    </div>
</body>
</html>