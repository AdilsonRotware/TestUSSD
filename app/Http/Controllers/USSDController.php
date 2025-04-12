<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meeting;
use Illuminate\Support\Facades\Log;

class USSDController extends Controller
{
public function handle(Request $request)
{
    $command = $request->input('command');
    $payload = $request->input('payload');
    $response = isset($payload['response']) ? $payload['response'] : "";

    // Início da sessão
    if ($command === 'initiate') {
        return response("CON Bem-vindo ao sistema de reuniões\n1. Marcar reunião", 200)
               ->header('Content-Type', 'text/plain');
    }

    // Divide os passos do menu
    $steps = explode("*", $response);
    $firstStep = $steps[0] ?? '';

    // Número do telefone do usuário
    $phoneNumber = $payload['phoneNumber'] ?? '0000';

    // Verifica se é o supervisor (por enquanto vamos assumir que é "9999")
    if ($phoneNumber == '9999') {
        return $this->handleSupervisorFlow($steps, $phoneNumber);
    } else {
        return $this->handleUserFlow($steps, $phoneNumber);
    }
}

    
    private function handleUserFlow($steps, $phoneNumber)
    {
        $response = "";

        if ($steps[0] == "") {
            $response = "CON Bem-vindo ao sistema de reuniões\n";
            $response .= "1. Marcar reunião";
        }

        else if ($steps[0] == "1" && count($steps) == 1) {
            $response = "CON Informe a data da reunião (dd/mm/yyyy):";
        }

        else if ($steps[0] == "1" && count($steps) == 2) {
            $response = "CON Informe a hora da reunião (ex: 14h):";
        }

        else if ($steps[0] == "1" && count($steps) == 3) {
            $response = "CON Informe o motivo da reunião:";
        }

        else if ($steps[0] == "1" && count($steps) == 4) {
            $date = $this->convertDate($steps[1]);
            $time = $steps[2];
            $reason = $steps[3];

            $user = User::firstOrCreate(
                ['phone' => $phoneNumber],
                ['name' => 'Usuário USSD', 'password' => bcrypt('123456')]
            );

            Meeting::create([
                'user_id' => $user->id,
                'date' => $date,
                'time' => $time,
                'reason' => $reason,
                'status' => 'pendente',
            ]);

            $response = "END Solicitação enviada ao supervisor. Aguarde resposta.";
        }

        else {
            $response = "END Opção inválida.";
        }

        return response($response)->header('Content-Type', 'text/plain');
    }

    private function handleSupervisorFlow($steps, $phoneNumber)
    {
        $response = "";

        // Etapa 0 - Lista de reuniões pendentes
        if (count($steps) == 1 && $steps[0] == "") {
            $pendentes = Meeting::where('status', 'pendente')->get();

            if ($pendentes->isEmpty()) {
                return response("END Sem reuniões pendentes.")
                    ->header('Content-Type', 'text/plain');
            }

            $response = "CON Reuniões pendentes:\n";
            foreach ($pendentes as $index => $m) {
                $response .= ($index + 1) . ". " . $m->date . " - " . $m->time . "\n";
            }
            $response .= "Escolha o nº da reunião:";
        }

        // Etapa 1 - Ver detalhes
        else if (count($steps) == 1 && is_numeric($steps[0])) {
            $index = intval($steps[0]) - 1;
            $meeting = Meeting::where('status', 'pendente')->get()[$index] ?? null;

            if (!$meeting) {
                return response("END Reunião não encontrada.")
                    ->header('Content-Type', 'text/plain');
            }

            $response = "CON Reunião:\n";
            $response .= "Data: {$meeting->date}\nHora: {$meeting->time}\nMotivo: {$meeting->reason}\n";
            $response .= "1. Aceitar\n2. Rejeitar";
        }

        // Etapa 2 - Aprovar ou rejeitar
        else if (count($steps) == 2 && is_numeric($steps[0]) && in_array($steps[1], ['1', '2'])) {
            $index = intval($steps[0]) - 1;
            $action = $steps[1];

            $meeting = Meeting::where('status', 'pendente')->get()[$index] ?? null;

            if (!$meeting) {
                return response("END Reunião não encontrada.")
                    ->header('Content-Type', 'text/plain');
            }

            $meeting->status = $action == '1' ? 'aceita' : 'rejeitada';
            $meeting->save();

            $response = "END Reunião " . ($action == '1' ? 'aceita' : 'rejeitada') . " com sucesso.";
        }

        else {
            $response = "END Opção inválida.";
        }

        return response($response)->header('Content-Type', 'text/plain');
    }

    private function convertDate($str)
    {
        try {
            [$d, $m, $y] = explode('/', $str);
            return "$y-$m-$d";
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}