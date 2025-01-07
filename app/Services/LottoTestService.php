<?php

namespace App\Services;

use App\Models\Draw;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class LottoTestService extends LottoGeneratorService
{
    private $targetDrawNumber;

    public function setTargetDrawNumber($drawNumber)
    {
        $this->targetDrawNumber = $drawNumber;
    }

    /**
     * 알고리즘 A: 기존 알고리즘
     * - 최신 당첨번호 1개
     * - 3~4번 출현 번호 2개
     * - 나머지 랜덤 3개
     * 특징: 중간 빈도수의 번호를 활용하여 균형잡힌 접근
     */
    public function algorithmA()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $latestNumbers = [
            $data['draw']->number1,
            $data['draw']->number2,
            $data['draw']->number3,
            $data['draw']->number4,
            $data['draw']->number5,
            $data['draw']->number6
        ];
        $numberFrequency = $data['frequency'];

        $numbers = [];
        
        // 최신 당첨번호에서 1개 선택
        $numbers[] = $latestNumbers[array_rand($latestNumbers)];
        
        // 3~4번 출현한 번호에서 2개 선택
        $frequentNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 3 && $count <= 4;
        }));
        
        $availableFrequent = array_diff($frequentNumbers, $numbers);
        for ($i = 0; $i < 2 && !empty($availableFrequent); $i++) {
            $randomIndex = array_rand($availableFrequent);
            $numbers[] = $availableFrequent[$randomIndex];
            unset($availableFrequent[$randomIndex]);
        }
        
        // 나머지 랜덤 3개
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $randomKey = array_rand($remainingNumbers);
            $numbers[] = $remainingNumbers[$randomKey];
            unset($remainingNumbers[$randomKey]);
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 B: 고빈도 집중형
     * - 최신 당첨번호 2개
     * - 4~5번 출현 번호 3개
     * - 나머지 랜덤 1개
     * 특징: 자주 나오는 번호에 집중하여 확률 높이기
     */
    public function algorithmB()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $latestNumbers = [
            $data['draw']->number1,
            $data['draw']->number2,
            $data['draw']->number3,
            $data['draw']->number4,
            $data['draw']->number5,
            $data['draw']->number6
        ];
        $numberFrequency = $data['frequency'];

        $numbers = [];
        
        // 최신 당첨번호에서 2개 선택
        $randomKeys = array_rand($latestNumbers, 2);
        foreach ($randomKeys as $key) {
            $numbers[] = $latestNumbers[$key];
        }
        
        // 4~5번 출현한 번호에서 3개 선택
        $frequentNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 4 && $count <= 5;
        }));
        
        $availableFrequent = array_diff($frequentNumbers, $numbers);
        for ($i = 0; $i < 3 && !empty($availableFrequent); $i++) {
            $randomIndex = array_rand($availableFrequent);
            $numbers[] = $availableFrequent[$randomIndex];
            unset($availableFrequent[$randomIndex]);
        }
        
        // 나머지 랜덤 1개
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $randomKey = array_rand($remainingNumbers);
            $numbers[] = $remainingNumbers[$randomKey];
            unset($remainingNumbers[$randomKey]);
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 C: 저빈도 집중형
     * - 최신 당첨번호 1개
     * - 1~2번 출현 번호 4개 (잘 안나온 번호)
     * - 나머지 랜덤 1개
     * 특징: 확률적 회귀를 노린 전략
     */
    public function algorithmC()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $latestNumbers = [
            $data['draw']->number1,
            $data['draw']->number2,
            $data['draw']->number3,
            $data['draw']->number4,
            $data['draw']->number5,
            $data['draw']->number6
        ];
        $numberFrequency = $data['frequency'];

        $numbers = [];
        
        // 최신 당첨번호에서 1개 선택
        $numbers[] = $latestNumbers[array_rand($latestNumbers)];
        
        // 1~2번 출현한 번호에서 4개 선택
        $rareNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 1 && $count <= 2;
        }));
        
        $availableRare = array_diff($rareNumbers, $numbers);
        for ($i = 0; $i < 4 && !empty($availableRare); $i++) {
            $randomIndex = array_rand($availableRare);
            $numbers[] = $availableRare[$randomIndex];
            unset($availableRare[$randomIndex]);
        }
        
        // 나머지 랜덤 1개
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $randomKey = array_rand($remainingNumbers);
            $numbers[] = $remainingNumbers[$randomKey];
            unset($remainingNumbers[$randomKey]);
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 D: 균형 분산형
     * - 각 빈도 구간(1-2회, 3-4회, 4-5회)에서 2개씩 선택
     * 특징: 모든 빈도 구간에서 골고루 선택하여 위험 분산
     */
    public function algorithmD()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        $numbers = [];
        
        // 1~2회 출현 번호에서 2개
        $lowFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 1 && $count <= 2;
        }));
        
        // 3~4회 출현 번호에서 2개
        $midFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 3 && $count <= 4;
        }));
        
        // 4~5회 출현 번호에서 2개
        $highFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 4 && $count <= 5;
        }));
        
        // 각 구간에서 2개씩 선택
        foreach ([$lowFreqNumbers, $midFreqNumbers, $highFreqNumbers] as $pool) {
            $available = array_diff($pool, $numbers);
            for ($i = 0; $i < 2 && !empty($available); $i++) {
                $randomIndex = array_rand($available);
                $numbers[] = $available[$randomIndex];
                unset($available[$randomIndex]);
            }
        }
        
        // 부족한 번호는 랜덤으로 채우기
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $randomKey = array_rand($remainingNumbers);
            $numbers[] = $remainingNumbers[$randomKey];
            unset($remainingNumbers[$randomKey]);
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 E: 연속성 기반형
     * - 최신 당첨번호의 연속된 2개 번호 선택
     * - 3~4회 출현 번호 2개
     * - 나머지 랜덤 2개
     * 특징: 연속된 번호의 출현 패턴을 활용
     */
    public function algorithmE()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $latestNumbers = [
            $data['draw']->number1,
            $data['draw']->number2,
            $data['draw']->number3,
            $data['draw']->number4,
            $data['draw']->number5,
            $data['draw']->number6
        ];
        $numberFrequency = $data['frequency'];

        $numbers = [];
        
        // 최신 당첨번호에서 연속된 2개 선택
        sort($latestNumbers);
        for ($i = 0; $i < count($latestNumbers) - 1; $i++) {
            if ($latestNumbers[$i + 1] - $latestNumbers[$i] == 1) {
                $numbers[] = $latestNumbers[$i];
                $numbers[] = $latestNumbers[$i + 1];
                break;
            }
        }
        
        // 연속된 번호가 없으면 랜덤하게 2개 선택
        if (empty($numbers)) {
            $randomKeys = array_rand($latestNumbers, 2);
            foreach ($randomKeys as $key) {
                $numbers[] = $latestNumbers[$key];
            }
        }
        
        // 3~4회 출현 번호에서 2개 선택
        $frequentNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 3 && $count <= 4;
        }));
        
        $availableFrequent = array_diff($frequentNumbers, $numbers);
        for ($i = 0; $i < 2 && !empty($availableFrequent); $i++) {
            $randomIndex = array_rand($availableFrequent);
            $numbers[] = $availableFrequent[$randomIndex];
            unset($availableFrequent[$randomIndex]);
        }
        
        // 나머지 랜덤 선택
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $randomKey = array_rand($remainingNumbers);
            $numbers[] = $remainingNumbers[$randomKey];
            unset($remainingNumbers[$randomKey]);
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 F: 고빈도 + 최근 당첨 조합형
     * - 최근 당첨번호에서 2개
     * - 고빈도 번호(4회 이상)에서 2개
     * - 중빈도 번호(2~3회)에서 2개
     */
    public function algorithmF()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        $latestNumbers = [
            $data['draw']->number1,
            $data['draw']->number2,
            $data['draw']->number3,
            $data['draw']->number4,
            $data['draw']->number5,
            $data['draw']->number6
        ];
        
        $numbers = [];
        
        // 1. 최근 당첨번호에서 2개 선택
        $selected = array_rand($latestNumbers, 2);
        foreach ($selected as $key) {
            $numbers[] = $latestNumbers[$key];
        }
        
        // 2. 고빈도 번호(4회 이상)에서 2개 선택
        $highFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 4;
        }));
        
        // 고빈도 번호가 충분하지 않으면 3회 이상으로 조정
        if (count($highFreqNumbers) < 2) {
            $highFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
                return $count >= 3;
            }));
        }
        
        // 이미 선택된 번호 제외
        $highFreqNumbers = array_diff($highFreqNumbers, $numbers);
        
        if (count($highFreqNumbers) >= 2) {
            $selected = array_rand(array_flip($highFreqNumbers), 2);
            if (!is_array($selected)) {
                $selected = [$selected];
            }
            foreach ($selected as $num) {
                $numbers[] = $num;
            }
        }
        
        // 3. 중빈도 번호(2~3회)에서 남은 자리 선택
        $midFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 2 && $count <= 3;
        }));
        
        // 이미 선택된 번호 제외
        $midFreqNumbers = array_diff($midFreqNumbers, $numbers);
        
        // 남은 자리 채우기
        while (count($numbers) < 6 && !empty($midFreqNumbers)) {
            $randKey = array_rand($midFreqNumbers);
            $numbers[] = $midFreqNumbers[$randKey];
            unset($midFreqNumbers[$randKey]);
        }
        
        // 여전히 6개가 안 되면 나머지는 랜덤 선택
        while (count($numbers) < 6) {
            $remaining = array_diff(range(1, 45), $numbers);
            $randKey = array_rand($remaining);
            $numbers[] = $remaining[$randKey];
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 G: 연속수 회피 + 합계 범위
     * - 연속된 번호 최대 1쌍만 허용
     * - 번호들의 합이 120-180 사이 (통계적 최적 범위)
     * - 최근 6개월 데이터에서 자주 나온 간격 활용
     */
    public function algorithmG()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        
        $numbers = [];
        arsort($numberFrequency);
        
        // 첫 번호 선택 (가장 많이 나온 번호)
        $numbers[] = array_key_first($numberFrequency);
        
        while (count($numbers) < 6) {
            $remainingNumbers = array_diff(range(1, 45), $numbers);
            $validNumbers = [];
            
            foreach ($remainingNumbers as $num) {
                // 현재 합계 계산
                $currentSum = array_sum($numbers) + $num;
                if (count($numbers) == 5 && ($currentSum < 120 || $currentSum > 180)) {
                    continue;
                }
                
                // 연속수 체크
                $hasConsecutive = false;
                foreach ($numbers as $existing) {
                    if (abs($existing - $num) == 1) {
                        $hasConsecutive = true;
                        break;
                    }
                }
                
                if ($this->hasConsecutivePair($numbers) && $hasConsecutive) {
                    continue;
                }
                
                $validNumbers[] = $num;
            }
            
            if (empty($validNumbers)) {
                $validNumbers = $remainingNumbers;
            }
            
            $numbers[] = $validNumbers[array_rand($validNumbers)];
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 H: 당첨 패턴 기반
     * - 최근 6개월 당첨번호의 패턴 분석
     * - 자주 나오는 간격과 조합 활용
     * - 미출현 기간이 긴 번호 포함
     */
    public function algorithmH()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        
        // 최근 6개월간 미출현 번호 찾기
        $rareNumbers = array_keys(array_filter($numberFrequency, fn($count) => $count == 0));
        
        $numbers = [];
        
        // 미출현 번호 1개 포함
        if (!empty($rareNumbers)) {
            $numbers[] = $rareNumbers[array_rand($rareNumbers)];
        }
        
        // 고빈도 번호에서 2개 선택
        arsort($numberFrequency);
        $highFreqNumbers = array_slice(array_keys($numberFrequency), 0, 10, true);
        $selected = array_rand($highFreqNumbers, 2);
        foreach ($selected as $key) {
            $numbers[] = $highFreqNumbers[$key];
        }
        
        // 나머지는 중간 범위(15-30)에서 선택
        $midRange = array_diff(range(15, 30), $numbers);
        while (count($numbers) < 6 && !empty($midRange)) {
            $num = array_rand(array_flip($midRange));
            $numbers[] = $num;
            $midRange = array_diff($midRange, [$num]);
        }
        
        // 부족한 번호는 전체 범위에서 선택
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        while (count($numbers) < 6) {
            $numbers[] = array_rand(array_flip($remainingNumbers));
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 I: 중빈도 중심형
     * - 3~4회 출현 번호 2개
     * - 나머지 랜덤 4개
     */
    public function algorithmI()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        
        $numbers = [];
        
        // 3~4회 출현한 번호 찾기
        $midFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 3 && $count <= 4;
        }));
        
        // 3~4회 출현 번호가 2개 이상 있으면 2개 선택, 없으면 랜덤 선택
        if (count($midFreqNumbers) >= 2) {
            $selected = array_rand($midFreqNumbers, 2);
            if (!is_array($selected)) $selected = [$selected];
            foreach ($selected as $key) {
                $numbers[] = $midFreqNumbers[$key];
            }
        } else {
            // 부족한 만큼 랜덤 선택
            $remainingCount = 2 - count($numbers);
            $availableNumbers = array_diff(range(1, 45), $numbers);
            $selected = array_rand($availableNumbers, $remainingCount);
            if (!is_array($selected)) $selected = [$selected];
            foreach ($selected as $key) {
                $numbers[] = $availableNumbers[$key];
            }
        }
        
        // 나머지 4개는 랜덤 선택
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        $selected = array_rand($remainingNumbers, 6 - count($numbers));
        if (!is_array($selected)) $selected = [$selected];
        foreach ($selected as $key) {
            $numbers[] = $remainingNumbers[$key];
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 J: 저빈도 중심형
     * - 2~3회 출현 번호 2개
     * - 나머지 랜덤 4개
     */
    public function algorithmJ()
    {
        $data = $this->getDrawAndFrequencyByDrawNumber($this->targetDrawNumber);
        $numberFrequency = $data['frequency'];
        
        $numbers = [];
        
        // 2~3회 출현한 번호 찾기
        $lowFreqNumbers = array_keys(array_filter($numberFrequency, function($count) {
            return $count >= 2 && $count <= 3;
        }));
        
        // 2~3회 출현 번호가 2개 이상 있으면 2개 선택, 없으면 랜덤 선택
        if (count($lowFreqNumbers) >= 2) {
            $selected = array_rand($lowFreqNumbers, 2);
            if (!is_array($selected)) $selected = [$selected];
            foreach ($selected as $key) {
                $numbers[] = $lowFreqNumbers[$key];
            }
        } else {
            // 부족한 만큼 랜덤 선택
            $remainingCount = 2 - count($numbers);
            $availableNumbers = array_diff(range(1, 45), $numbers);
            $selected = array_rand($availableNumbers, $remainingCount);
            if (!is_array($selected)) $selected = [$selected];
            foreach ($selected as $key) {
                $numbers[] = $availableNumbers[$key];
            }
        }
        
        // 나머지 4개는 랜덤 선택
        $remainingNumbers = array_diff(range(1, 45), $numbers);
        $selected = array_rand($remainingNumbers, 6 - count($numbers));
        if (!is_array($selected)) $selected = [$selected];
        foreach ($selected as $key) {
            $numbers[] = $remainingNumbers[$key];
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 알고리즘 K: 완전 랜덤형
     * - 1~45 중 랜덤 6개 선택
     */
    public function algorithmK()
    {
        $numbers = [];
        $availableNumbers = range(1, 45);
        
        // 6개 랜덤 선택
        $selected = array_rand($availableNumbers, 6);
        foreach ($selected as $key) {
            $numbers[] = $availableNumbers[$key];
        }
        
        sort($numbers);
        return [$numbers];
    }

    /**
     * 연속된 숫자 쌍이 있는지 확인하는 메서드
     */
    private function hasConsecutivePair($numbers)
    {
        if (empty($numbers)) return false;
        
        sort($numbers);
        for ($i = 0; $i < count($numbers) - 1; $i++) {
            if ($numbers[$i + 1] - $numbers[$i] == 1) {
                return true;
            }
        }
        return false;
    }

    protected function getDrawAndFrequencyByDrawNumber($targetDrawNumber)
    {
        // 해당 회차 정보 가져오기
        $targetDraw = Draw::where('draw_number', $targetDrawNumber)->first();

        // 이전 회차 정보 가져오기
        $previousDraw = Draw::where('draw_number', $targetDrawNumber - 1)->first();

        if (!$targetDraw || !$previousDraw) {
            throw new \Exception('해당 회차의 데이터를 찾을 수 없습니다.');
        }
        // 해당 회차 이전 6개월치 데이터 가져오기
        $sixMonthsAgo = $targetDraw->draw_date->copy()->subMonths(6);
        $numberFrequency = array_fill(1, 45, 0);

        Draw::where('draw_date', '>=', $sixMonthsAgo)
            ->where('draw_date', '<', $targetDraw->draw_date)
            ->where('draw_number', '!=', $targetDrawNumber)
            ->get()
            ->each(function ($draw) use (&$numberFrequency) {
                $numberFrequency[$draw->number1]++;
                $numberFrequency[$draw->number2]++;
                $numberFrequency[$draw->number3]++;
                $numberFrequency[$draw->number4]++;
                $numberFrequency[$draw->number5]++;
                $numberFrequency[$draw->number6]++;
            });

        return [
            'draw' => $previousDraw,
            'frequency' => $numberFrequency
        ];
    }
}