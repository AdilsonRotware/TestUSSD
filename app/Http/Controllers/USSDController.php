<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UssdSession;
use App\Models\Meeting;
use App\Models\User;

class USSDController extends Controller
{
    public function handleSupervisor(Request $request)
{
    $sessionId = $request->input('sessionId');
    $phoneNumber = $request->input('phoneNumber');
    $text = $request->input('text');
    $steps = explode("*", $text);

    $response = "";

    // Verificar se o usuário é supervisor
    $user = User::where('phone', $phoneNumber)->first();
    if (!$user || !$user->is_supervisor) {
        $response = "END Você não tem permissão para acessar esta função.";
        return response($response)->header('Content-Type', 'text/plain');
    }

    // Etapa 1 - Listar as reuniões pendentes
    if ($text == "") {
        $meetings = Meeting::where('status', 'pendente')->get();
        if ($meetings->isEmpty()) {
            $response = "END Não há reuniões pendentes.";
        } else {
            $response = "CON Selecione uma reunião para aprovar ou rejeitar:\n";
            foreach ($meetings as $key => $meeting) {
                $response .= ($key + 1) . ". Reunião de " . $meeting->user->name . " para " . $meeting->date . " às " . $meeting->time . "\n";
            }
        }
    }

    // Etapa 2 - Aprovar ou rejeitar reunião
    elseif (count($steps) == 1) {
        $meeting = Meeting::where('status', 'pendente')->skip($steps[0] - 1)->first();
        if ($meeting) {
            $response = "CON Escolha uma opção:\n1. Aprovar\n2. Rejeitar";
        } else {
            $response = "END Reunião não encontrada.";
        }
    }

    // Etapa 3 - Ação de Aprovação ou Rejeição
    elseif (count($steps) == 2) {
        $meeting = Meeting::where('status', 'pendente')->skip($steps[0] - 1)->first();
        if ($meeting) {
            if ($steps[1] == "1") {
                $meeting->status = 'aceita';
                $response = "END Reunião aprovada.";
            } elseif ($steps[1] == "2") {
                $meeting->status = 'rejeitada';
                $response = "END Reunião rejeitada.";
            } else {
                $response = "END Opção inválida.";
            }
            $meeting->save();
        } else {
            $response = "END Reunião não encontrada.";
        }
    }

    return response($response)->header('Content-Type', 'text/plain');
}

    public function handle(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $phoneNumber = $request->input('phoneNumber');
        $text = $request->input('text');

        // Separar os passos digitados
        $steps = explode("*", $text);

        $response = "";

        // Etapa 0 - Menu Inicial
        if ($text == "") {
            $response = "CON Bem-vindo ao sistema de reuniões\n";
            $response .= "1. Marcar reunião";
        }

        // Etapa 1 - Inserir data
        else if ($steps[0] == "1" && count($steps) == 1) {
            $response = "CON Informe a data da reunião (dd/mm/yyyy):";
        }

        // Etapa 2 - Inserir hora
        else if ($steps[0] == "1" && count($steps) == 2) {
            $response = "CON Informe a hora da reunião (ex: 14h):";
        }

        // Etapa 3 - Inserir motivo
        else if ($steps[0] == "1" && count($steps) == 3) {
            $response = "CON Informe o motivo da reunião:";
        }

        // Etapa 4 - Criar a reunião e notificar o supervisor
        else if ($steps[0] == "1" && count($steps) == 4) {
            $date = $this->convertDate($steps[1]);
            $time = $steps[2];
            $reason = $steps[3];

            // Criar usuário se não existir
            $user = User::firstOrCreate(
                ['phone' => $phoneNumber],
                ['name' => 'Usuário USSD', 'password' => bcrypt('123456')]
            );

            $meeting = Meeting::create([
                'user_id' => $user->id,
                'date' => $date,
                'time' => $time,
                'reason' => $reason,
                'status' => 'pendente',
            ]);

            // Simula notificação ao supervisor (poderia ser SMS ou outro canal)
            $response = "END Solicitação enviada ao supervisor. Aguarde resposta.";
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
            return now();
        }
    }
}
