<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Models\MstItenerary;
use App\Models\JamaahPaket;

class IteneraryControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_data()
    {
        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('setIDTravel')->andReturnSelf();
        $itMock->shouldReceive('setIDPaket')->andReturnSelf();
        $itMock->shouldReceive('setHariKe')->andReturnSelf();
        $itMock->shouldReceive('setCari')->andReturnSelf();
        $itMock->shouldReceive('setPage')->andReturnSelf();
        $itMock->shouldReceive('setLimit')->andReturnSelf();
        $itMock->shouldReceive('findAll')->andReturn(collect([(object)['NoUrut' => 1, 'Agenda' => 'Test']]));

        $this->app->instance(MstItenerary::class, $itMock);

        $response = $this->getJson('/api/Itenerary?IDTravel=TR1');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
        $this->assertTrue($response->json('status'));
    }

    public function test_detail_returns_single_item()
    {
        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('setIDTravel')->andReturnSelf();
        $itMock->shouldReceive('setIDPaket')->andReturnSelf();
        $itMock->shouldReceive('setNoUrut')->andReturnSelf();
        $itMock->shouldReceive('setLimit')->andReturnSelf();
        $itMock->shouldReceive('findAll')->andReturn([ (object)['NoUrut' => 5, 'Agenda' => 'Detail'] ]);

        $this->app->instance(MstItenerary::class, $itMock);

        $response = $this->getJson('/api/Itenerary/detail?IDTravel=TR1&IDPaket=PK1&NoUrut=5');
        $response->assertStatus(200);
        $response->assertJsonPath('data.NoUrut', 5);
    }

    public function test_current_returns_not_found_when_jamaah_missing()
    {
        $jamaahMock = Mockery::mock(JamaahPaket::class);
        $jamaahMock->shouldReceive('cekPersonTerdaftar')->andReturn(null);
        $this->app->instance(JamaahPaket::class, $jamaahMock);

        // also mock MstItenerary but it should not be called when jamaah missing
        $itMock = Mockery::mock(MstItenerary::class);
        $this->app->instance(MstItenerary::class, $itMock);

        $response = $this->getJson('/api/Itenerary/current?IDTravel=TR1&IDPaket=PK1&IDPerson=P1');
        $response->assertStatus(404);
        $this->assertFalse($response->json('status'));
    }

    public function test_store_succeeds_when_valid()
    {
        // mock MstPaket->detail via container binding
        $paketObj = (object)[ 'TglPulang' => now()->addDay()->toDateTimeString(), 'IsAktif' => 1 ];
        $paketMock = Mockery::mock(\App\Models\MstPaket::class);
        $paketMock->shouldReceive('detail')->andReturn($paketObj);
        $this->app->instance(\App\Models\MstPaket::class, $paketMock);

        $jamaahMock = Mockery::mock(JamaahPaket::class);
        $jamaahMock->shouldReceive('cekPersonTerdaftar')->andReturn((object)['StatusJamaah' => 'Leader']);
        $this->app->instance(JamaahPaket::class, $jamaahMock);

        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('setIDTravel')->andReturnSelf();
        $itMock->shouldReceive('setIDPaket')->andReturnSelf();
        $itMock->shouldReceive('saveItinerary')->andReturn(true);
        $this->app->instance(MstItenerary::class, $itMock);

        $payload = [
            'IDTravel' => 'TR1', 'IDPaket' => 'PK1', 'TglMulai' => now()->toDateTimeString(),
            'TglSelesai' => now()->addHour()->toDateTimeString(), 'KoorLat' => null, 'KoorLong' => null,
            'Lokasi' => 'X', 'Agenda' => 'Y', 'IDPerson' => 'P1', 'HariKe' => 1
        ];

        $response = $this->postJson('/api/Itenerary', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('status'));
    }

    public function test_batch_succeeds_when_valid()
    {
        $paketObj = (object)[ 'TglPulang' => now()->addDay()->toDateTimeString(), 'IsAktif' => 1 ];
        $paketMock = Mockery::mock(\App\Models\MstPaket::class);
        $paketMock->shouldReceive('detail')->andReturn($paketObj);
        $this->app->instance(\App\Models\MstPaket::class, $paketMock);

        $jamaahMock = Mockery::mock(JamaahPaket::class);
        $jamaahMock->shouldReceive('cekPersonTerdaftar')->andReturn((object)['StatusJamaah' => 'Leader']);
        $this->app->instance(JamaahPaket::class, $jamaahMock);

        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('saveAll')->andReturn(true);
        $this->app->instance(MstItenerary::class, $itMock);

        $payload = [
            'IDTravel' => 'TR1', 'IDPaket' => 'PK1', 'IDPerson' => 'P1',
            'InsertData' => [ ['TglMulai' => now()->toDateTimeString(), 'TglSelesai' => now()->addHour()->toDateTimeString(), 'Lokasi' => 'A', 'Agenda' => 'B', 'HariKe' => 1] ]
        ];

        $response = $this->postJson('/api/Itenerary/batch', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('status'));
    }

    public function test_update_succeeds_when_valid()
    {
        $paketObj = (object)[ 'TglPulang' => now()->addDay()->toDateTimeString(), 'IsAktif' => 1 ];
        $paketMock = Mockery::mock(\App\Models\MstPaket::class);
        $paketMock->shouldReceive('detail')->andReturn($paketObj);
        $this->app->instance(\App\Models\MstPaket::class, $paketMock);

        $jamaahMock = Mockery::mock(JamaahPaket::class);
        $jamaahMock->shouldReceive('cekPersonTerdaftar')->andReturn((object)['StatusJamaah' => 'Leader']);
        $this->app->instance(JamaahPaket::class, $jamaahMock);

        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('setIDTravel')->andReturnSelf();
        $itMock->shouldReceive('setIDPaket')->andReturnSelf();
        $itMock->shouldReceive('setNoUrut')->andReturnSelf();
        $itMock->shouldReceive('updateItinerary')->andReturn(1);
        $this->app->instance(MstItenerary::class, $itMock);

        $payload = [ 'IDTravel' => 'TR1', 'IDPaket' => 'PK1', 'NoUrut' => 1, 'TglMulai' => now()->toDateTimeString(), 'TglSelesai' => now()->addHour()->toDateTimeString(), 'IDPerson' => 'P1' ];
        $response = $this->putJson('/api/Itenerary', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('status'));
    }

    public function test_destroy_succeeds_when_valid()
    {
        $paketObj = (object)[ 'TglPulang' => now()->addDay()->toDateTimeString(), 'IsAktif' => 1 ];
        $paketMock = Mockery::mock(\App\Models\MstPaket::class);
        $paketMock->shouldReceive('detail')->andReturn($paketObj);
        $this->app->instance(\App\Models\MstPaket::class, $paketMock);

        $jamaahMock = Mockery::mock(JamaahPaket::class);
        $jamaahMock->shouldReceive('cekPersonTerdaftar')->andReturn((object)['StatusJamaah' => 'Leader']);
        $this->app->instance(JamaahPaket::class, $jamaahMock);

        $itMock = Mockery::mock(MstItenerary::class);
        $itMock->shouldReceive('setIDTravel')->andReturnSelf();
        $itMock->shouldReceive('setIDPaket')->andReturnSelf();
        $itMock->shouldReceive('setNoUrut')->andReturnSelf();
        $itMock->shouldReceive('deleteItinerary')->andReturn(1);
        $this->app->instance(MstItenerary::class, $itMock);

        $payload = [ 'IDTravel' => 'TR1', 'IDPaket' => 'PK1', 'NoUrut' => 1, 'IDPerson' => 'P1' ];
        $response = $this->deleteJson('/api/Itenerary', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('status'));
    }
}
