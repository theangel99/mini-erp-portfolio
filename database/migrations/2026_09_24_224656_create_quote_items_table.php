<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->comment('Povezan produkt (nullable za proste postavke)');
            $table->integer('sort')->default(0)->comment('Vrstni red');
            $table->text('description')->comment('Opis postavke');
            $table->string('unit')->comment('Enota mere');
            $table->decimal('quantity', 12, 3)->default(1)->comment('Količina');
            $table->decimal('unit_price', 12, 2)->default(0)->comment('Cena na enoto');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Popust v %');
            $table->string('vat_rate')->comment('DDV stopnja');

            // Izračunane vrednosti
            $table->decimal('line_net', 12, 2)->default(0)->comment('Neto vrednost vrstice');
            $table->decimal('line_vat', 12, 2)->default(0)->comment('DDV vrstice');
            $table->decimal('line_total', 12, 2)->default(0)->comment('Bruto vrednost vrstice');

            $table->timestamps();

            $table->index(['quote_id', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
