<?hh
namespace Hrrgn\HackSack;

use ArrayAccess;

type Mapping = shape('concrete' => mixed, 'singleton' => bool);

class Container implements ArrayAccess
{
    protected Map<string, Mapping> $mappings;
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

        $this->mappings[$alias] = shape(
            'concrete' => $concrete,
            'singleton' => $singleton,
        );
    }


    public function resolve(string $alias, array $args = []) : mixed
    {
        if ($this->singletons->contains($alias)) {
            return $this->singletons->get($alias);
        }

        if ($this->mappings->contains($alias)) {
            $concrete = $this->mappings[$alias]['concrete'];
            if ($concrete instanceof \Closure) {
                $result = $concrete($this);
            } else {
                $result = $concrete;
            }

            if ($this->mappings[$alias]['singleton']) {
                $this->singletons[$alias] = $result;
            }

            return $result;
        }

        // @todo - resolve constructor dependencies.
        $reflection = new \ReflectionClass($alias);
        $concrete = $reflection->newInstance();

        $this->mappings[$alias] = shape(
            'concrete' => $concrete,
            'singleton' => false,
        );

        return $concrete;
    }

    public function offsetSet(mixed $key, mixed $value) : this
    {
        $this->bind($key, $value, true);
    }

    public function offsetExists(mixed $offset) : bool
    {
        return isset($this->mappings[$offset]);
    }

    public function offsetUnset(mixed $offset) : this
    {
        $this->mappings->removeKey($offset);
    }

    public function offsetGet(mixed $offset) : mixed
    {
        return $this->resolve($offset);
    }

    protected function wrap(string $concrete) : (function(Container) : mixed)
    {
        return ($c) ==> $c->resolve($concrete);
    }
}
