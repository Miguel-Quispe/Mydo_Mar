<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Credenciales de Acceso</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h2 style="color: #0f172a; border-bottom: 2px solid #38bdf8; padding-bottom: 10px;">MADEART S.R.L. — Acceso B2B</h2>
        <p>Hola, <strong>{{ $empleado->nombre_completo ?? $empleado->nombre_usuario_users }}</strong>:</p>
        <p>Se ha generado exitosamente tu cuenta de acceso operativo para la planta. A continuación tus credenciales:</p>
        
        <div style="background: #f1f5f9; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Usuario de acceso:</strong> <code>{{ $empleado->nombre_usuario_users }}</code></p>
            <p style="margin: 5px 0;"><strong>Contraseña temporal:</strong> <code>{{ $passwordPlano }}</code></p>
            <p style="margin: 5px 0;"><strong>Correo registrado:</strong> {{ $empleado->email_usuario_users }}</p>
        </div>

        <p>Por seguridad, te recomendamos cambiar tu contraseña al ingresar por primera vez al sistema.</p>
        <p style="font-size: 12px; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            Este es un mensaje automatizado generado por el núcleo transaccional de PolloMuebles. No respondas a este correo.
        </p>
    </div>
</body>
</html>
