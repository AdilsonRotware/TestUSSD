<form action="http://localhost/Rotware/Rotware/public/ussd/supervisor" method="POST">
    @csrf
    <label for="sessionId">Session ID:</label>
    <input type="text" id="sessionId" name="sessionId" value="1234567890">
    <br><br>

    <label for="phoneNumber">Número de Telefone:</label>
    <input type="text" id="phoneNumber" name="phoneNumber" value="987654321">
    <br><br>

    <label for="text">Texto (opção de Supervisor):</label>
    <input type="text" id="text" name="text" value="1*">
    <br><br>

    <button type="submit">Enviar</button>
</form>
