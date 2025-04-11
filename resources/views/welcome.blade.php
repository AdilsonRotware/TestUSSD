<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste USSD</title>
</head>
<body>

<h1>Teste USSD</h1>

<!-- Formulário para enviar os dados via POST -->
<form action="http://localhost/Rotware/Rotware/public/ussd" method="POST">
    @csrf <!-- Laravel CSRF Protection Token -->

    <!-- Campo para o sessionId -->
    <input type="hidden" name="sessionId" value="12345">

    <!-- Campo para o phoneNumber -->
    <input type="hidden" name="phoneNumber" value="987654321">

    <!-- Campo para o texto USSD -->
    <label for="text">Texto USSD (ex: 1*10/10/2025*14h*Reunião importante):</label>
    <input type="text" id="text" name="text" required>

    <button type="submit">Enviar</button>
</form>

</body>
</html>
