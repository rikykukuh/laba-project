<?php

namespace Tests\Unit;

use App\DataTables\OrdersDataTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrdersDataTableEstimateFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedTinyInteger('transaction_type')->default(0);
            $table->string('status')->default('DIPROSES');
            $table->date('estimate_take_item')->nullable();
            $table->decimal('bruto', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('netto', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Carbon::setTestNow(Carbon::parse('2026-08-13 10:00:00', 'Asia/Jakarta'));

        foreach ([
            ['2026-08-13', 100],
            ['2026-08-14', 200],
            ['2026-08-15', 300],
            ['2026-08-16', 400],
        ] as [$estimateDate, $amount]) {
            DB::table('orders')->insert([
                'transaction_type' => 0,
                'status' => 'DIPROSES',
                'estimate_take_item' => $estimateDate,
                'bruto' => $amount,
                'netto' => $amount,
                'total' => $amount,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Schema::dropIfExists('orders');

        parent::tearDown();
    }

    public function test_it_filters_repair_completion_totals_by_estimate_date_range(): void
    {
        $this->setFilterRequest([
            'date_start' => '2026-08-14 00:00:00',
            'date_end' => '2026-08-15 23:59:59',
        ]);

        $dataTable = new OrdersDataTable();

        $this->assertEquals(500, $dataTable->total_bruto);
    }

    public function test_day_after_tomorrow_checkbox_overrides_estimate_date_range(): void
    {
        $this->setFilterRequest([
            'date_start' => '2026-08-13 00:00:00',
            'date_end' => '2026-08-13 23:59:59',
            'ready_day_after_tomorrow' => 'on',
        ]);

        $dataTable = new OrdersDataTable();

        $this->assertEquals(300, $dataTable->total_bruto);
    }

    private function setFilterRequest(array $parameters): void
    {
        $request = Request::create('/orders/selesai-besok', 'GET', array_merge([
            'is_ready_tomorrow' => 'True',
        ], $parameters));

        $this->app->instance('request', $request);
    }
}
