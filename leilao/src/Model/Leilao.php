<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    /** @var true */
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    private function ultimoLanceDoUsuario(Lance $lance): bool
    {
        $lanceDoUltimoUsuario = $this->lances[count($this->lances) - 1];
        return $lance->getUsuario() === $lanceDoUltimoUsuario->getUsuario();
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ultimoLanceDoUsuario($lance)) {
            throw new \DomainException('Usuário não pode propor 2 lances seguidos');
        }

        $usuario = $lance->getUsuario();

        $totalLancesUsuario = $this->contadorDeLancesPorUsuario($usuario);

        if ($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuário não pode propor mais de 5 lances por leilões');
        }

        $this->lances[] = $lance;
    }

    public function finalizaLeilao()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado()
    {
        return $this->finalizado;
    }

    public function getLances(): array
    {
        return $this->lances;
    }

    private function contadorDeLancesPorUsuario(Usuario $usuario): int
    {
        return array_reduce($this->lances, function ($totalAcumulado, Lance $lanceAtual) use ($usuario) {
            if ($lanceAtual->getUsuario() == $usuario) {
                return $totalAcumulado + 1;
            }
            return $totalAcumulado;
        }, 0);
    }
}
