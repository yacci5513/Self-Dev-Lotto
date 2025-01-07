<?php

namespace App\Console\Commands;

use App\Services\LottoService;
use Illuminate\Console\Command;

class FetchLatestLottoData extends Command
{
    protected $signature = 'lotto:fetch-latest';
    protected $description = '최신 회차 로또 데이터 가져오기';

    public function handle(LottoService $lottoService)
    {
        $this->info('최신 회차 데이터 가져오기 시작...');
        
        if ($lottoService->fetchLatestDraw()) {
            $this->info('최신 회차 데이터 가져오기 완료!');
        } else {
            $this->error('최신 회차 데이터 가져오기 실패!');
        }
    }
} 