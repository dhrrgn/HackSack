<?hh
namespace Hrrgn\HackSack;

class Container
{
    protected Map<string, Map<string, mixed>> $mappings;
    protected Map<string, mixed> $singletons;

    public function __construct(array $mappings = [])
    {
        $this->singletons = new Map();
        $this->mappings = new Map();

        foreach ($mappings as $alias => $concrete) {
            $this->bind($mappings);
        }
    }

    public function bind(string $alias, mixed $concrete = null, bool $singleton = false) : \void
    {
        if (is_null($concrete)) {
            $concrete = $alias;
        }

        if (is_object($concrete) && ! $concrete instanceof \Closure) {
            $this->singletons[$alias] = $concrete;
            return;
        }

        if (is_string($concrete)) {
            $concrete = $this->wrap($concrete);
        }

        $this->mappings[$alias] = Map {
            'concrete' => $concrete,
            'singleton' => $singleton,
        };
    }


    public function resolve(string $alias, array $args = []) : mixed
    {
        if ($this->singletons->contains($alias)) {
            return $this->singletons->get($alias);
        }

        if ($this->mappings->contains($alias)) {
            $concrete = $this->mappings[$alias]['concrete'];
            if ($concrete instanceof \Closure) {
                $result = $concrete();
            } else {
                $result = $concrete;
            }

            if ($this->mappings[$alias]['singleton']) {
                $this->singletons[$alias] = $result;
            }

            return $result;
        }

        $reflection = new \ReflectionClass($alias);
        $concrete = $reflection->newInstance();

        $this->mappings[$alias] = Map {
            'concrete' => $concrete,
            'singleton' => false,
        };

        return $concrete;
    }

    protected function wrap(string $concrete) : (function() : mixed)
    {
        return () ==> $this->resolve($concrete);
    }
}
