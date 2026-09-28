<?php

namespace App\Console\Commands;

use App\Models\BinaryNode;
use App\Models\BinarySummary;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mlm:recalculate-extremes')]
#[Description('Recalcula de forma precisa los punteros extreme_left_user_id y extreme_right_user_id para todos los usuarios')]
class RecalculateBinaryExtremesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando recalculo de punteros extremos binarios...');

        $nodes = BinaryNode::all()->keyBy('user_id');

        $this->withProgressBar($nodes, function (BinaryNode $node) use ($nodes) {
            // 1. Encontrar el extremo izquierdo exterior
            $extremeLeftId = null;
            $currLeftId = $node->left_child_id;
            while ($currLeftId && isset($nodes[$currLeftId])) {
                $extremeLeftId = $currLeftId;
                $currLeftId = $nodes[$currLeftId]->left_child_id;
            }

            // 2. Encontrar el extremo derecho exterior
            $extremeRightId = null;
            $currRightId = $node->right_child_id;
            while ($currRightId && isset($nodes[$currRightId])) {
                $extremeRightId = $currRightId;
                $currRightId = $nodes[$currRightId]->right_child_id;
            }

            BinarySummary::updateOrCreate(
                ['user_id' => $node->user_id],
                [
                    'extreme_left_user_id' => $extremeLeftId,
                    'extreme_right_user_id' => $extremeRightId,
                ]
            );
        });

        $this->newLine();
        $this->info('¡Recalculo de extremos completado con total consistencia!');

        return self::SUCCESS;
    }
}
