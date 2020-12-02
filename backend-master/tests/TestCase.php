<?php

namespace Tests;

use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionObject;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MockeryPHPUnitIntegration;

    protected function setUp() : void {
        parent::setUp();
        DB::beginTransaction();

        Cache::flush();
    }

    protected function tearDown() : void {
        DB::rollBack();

        $this->getConnection()->disconnect();

        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        parent::tearDown();
    }

    public function assertModelIsDeleted(Model $model) {
        $model = $model->fresh();

        if (method_exists($model, "getDeletedAtColumn")) {
            $column = $model->getDeletedAtColumn();
            $this->assertNotNull($model->$column, "Failed asserting that model is soft deleted");
        } else {
            $this->assertTrue(is_null($model), "Failed asserting that model is deleted");
        }
    }

    public function assertEventIsBroadcasted($eventClassName, $channel = "") {
        $logfileFullpath = storage_path("logs/laravel.log");
        $logfile = explode("\n", file_get_contents($logfileFullpath));

        $goods = [];
        foreach ($logfile as $row) {
            if (strpos($row, "Broadcasting [" . $eventClassName . "]") !== false) {
                $goods[] = $row;
            } elseif (strpos($row, "Broadcasting [") !== false) {
                $goods = [];
            }
        }

        $this->assertNotEmpty($goods, "No event is broadcasted");

        $good = $goods[count($goods) - 1]; // last one

        $datetime_raw = substr($good, 1, 19);
        $datetime = Carbon::parse($datetime_raw);

        $testnow = Carbon::getTestNow();
        Carbon::setTestNow();
        $this->assertTrue($datetime->diffInSeconds(Carbon::now()) <= 20, "Broadcast happened more than twenty seconds ago");
        Carbon::setTestNow($testnow);

        if ($channel != "") {
            $this->assertContains("Broadcasting [" . $eventClassName . "] on channels [" . $channel . "]", $good, "The expected broadcast (" . $eventClassName . ") event was found, but not on the expected channel '" . $channel . "'.\n");
        }
    }

    public function assertValidationFailed($result) {
        $this->assertEquals("bad", $result["status"]);
        $this->assertEquals("validation_failed", $result["reason"]);
    }

    private function createBackupDB() {
        if (!file_exists(base_path('tests') . '/test.db.bak')) {
            copy(base_path('tests') . '/test.db', base_path('tests') . '/test.db.bak');
        }
    }

    private function restoreBackupDB() {
        copy(base_path('tests') . '/test.db.bak', base_path('tests') . '/test.db');
    }

    public function getQ($url, $data = [], $headers = [])
    {
        $newUrl = $url . "?" . http_build_query($data);

        return $this->get($newUrl, $headers);
    }
}
