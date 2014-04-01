<?hh
use Hrrgn\HackSack\Container;

/**
 * ContainerTest
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
	public function testBindWithString()
	{
		$c = new Container;
		$c->bind('foo', 'stdClass');

		$this->assertInstanceOf('stdClass', $c->resolve('foo'));
	}

	public function testImplementsArrayAccess()
	{
		$c = new Container;
		$c->bind('foo', 'stdClass');

		$this->assertInstanceOf('ArrayAccess', $c);
		$this->assertInstanceOf('stdClass', $c['foo']);
	}
}