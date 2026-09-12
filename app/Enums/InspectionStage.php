<?php

namespace App\Enums;

enum InspectionStage: string
{
    case Incoming = 'incoming';
    case InProcess = 'in_process';
    case FinalQA = 'final_qa';

    public function label(): string
    {
        return match ($this) {
            self::Incoming => 'Incoming Inspection (Bahan Masuk)',
            self::InProcess => 'In-Process Quality Control (IPQC)',
            self::FinalQA => 'Final Quality Assurance (Outgoing)',
        };
    }
}
