<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenue sur Novauto</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f4f4f4; font-family: 'Arial', sans-serif; }
    .container { max-width: 600px; margin: 40px auto; background: #fff; }
    .header { background: #000; padding: 32px 40px; }
    .header-stripe { height: 3px; background: linear-gradient(to right, #C9A84C, #E8C97A); margin-bottom: 24px; }
    .logo { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 2px; }
    .logo span { color: #C9A84C; }
    .body { padding: 40px; }
    .greeting { font-size: 22px; font-weight: 700; color: #111; margin-bottom: 16px; }
    .text { font-size: 15px; color: #555; line-height: 1.7; margin-bottom: 16px; }
    .badge { display: inline-block; background: #000; color: #C9A84C; padding: 8px 20px; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; margin: 8px 0 24px; }
    .features { background: #f9f9f9; border-left: 3px solid #C9A84C; padding: 20px 24px; margin: 24px 0; }
    .feature { font-size: 14px; color: #333; padding: 6px 0; }
    .feature::before { content: '✓ '; color: #C9A84C; font-weight: 700; }
    .btn { display: inline-block; background: #C9A84C; color: #000; padding: 14px 32px; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; text-decoration: none; margin: 8px 0; }
    .footer { background: #111; padding: 24px 40px; text-align: center; }
    .footer-text { font-size: 12px; color: #7e7e7e; line-height: 1.6; }
    .footer-stripe { height: 2px; background: linear-gradient(to right, #C9A84C, #E8C97A); margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-stripe"></div>
      <div class="logo">NOV<span>AUTO</span></div>
    </div>

    <div class="body">
      <p class="greeting">Bienvenue, {{ $user->nom }} ! 🎉</p>
      <p class="text">
        Votre compte a été créé avec succès sur <strong>Novauto</strong>,
        la plateforme automobile premium du Bénin.
      </p>

      <div class="badge">
        @if($user->role === 'ACHETEUR')
          Compte Acheteur
        @elseif($user->role === 'VENDEUR')
          Compte Vendeur
        @endif
      </div>

      <div class="features">
        <div class="feature">Consultez des milliers de véhicules certifiés</div>
        <div class="feature">Simulez votre coût de revient total</div>
        <div class="feature">Réservez en toute sécurité via Mobile Money</div>
        <div class="feature">Vérifiez l'historique technique via le décodeur VIN</div>
      </div>

      <p class="text">
        Vous pouvez dès maintenant accéder à votre espace personnel
        et profiter de toutes les fonctionnalités de la plateforme.
      </p>

      <a href="http://localhost:4200" class="btn">
        ACCÉDER À MON COMPTE →
      </a>

      <p class="text" style="margin-top: 24px; font-size: 13px; color: #999;">
        Si vous n'êtes pas à l'origine de cette inscription,
        ignorez cet email.
      </p>
    </div>

    <div class="footer">
      <div class="footer-stripe"></div>
      <p class="footer-text">
        © 2026 Novauto — Plateforme automobile premium du Bénin<br>
        Cotonou, Bénin · contact@novauto.bj
      </p>
    </div>
  </div>
</body>
</html>