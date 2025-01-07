<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\WinningStat;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class LottoService
{
    private $client;
    private $baseUrl = "https://www.dhlottery.co.kr/common.do";

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchDrawData(int $drawNumber)
    {
        try {
            $response = $this->client->get($this->baseUrl, [
                'query' => [
                    'method' => 'getLottoNumber',
                    'drwNo' => $drawNumber
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!$data || isset($data['returnValue']) && $data['returnValue'] === 'fail') {
                return null;
            }

            // draws 테이블에 저장
            $draw = Draw::create([
                'draw_number' => $data['drwNo'],
                'draw_date' => $data['drwNoDate'],
                'number1' => $data['drwtNo1'],
                'number2' => $data['drwtNo2'],
                'number3' => $data['drwtNo3'],
                'number4' => $data['drwtNo4'],
                'number5' => $data['drwtNo5'],
                'number6' => $data['drwtNo6'],
                'bonus_number' => $data['bnusNo'],
            ]);

            // winning_stats 테이블에 저장
            WinningStat::create([
                'draw_id' => $draw->id,
                'rank1_winners' => $data['firstPrzwnerCo'],
                'rank1_prize_amount' => $data['firstWinamnt'],
                'total_prize_amount' => $data['totSellamnt'],
                'rank2_winners' => $data['secondPrzwnerCo'] ?? null,
                'rank2_prize_amount' => $data['secondWinamnt'] ?? null,
                'rank3_winners' => $data['thirdPrzwnerCo'] ?? null,
                'rank3_prize_amount' => $data['thirdWinamnt'] ?? null,
                'rank4_winners' => $data['fourthPrzwnerCo'] ?? null,
                'rank4_prize_amount' => $data['fourthWinamnt'] ?? 50000,
                'rank5_winners' => $data['fifthPrzwnerCo'] ?? null,
                'rank5_prize_amount' => $data['fifthWinamnt'] ?? 5000,
            ]);

            return $draw;

        } catch (\Exception $e) {
            Log::error('Lotto data fetch error: ' . $e->getMessage());
            return null;
        }
    }

    public function fetchAllDraws()
    {
        try {
            $currentDraw = 1;
            $hasMoreData = true;

            while ($hasMoreData) {
                try {
                    $response = $this->client->get($this->baseUrl, [
                        'query' => [
                            'method' => 'getLottoNumber',
                            'drwNo' => $currentDraw
                        ]
                    ]);

                    $data = json_decode($response->getBody(), true);

                    if (!$data || (isset($data['returnValue']) && $data['returnValue'] === 'fail')) {
                        $hasMoreData = false;
                        continue;
                    }

                    // draws 테이블에 저장
                    $draw = Draw::updateOrCreate(
                        ['draw_number' => $data['drwNo']],
                        [
                            'draw_date' => $data['drwNoDate'],
                            'number1' => $data['drwtNo1'],
                            'number2' => $data['drwtNo2'],
                            'number3' => $data['drwtNo3'],
                            'number4' => $data['drwtNo4'],
                            'number5' => $data['drwtNo5'],
                            'number6' => $data['drwtNo6'],
                            'bonus_number' => $data['bnusNo'],
                        ]
                    );

                    // winning_stats 테이블에 저장
                    WinningStat::updateOrCreate(
                        ['draw_id' => $draw->id],
                        [
                            'rank1_winners' => $data['firstPrzwnerCo'],
                            'rank1_prize_amount' => $data['firstWinamnt'],
                            'total_prize_amount' => $data['totSellamnt'],
                            'rank2_winners' => $data['secondPrzwnerCo'] ?? null,
                            'rank2_prize_amount' => $data['secondWinamnt'] ?? null,
                            'rank3_winners' => $data['thirdPrzwnerCo'] ?? null,
                            'rank3_prize_amount' => $data['thirdWinamnt'] ?? null,
                            'rank4_winners' => $data['fourthPrzwnerCo'] ?? null,
                            'rank4_prize_amount' => $data['fourthWinamnt'] ?? 50000,
                            'rank5_winners' => $data['fifthPrzwnerCo'] ?? null,
                            'rank5_prize_amount' => $data['fifthWinamnt'] ?? 5000,
                        ]
                    );

                    Log::info("회차 {$currentDraw}: 저장 완료");
                    $currentDraw++;

                    // API 호출 간격 조절 (초)
                    usleep(300000); // 0.3초 대기

                } catch (\Exception $e) {
                    Log::error("회차 {$currentDraw} 처리 중 오류: " . $e->getMessage());
                    $hasMoreData = false;
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('전체 데이터 가져오기 실패: ' . $e->getMessage());
            return false;
        }
    }

    public function fetchLatestDraw()
    {
        try {
            // 현재 DB에 저장된 최신 회차 조회
            $lastDrawInDB = Draw::orderBy('draw_number', 'desc')->first();
            $lastDrawNumber = $lastDrawInDB ? $lastDrawInDB->draw_number : 0;

            // 동행복권 최신 회차 조회
            $response = $this->client->get($this->baseUrl, [
                'query' => [
                    'method' => 'getLottoNumber',
                    'drwNo' => $lastDrawNumber + 1
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);

            // 새로운 회차가 있으면 저장
            if ($data && isset($data['returnValue']) && $data['returnValue'] !== 'fail') {
                return $this->fetchDrawData($data['drwNo']);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Latest lotto data fetch error: ' . $e->getMessage());
            return null;
        }
    }
} 