<?hh

class Obj implements ArrayAccess<string, string> {
  private array $container = array();
  public function __construct() {
    $this->container = array(
      "one"   => 1,
      "two"   => 2,
      "three" => 3,
    );
  }
  public function offsetSet($key, $value) {
    if (is_null($key)) {
      $this->container[] = $value;
    } else {
      $this->container[$key] = $value;
    }
  }
  public function offsetExists($offset) {
    return isset($this->container[$offset]);
  }
  public function offsetUnset($offset) {
    unset($this->container[$offset]);
  }
  public function offsetGet($offset) : ?string {
    return isset($this->container[$offset]) ? $this->container[$offset] : null;
  }
}

$obj = new Obj();

var_dump(isset($obj["two"]));
var_dump($obj["two"]);
unset($obj["two"]);
var_dump(isset($obj["two"]));
$obj["two"] = "A value";
var_dump($obj["two"]);
$obj[] = 'Append 1';
$obj[] = 'Append 2';
$obj[] = 'Append 3';
print_r($obj);