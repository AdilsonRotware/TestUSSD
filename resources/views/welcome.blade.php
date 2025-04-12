<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador USSD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }
        input, button {
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
        }
        pre {
            background: #eee;
            padding: 1rem;
            white-space: pre-wrap;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Simulador USSD</h2>
        <label for="phone">Telefone:</label>
        <input type="text" id="phone" value="+244900000000">

        <label for="text">Texto USSD:</label>
        <input type="text" id="text" placeholder="Ex: 1*12/04/2025*15h*Reunião">

        <button onclick="enviarUSSD()">Enviar</button>

        <h3>Resposta:</h3>
        <pre id="resposta">...</pre>
    </div>

    <script>
        async function enviarUSSD() {
            const phone = document.getElementById('phone').value;
            const text = document.getElementById('text').value;

            const formData = new URLSearchParams();
            formData.append('sessionId', '12345');
            formData.append('phoneNumber', phone);
            formData.append('text', text);

            try {
                const response = await fetch('/ussd', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                });
                const data = await response.text();
                document.getElementById('resposta').textContent = data;
            } catch (err) {
                document.getElementById('resposta').textContent = 'Erro: ' + err.message;
            }
        }
    </script>
</body>
</html>
