<?php

namespace App\Console\Commands;

use App\Services\LottoService;
use Illuminate\Console\Command;

class FetchLottoData extends Command
{
    protected $signature = 'lotto:fetch';
    protected $description = '동행복권 데이터 가져오기';

    public function handle(LottoService $lottoService)
    {
        $this->info('데이터 가져오기 시작...');
        
        try {
            if ($lottoService->fetchAllDraws()) {
                $this->info('데이터 가져오기 완료!');
            } else {
                $this->error('데이터 가져오기 실패!');
            }
        } catch (\Exception $e) {
            $this->error('오류 발생: ' . $e->getMessage());
        }
    }
} 