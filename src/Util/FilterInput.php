<?php declare(strict_types=1);

namespace GameWith\Oidc\Util;

/**
 * Class FilterInput
 * @package GameWith\Oidc\Util
 */
class FilterInput
{
    /**
     * @var array<string, int>
     */
    private $definition;

    /**
     * FilterInput constructor.
     *
     * @param array<string, int> $definition
     */
    public function __construct(array $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Filter values
     *
     * @param int $type
     * @param bool $addEmpty
     * @return mixed
     */
    public function values(int $type, bool $addEmpty = true)
    {
        return filter_input_array($type, $this->definition, $addEmpty);
    }
}
