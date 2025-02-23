<?php

namespace Elastica\Test\Connection\Strategy;

use Elastica\Connection;
use Elastica\Connection\Strategy\CallbackStrategy;
use Elastica\Test\Base;

/**
 * Description of CallbackStrategyTest.
 *
 * @author chabior
 *
 * @internal
 */
class CallbackStrategyTest extends Base
{
    /**
     * @group unit
     */
    public function testInvoke(): void
    {
        $count = 0;

        $callback = static function ($connections) use (&$count): Connection {
            ++$count;

            return \current($connections);
        };

        $mock = $this->createMock(Connection::class);
        $strategy = new CallbackStrategy($callback);
        $strategy->getConnection([$mock]);

        $this->assertEquals(1, $count);
    }

    /**
     * @group functional
     */
    public function testConnection(): void
    {
        $count = 0;

        $config = ['connectionStrategy' => static function ($connections) use (&$count): Connection {
            ++$count;

            return \current($connections);
        }];

        $client = $this->_getClient($config);
        $response = $client->toElasticaResponse($client->indices()->getAlias());

        $this->assertEquals(1, $count);

        $this->assertTrue($response->isOk());

        $strategy = $client->getConnectionStrategy();

        $this->assertInstanceOf(CallbackStrategy::class, $strategy);
    }
}
