<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LottoTestService;
use App\Models\Draw;
use App\Models\WinningStat;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class LottoSimulationTest extends TestCase
{
    private LottoTestService $service;
    private array $latestWinningNumbers;
    private int $bonusNumber;
    private array $winningPrizes;
    private const SIMULATION_COUNT = 1000; // 각 회차당 1000회
    private const LOTTO_PRICE = 1000;
    private const TARGET_DRAWS = 30; 

    #[Test]
    public function simulate_lotto_winning()
    {
        $this->service = new LottoTestService();
        
        $algorithms = [
            'A' => 'algorithmA',
            'B' => 'algorithmB',
            'C' => 'algorithmC',
            'E' => 'algorithmE',
            'F' => 'algorithmF',
            'G' => 'algorithmG',
            'H' => 'algorithmH',
            'I' => 'algorithmI',
            'J' => 'algorithmJ',
            'K' => 'algorithmK',
        ];

        $timestamp = date('Y-m-d_H-i-s');
        $baseFilePath = storage_path("app/simulation_results_{$timestamp}");

        // 최근 5회차 가져오기
        $recentDraws = Draw::orderBy('draw_number', 'desc')
                          ->take(self::TARGET_DRAWS)
                          ->get();

        $allResults = [];

        foreach ($recentDraws as $targetDraw) {
            $drawNumber = $targetDraw->draw_number;
            $drawResults = [];
            
            // 해당 회차 번호 설정
            $this->service->setTargetDrawNumber($drawNumber);
            
            // 해당 회차의 당첨번호 설정
            $this->latestWinningNumbers = [
                $targetDraw->number1,
                $targetDraw->number2,
                $targetDraw->number3,
                $targetDraw->number4,
                $targetDraw->number5,
                $targetDraw->number6
            ];
            $this->bonusNumber = $targetDraw->bonus_number;
            
            // 해당 회차의 당첨금액 설정
            $winningStat = WinningStat::where('draw_id', $targetDraw->id)->first();
            $this->winningPrizes = [
                1 => $winningStat->rank1_prize_amount > 0 ? $winningStat->rank1_prize_amount : 2000000000,
                2 => $winningStat->rank2_prize_amount > 0 ? $winningStat->rank2_prize_amount : 30000000,
                3 => $winningStat->rank3_prize_amount > 0 ? $winningStat->rank3_prize_amount : 1500000,
                4 => $winningStat->rank4_prize_amount > 0 ? $winningStat->rank4_prize_amount : 50000,
                5 => $winningStat->rank5_prize_amount > 0 ? $winningStat->rank5_prize_amount : 5000
            ];

            fwrite(STDOUT, "\n=== {$drawNumber}회차 시뮬레이션 시작 ===\n");

            foreach ($algorithms as $name => $method) {
                fwrite(STDOUT, "\n--- 알고리즘 {$name} 시작 ---\n");
                $results = $this->runSimulation($method, $drawNumber);
                $drawResults[$name] = $results;
                
                if (!isset($allResults[$name])) {
                    $allResults[$name] = $results;
                } else {
                    // 전체 결과에 현재 회차 결과 누적
                    foreach ([1, 2, 3, 4, 5, 'none'] as $rank) {
                        $allResults[$name][$rank]['count'] += $results[$rank]['count'];
                    }
                    $allResults[$name]['totalPrize'] += $results['totalPrize'];
                    $allResults[$name]['totalCost'] += $results['totalCost'];
                }
            }

            $this->saveComparison(
                "{$baseFilePath}_draw{$drawNumber}_comparison.txt",
                $drawNumber,
                $drawResults
            );
        }

        $this->saveTotalComparison("{$baseFilePath}_total_comparison.txt", $allResults);

        $this->assertTrue(true);
    }

    private function runSimulation($method, $targetDrawNumber)
    {
        $results = [
            1 => ['count' => 0, 'prize' => $this->winningPrizes[1]],
            2 => ['count' => 0, 'prize' => $this->winningPrizes[2]],
            3 => ['count' => 0, 'prize' => $this->winningPrizes[3]],
            4 => ['count' => 0, 'prize' => $this->winningPrizes[4]],
            5 => ['count' => 0, 'prize' => $this->winningPrizes[5]],
            'none' => ['count' => 0, 'prize' => 0]
        ];

        $totalCost = self::SIMULATION_COUNT * self::LOTTO_PRICE;
        $totalPrize = 0;

        for ($i = 0; $i < self::SIMULATION_COUNT; $i++) {
            $numbers = $this->service->{$method}()[0];
            $matchCount = count(array_intersect($numbers, $this->latestWinningNumbers));
            
            switch ($matchCount) {
                case 6:
                    $results[1]['count']++;
                    $totalPrize += $this->winningPrizes[1];
                    break;
                case 5:
                    if (in_array($this->bonusNumber, $numbers)) {
                        $results[2]['count']++;
                        $totalPrize += $this->winningPrizes[2];
                    } else {
                        $results[3]['count']++;
                        $totalPrize += $this->winningPrizes[3];
                    }
                    break;
                case 4:
                    $results[4]['count']++;
                    $totalPrize += $this->winningPrizes[4];
                    break;
                case 3:
                    $results[5]['count']++;
                    $totalPrize += $this->winningPrizes[5];
                    break;
                default:
                    $results['none']['count']++;
            }

            if (($i + 1) % 1000 === 0) {
                $progress = (($i + 1) / self::SIMULATION_COUNT) * 100;
                fwrite(STDOUT, "진행률: " . number_format($progress, 0) . "%\n");
            }
        }

        $results['totalCost'] = $totalCost;
        $results['totalPrize'] = $totalPrize;
        
        return $results;
    }

    private function saveComparison($filepath, $drawNumber, $results)
    {
        $content = "=== {$drawNumber}회차 알고리즘 비교 결과 ===\n\n";
        
        foreach ($results as $name => $result) {
            $content .= "\n알고리즘 {$name}:\n";
            $totalWinCount = 0;
            $totalPrize = 0;
            
            foreach ([1, 2, 3, 4, 5] as $rank) {
                $count = $result[$rank]['count'];
                $prize = $result[$rank]['prize'];
                $winRate = ($count / self::SIMULATION_COUNT) * 100;
                $rankTotalPrize = $count * $prize;
                
                $content .= "{$rank}등:\n";
                $content .= "  - 당첨횟수: " . number_format($count) . "회\n";
                $content .= "  - 당첨률: " . number_format($winRate, 6) . "%\n";
                $content .= "  - 당첨금액: " . number_format($prize) . "원\n";
                $content .= "  - 총 당첨금액: " . number_format($rankTotalPrize) . "원\n";
                
                $totalWinCount += $count;
                $totalPrize += $rankTotalPrize;
            }
            
            $totalWinRate = ($totalWinCount / self::SIMULATION_COUNT) * 100;
            $profitRate = ($result['totalPrize'] / $result['totalCost']) * 100;
            
            $content .= "\n종합 통계:\n";
            $content .= "  - 총 시뮬레이션 횟수: " . number_format(self::SIMULATION_COUNT) . "\n";
            $content .= "  - 총 당첨횟수: " . number_format($totalWinCount) . "회\n";
            $content .= "  - 총 당첨률: " . number_format($totalWinRate, 4) . "%\n";
            $content .= "  - 총 구매금액: " . number_format($result['totalCost']) . "원\n";
            $content .= "  - 총 당첨금액: " . number_format($result['totalPrize']) . "원\n";
            $content .= "  - 수익률: " . number_format($profitRate, 2) . "%\n";
        }

        // 해당 회차 알고리즘 간 비교
        $content .= "\n=== 알고리즘 순위 (수익률 기준) ===\n";
        $profitRates = [];
        foreach ($results as $name => $result) {
            $profitRates[$name] = ($result['totalPrize'] / $result['totalCost']) * 100;
        }
        arsort($profitRates);
        
        $rank = 1;
        foreach ($profitRates as $name => $rate) {
            $content .= "{$rank}위: 알고리즘 {$name} (" . number_format($rate, 2) . "%)\n";
            $rank++;
        }

        file_put_contents($filepath, $content);
    }

    private function saveTotalComparison($filepath, $allResults)
    {
        $content = "=== 전체 회차 종합 비교 결과 ===\n\n";
        $totalSimulations = self::SIMULATION_COUNT * self::TARGET_DRAWS;
        $totalCost = $totalSimulations * self::LOTTO_PRICE;
        
        $content .= "전체 시뮬레이션 정보:\n";
        $content .= "- 회차당 시도 횟수: " . number_format(self::SIMULATION_COUNT) . "회\n";
        $content .= "- 대상 회차 수: " . self::TARGET_DRAWS . "회\n";
        $content .= "- 총 시도 횟수: " . number_format($totalSimulations) . "회\n";
        $content .= "- 총 구매금액: " . number_format($totalCost) . "원\n\n";
        
        $content .= "알고리즘별 종합 결과:\n";
        foreach ($allResults as $name => $results) {
            $content .= "\n알고리즘 {$name}:\n";
            $totalWinCount = 0;
            
            foreach ([1, 2, 3, 4, 5] as $rank) {
                $count = $results[$rank]['count'];
                $prize = $results[$rank]['prize'];
                $winRate = ($count / $totalSimulations) * 100;
                $totalPrize = $count * $prize;
                
                $content .= "{$rank}등:\n";
                $content .= "  - 당첨횟수: " . number_format($count) . "회\n";
                $content .= "  - 당첨률: " . number_format($winRate, 6) . "%\n";
                $content .= "  - 총 당첨금액: " . number_format($totalPrize) . "원\n";
                
                $totalWinCount += $count;
            }
            
            $totalWinRate = ($totalWinCount / $totalSimulations) * 100;
            $returnMultiple = $results['totalPrize'] / $results['totalCost'];
            
            $content .= "\n종합 통계:\n";
            $content .= "  - 총 당첨횟수: " . number_format($totalWinCount) . "회\n";
            $content .= "  - 총 당첨률: " . number_format($totalWinRate, 4) . "%\n";
            $content .= "  - 총 당첨금액: " . number_format($results['totalPrize']) . "원\n";
            $content .= "  - 수익 배수: " . number_format($returnMultiple, 2) . "배\n";
        }

        // 수익 배수 순위
        $content .= "\n=== 알고리즘 순위 (수익 배수 기준) ===\n";
        $returnMultiples = [];
        foreach ($allResults as $name => $results) {
            $returnMultiples[$name] = $results['totalPrize'] / $results['totalCost'];
        }
        arsort($returnMultiples);
        
        foreach ($returnMultiples as $name => $multiple) {
            $totalPrize = $allResults[$name]['totalPrize'];
            $content .= "알고리즘 {$name}:\n";
            $content .= "  - 수익 배수: " . number_format($multiple, 2) . "배\n";
            $content .= "  - 총 당첨금액: " . number_format($totalPrize) . "원\n";
        }
        
        // 각 등수별 최고 당첨률 알고리즘
        $content .= "\n=== 각 등수별 최고 당첨률 ===\n";
        $hasWinningResults = false;

        foreach ([1, 2, 3, 4, 5] as $rank) {
            $maxRate = 0;
            $maxAlgo = null;
            $maxCount = 0;
            $maxPrize = 0;
            
            foreach ($allResults as $name => $results) {
                $count = $results[$rank]['count'];
                if ($count > 0) {  // 당첨이 있는 경우만 처리
                    $winRate = ($count / $totalSimulations) * 100;
                    $prize = $results[$rank]['prize'];
                    if ($winRate > $maxRate) {
                        $maxRate = $winRate;
                        $maxAlgo = $name;
                        $maxCount = $count;
                        $maxPrize = $prize;
                        $hasWinningResults = true;
                    }
                }
            }
            
            if ($maxAlgo !== null) {  // 당첨이 있는 경우만 출력
                $content .= "{$rank}등: 알고리즘 {$maxAlgo}\n";
                $content .= "  - 당첨횟수: " . number_format($maxCount) . "회\n";
                $content .= "  - 당첨률: " . number_format($maxRate, 6) . "%\n";
                $content .= "  - 당첨금액: " . number_format($maxPrize) . "원\n";
                $content .= "  - 총 당첨금액: " . number_format($maxCount * $maxPrize) . "원\n\n";
            }
        }

        if (!$hasWinningResults) {
            $content .= "당첨 결과가 없습니다.\n";
        }

        file_put_contents($filepath, $content);
    }
} 