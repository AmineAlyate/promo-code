<?php

namespace PromoCode\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PromoCode\DTO\PromoCodeUser;

class PromoCode extends Model
{
    public const ID_COLUMN = 'id';
    public const COL_CODE = 'code';
    public const COL_VALUE = 'value';
    public const COL_TYPE = 'type';
    public const COL_END_DATE = 'end_date';
    public const COL_MAX_USAGE = 'max_usage';
    public const COL_USAGE_COUNT = 'usage_count';
    public const COL_USERS = 'users';

    public const TYPE_PERCENTAGE = 1;
    public const TYPE_VALUE = 2;

    protected $guarded = [];

    protected $dates = [
        self::COL_END_DATE,
    ];

    protected $casts = [
        self::COL_USERS => 'array',
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID_COLUMN);
    }

    public function getCode(): string
    {
        return $this->getAttribute(self::COL_CODE);
    }

    public function getValue(): float
    {
        return $this->getAttribute(self::COL_VALUE);
    }

    public function getTypeAsText(): string
    {
        return self::getAvailableTypes()[$this->getType()];
    }

    public static function getAvailableTypes(): array
    {
        // TODO - add translation
        return [
            self::TYPE_PERCENTAGE => 'Percentage',
            self::TYPE_VALUE      => 'Value',
        ];
    }

    public function getType(): int
    {
        return $this->getAttribute(self::COL_TYPE);
    }

    public function getEndDate(): ?Carbon
    {
        return $this->getAttribute(self::COL_END_DATE);
    }

    public function getUsageCount(): int
    {
        return $this->getAttribute(self::COL_USAGE_COUNT) ?? 0;
    }

    public function hasUser(int $id): bool
    {
        return $this->findUser($id) !== null;
    }

    private function findUser(int $id): ?PromoCodeUser
    {
        return $this->getUsers()->first(fn(PromoCodeUser $user) => $user->getUserId() === $id);
    }

    /**
     * @return Collection|PromoCodeUser[]
     */
    public function getUsers(): array|Collection
    {
        $users = $this->getAttribute(self::COL_USERS);

        return collect(
            array_map(function ($user) {
                return PromoCodeUser::createFromArray($user);
            }, $users)
        );
    }

    public function getMaxUsagePerUser(int $id): int
    {
        /** @var PromoCodeUser|null $user */
        $user = $this->findUser($id);

        return $user ? $user->getMaxUsage() : 0;
    }

    public function getMaxUsage(): ?int
    {
        return $this->getAttribute(self::COL_MAX_USAGE);
    }

    public function getUsageCountForUser(int $id): int
    {
        /** @var PromoCodeUser|null $user */
        $user = $this->findUser($id);

        return $user ? $user->getUsage() : 0;
    }
}
