<?php

namespace PromoCode\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PromoCodeUser implements Arrayable
{
    private int $userId;
    private int $usage;
    private int $maxUsage;

    private function __construct(int $userId, int $maxUsage, int $usage)
    {
        $this->userId = $userId;
        $this->usage = $usage;
        $this->maxUsage = $maxUsage;
    }

    public static function createFromArray(array $data): self
    {
        if (!Arr::has($data, 'user_id') || !Arr::has($data, 'max_usage')) {
            {
                throw new InvalidArgumentException('Invalid promo code users data provided');
            }
        }

        return new self($data['user_id'], $data['max_usage'], $data['usage'] ?? 0);
    }

    public function toArray()
    {
        return [
            'user_id'   => $this->getUserId(),
            'usage'     => $this->getUsage(),
            'max_usage' => $this->getMaxUsage(),
        ];
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getUsage(): int
    {
        return $this->usage;
    }

    /**
     * @return int
     */
    public function getMaxUsage(): int
    {
        return $this->maxUsage;
    }
}
