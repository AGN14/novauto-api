<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouvelle connexion Novauto</title>
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
    .info-box { background: #f9f9f9; border: 1px solid #e8e8e8; border-left: 3px solid #C9A84C; padding: 20px 24px; margin: 24px 0; }
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; font-size: 14px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #999; }
    .info-value { color: #111; font-weight: 700; }
    .alert { background: #fff8e1; border-left: 3px solid #C9A84C; padding: 16px 20px; margin: 24px 0; font-size: 14px; color: #555; }
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
      <p class="greeting">Nouvelle connexion détectée</p>
      <p class="text">
        Bonjour <strong>{{ $user->nom }}</strong>,<br>
        Une nouvelle connexion a été effectuée sur votre compte Novauto.
      </p>

      <div class="info-box">
        <div class="info-row">
          <span class="info-label">Compte</span>
          <span class="info-value">{{ $user->email }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Date</span>
          <span class="info-value">{{ $date }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Heure</span>
          <span class="info-value">{{ $heure }}</span>
        </div>
      </div>

      <div class="alert">
        ⚠️ Si vous n'êtes pas à l'origine de cette connexion,
        changez immédiatement votre mot de passe et contactez
        notre support à <strong>contact@novauto.bj</strong>.
      </div>

      <p class="text" style="font-size: 13px; color: #999;">
        Cet email a été envoyé automatiquement par Novauto
        pour assurer la sécurité de votre compte.
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