<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            // Sumber transaksi: manual, customer_invoice, supplier_invoice
            $table->string('source_type')->default('manual')->after('description');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            // Status posting: sekali diposting, tidak bisa diedit/dihapus
            $table->boolean('is_posted')->default(false)->after('source_id');
            $table->timestamp('posted_at')->nullable()->after('is_posted');

            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropColumn(['source_type', 'source_id', 'is_posted', 'posted_at']);
        });
    }
};
