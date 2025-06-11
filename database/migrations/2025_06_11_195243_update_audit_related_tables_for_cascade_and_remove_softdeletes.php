<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove softDeletes
        foreach (['audits', 'audit_items', 'data_requests'] as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }

        // Ensure cascading delete on audit_id in data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_item_id in data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_item_id']);
            $table->foreign('audit_item_id')->references('id')->on('audit_items')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_id in audit_items
        Schema::table('audit_items', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_id in file_attachments
        Schema::table('file_attachments', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add softDeletes back
        foreach (['audits', 'audit_items', 'data_requests'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Remove cascade on delete for audit_id in data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits');
        });

        // Remove cascade on delete for audit_item_id in data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_item_id']);
            $table->foreign('audit_item_id')->references('id')->on('audit_items');
        });

        // Remove cascade on delete for audit_id in audit_items
        Schema::table('audit_items', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits');
        });

        // Remove cascade on delete for audit_id in file_attachments
        Schema::table('file_attachments', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits');
        });
    }
};