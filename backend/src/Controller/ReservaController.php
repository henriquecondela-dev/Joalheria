<?php
declare(strict_types=1);

namespace APP\Controller;

use APP\Services\ReservaService;
use APP\padrao\Padroes;

class ReservaController
{
    public function __construct(
        private ReservaService $reservas,
        private Padroes $padrao
    ) {}
    
    public function criarReserva(int $idDoUsuario): void
    {
        $dados = $this->padrao->lerDadosJson();
        $reserva = $this->reservas->criarReserva(
            $idDoUsuario,
            $dados
        );
        $this->padrao->RespostaDados(
            201,
            "Reserva efectuada com sucesso!",
            $reserva,
            true
        );
    }

    public function listarMinhasReservas(int $idDoUsuario): void
    {
        $reservas = $this->reservas->listarMinhasReservas(
            $idDoUsuario
        );
        $this->padrao->RespostaDados(
            200,
            null,
            $reservas,
            true
        );
    }

    public function procurarReservaPorId(
        int $idDaReserva,
        int $idDoUsuario
    ): void {

        $reserva = $this->reservas->procurarReservaPorId(
            $idDaReserva,
            $idDoUsuario
        );
        if ($reserva === null) {
            $this->padrao->RespostaFalha(
                404,
                "Reserva não encontrada."
            );
            return;
        }

        $this->padrao->RespostaDados(
            200,
            null,
            $reserva,
            true
        );
    }

    public function cancelarReserva(
        int $idDaReserva,
        int $idDoUsuario
    ): void {
        $cancelada = $this->reservas->cancelarReserva(
            $idDaReserva,
            $idDoUsuario
        );

        if (!$cancelada) {
            $this->padrao->RespostaFalha(
                404,
                "Reserva não encontrada."
            );
            return;
        }
        $this->padrao->RespostaDados(
            200,
            "Reserva cancelada com sucesso!",
            null,
            true
        );
    }
}
