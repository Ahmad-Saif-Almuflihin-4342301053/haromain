<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Models\JamaahPaket;
use App\Models\MstPerson;
use App\Models\MstPaket;
use App\Models\MstTravel;
use App\Models\AgenTravel;

class JamaahControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_paid_returns_data()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('findPaidJamaah')->with('P1')->andReturn([(object)['IDPerson'=>'P1']]);
        $this->app->instance(JamaahPaket::class, $jp);

        $response = $this->getJson('/api/Jamaah/paid/P1');
        $response->assertStatus(200)->assertJson(['status' => true]);
    }

    public function test_paidall_returns_404_when_empty()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('setLimit')->andReturnSelf();
        $jp->shouldReceive('findAllPaidJamaah')->andReturn([]);
        $this->app->instance(JamaahPaket::class, $jp);

        $response = $this->getJson('/api/Jamaah/paidall?IDTravel=TR1&IDPaket=PK1');
        $response->assertStatus(404)->assertJson(['status' => false]);
    }

    public function test_person_returns_data()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('setCari')->andReturnSelf();
        $jp->shouldReceive('setPage')->andReturnSelf();
        $jp->shouldReceive('setLimit')->andReturnSelf();
        $jp->shouldReceive('findByPerson')->andReturn([(object)['IDPerson'=>'P1']]);
        $this->app->instance(JamaahPaket::class, $jp);

        $response = $this->getJson('/api/Jamaah/person/P1');
        $response->assertStatus(200)->assertJson(['status' => true]);
    }

    public function test_detail_returns_data()
    {
        $detail = (object)[ 'IDPerson' => 'P1', 'IDTravel' => 'TR1', 'IDPaket' => 'PK1', 'IDPersonAgen' => 'A1', 'TanggalBooking' => now()->format('Y-m-d H:i:s') ];

        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('getDetail')->andReturn($detail);
        $jp->shouldReceive('getDokumenSyarat')->andReturn([]);
        $jp->shouldReceive('findPeserta')->andReturn([]);
        $this->app->instance(JamaahPaket::class, $jp);

        $mp = Mockery::mock(MstPerson::class);
        $mp->shouldReceive('getDetail')->andReturn((object)['NamaJamaah'=>'X']);
        $this->app->instance(MstPerson::class, $mp);

        $paket = Mockery::mock(MstPaket::class);
        $paket->shouldReceive('detailInstance')->andReturn((object)['BiayaTrip'=>100]);
        $paket->shouldReceive('getSyaratPaketInstance')->andReturn([]);
        $this->app->instance(MstPaket::class, $paket);

        $tv = Mockery::mock(MstTravel::class);
        $tv->shouldReceive('detail')->andReturn((object)['NamaTravel'=>'T']);
        $this->app->instance(MstTravel::class, $tv);

        $agt = Mockery::mock(AgenTravel::class);
        $agt->shouldReceive('findAgentForAgenAndBooking')->andReturn((object)['IDTravel'=>'TR1']);
        $this->app->instance(AgenTravel::class, $agt);

        $response = $this->getJson('/api/Jamaah/detail?IDTravel=TR1&IDPaket=PK1&IDPerson=P1');
        $response->assertStatus(200)->assertJsonPath('data.IDPerson', 'P1');
    }

    public function test_register_with_existing_person_succeeds()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('cekPersonTerdaftar')->andReturn([]);
        $jp->shouldReceive('registerJamaah')->andReturn(true);
        $this->app->instance(JamaahPaket::class, $jp);

        $mp = Mockery::mock(MstPerson::class);
        $mp->shouldReceive('getDetail')->andReturn((object)['IDPerson'=>'P1']);
        $this->app->instance(MstPerson::class, $mp);

        $paket = Mockery::mock(MstPaket::class);
        $paket->shouldReceive('detailInstance')->andReturn((object)['BiayaTrip'=>100]);
        $paket->shouldReceive('getSyaratPaketInstance')->andReturn([]);
        $this->app->instance(MstPaket::class, $paket);

        $payload = ['IDTravel'=>'TR1','IDPaket'=>'PK1','IDPerson'=>'P1','NoHP'=>'081234','Email'=>'a@b.com','NamaJamaah'=>'X'];
        $response = $this->postJson('/api/Jamaah', $payload);
        $response->assertStatus(201)->assertJson(['status' => true]);
    }

    public function test_dokumen_returns_406_when_no_file()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('findDokumen')->andReturn(null);
        $this->app->instance(JamaahPaket::class, $jp);

        $response = $this->postJson('/api/Jamaah/dokumen', ['IDTravel'=>'TR1','IDPaket'=>'PK1','IDPerson'=>'P1','NoUrutSyarat'=>1]);
        $response->assertStatus(406)->assertJson(['status' => false]);
    }

    public function test_update_person_succeeds()
    {
        $mp = Mockery::mock(MstPerson::class);
        $mp->shouldReceive('getByEmail')->andReturn([]);
        $mp->shouldReceive('getByNoHP')->andReturn([]);
        $mp->shouldReceive('getByNIK')->andReturn([]);
        $mp->shouldReceive('updatePerson')->andReturn(1);
        $this->app->instance(MstPerson::class, $mp);

        $payload = ['IDPerson'=>'PRS-2023-0000000074','NamaJamaah'=>'New','Email'=>'a@b.com','NoHP'=>'08123'];
        $response = $this->putJson('/api/Jamaah', $payload);
        $response->assertStatus(201)->assertJson(['status' => true]);
    }

    public function test_alert_toggle_and_testimoni()
    {
        $jp = Mockery::mock(JamaahPaket::class);
        $jp->shouldReceive('toggleAlert')->andReturn(true);
        $jp->shouldReceive('updateTestimoni')->andReturn(1);
        $this->app->instance(JamaahPaket::class, $jp);

        $resp1 = $this->putJson('/api/Jamaah/alert', ['IDPerson'=>'P1','IDPaket'=>'PK1','IDTravel'=>'TR1']);
        $resp1->assertStatus(200)->assertJson(['status' => true]);

        $resp2 = $this->postJson('/api/Jamaah/testimoni', ['IDPerson'=>'P1','IDTravel'=>'TR1','IDPaket'=>'PK1','Testimoni'=>'ok','RatingTestimoni'=>5]);
        $resp2->assertStatus(200)->assertJson(['status' => true]);
    }
}
