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

        // Helper to drop FK if it exists
        $dropForeignIfExists = function (string $tableName, string $columnName) {
            $foreignKeyExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_NAME', $tableName)
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('COLUMN_NAME', $columnName)
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists();

            if ($foreignKeyExists) {
                Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                    $table->dropForeign([$columnName]);
                });
            }
        };

        // Ensure cascading delete on audit_id in data_requests
        $dropForeignIfExists('data_requests', 'audit_id');
        Schema::table('data_requests', function (Blueprint $table) {
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_item_id in data_requests (missing in original)
        $dropForeignIfExists('data_requests', 'audit_item_id');
        Schema::table('data_requests', function (Blueprint $table) {
            $table->foreign('audit_item_id')->references('id')->on('audit_items')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_id in audit_items
        $dropForeignIfExists('audit_items', 'audit_id');
        Schema::table('audit_items', function (Blueprint $table) {
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Ensure cascading delete on audit_id in file_attachments
        $dropForeignIfExists('file_attachments', 'audit_id');
        Schema::table('file_attachments', function (Blueprint $table) {
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