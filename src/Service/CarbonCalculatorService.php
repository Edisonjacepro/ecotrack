<?php

namespace App\Service;

final class CarbonCalculatorService
{
    /** @var array<string, array<string, float>> */
    private array $factors;

    /**
     * @param array<string, array<string, float>> $factors
     */
    public function __construct(array $factors)
    {
        $this->factors = $factors;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function calculate(string $category, array $data): float
    {
        return match ($category) {
            'transport' => $this->calculateTransport($data),
            'energy' => $this->calculateEnergy($data),
            'food' => $this->calculateFood($data),
            'digital' => $this->calculateDigital($data),
            default => throw new \InvalidArgumentException(sprintf('Unsupported category "%s".', $category)),
        };
    }

    /** @param array<string, mixed> $data */
    private function calculateTransport(array $data): float
    {
        $distance = (float) ($data['distance_km'] ?? 0);
        $mode = (string) ($data['mode'] ?? 'car');
        $factor = $this->getFactor('transport', $mode);

        return $distance * $factor;
    }

    /** @param array<string, mixed> $data */
    private function calculateEnergy(array $data): float
    {
        $kwh = (float) ($data['kwh'] ?? 0);
        $type = (string) ($data['energy_type'] ?? 'electricity');
        $factor = $this->getFactor('energy', $type);

        return $kwh * $factor;
    }

    /** @param array<string, mixed> $data */
    private function calculateFood(array $data): float
    {
        $meals = (int) ($data['meals'] ?? 0);
        $mealType = (string) ($data['meal_type'] ?? 'meat');
        $factor = $this->getFactor('food', $mealType);

        return $meals * $factor;
    }

    /** @param array<string, mixed> $data */
    private function calculateDigital(array $data): float
    {
        $hours = (float) ($data['hours'] ?? 0);
        $activity = (string) ($data['activity'] ?? 'streaming_hour');
        $factor = $this->getFactor('digital', $activity);

        return $hours * $factor;
    }

    private function getFactor(string $group, string $key): float
    {
        if (!isset($this->factors[$group][$key])) {
            throw new \InvalidArgumentException(sprintf('Unsupported factor "%s" for "%s".', $key, $group));
        }

        return $this->factors[$group][$key];
    }
}