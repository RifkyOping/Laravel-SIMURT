<?php

namespace App\Services;

use App\Models\PaketSoal;

class QuestionShuffleService
{
    /**
     * Mengacak ID soal untuk disimpan pada sesi ujian peserta (rule 02-f)
     *
     * @return array<int>
     */
    public function generateShuffledQuestionIds(PaketSoal $paketSoal): array
    {
        $questionIds = $paketSoal->bankSoals()
            ->where('aktif', true)
            ->pluck('id')
            ->toArray();

        shuffle($questionIds);

        return $questionIds;
    }
}
