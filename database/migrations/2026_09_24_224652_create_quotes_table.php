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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique()->comment('Številka ponudbe (P-{leto}-{0001})');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Avtor ponudbe');
            $table->date('issued_at')->comment('Datum izdaje');
            $table->date('valid_until')->comment('Veljavna do');
            $table->string('status')->default('draft')->comment('Status ponudbe');
            $table->text('notes')->nullable()->comment('Opombe');
            $table->text('terms')->nullable()->comment('Pogoji');

            // Seštevki
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Neto vsota');
            $table->decimal('discount_total', 12, 2)->default(0)->comment('Popust skupaj');
            $table->decimal('vat_total', 12, 2)->default(0)->comment('DDV skupaj');
            $table->decimal('total', 12, 2)->default(0)->comment('Skupaj z DDV');

            // Sledenje statusom
            $table->timestamp('sent_at')->nullable()->comment('Poslana dne');
            $table->timestamp('accepted_at')->nullable()->comment('Sprejeta dne');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
