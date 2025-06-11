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
        // Remove softDeletes from audits
        if (Schema::hasColumn('audits', 'deleted_at')) {
            Schema::table('audits', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Remove softDeletes from audit_items
        if (Schema::hasColumn('audit_items', 'deleted_at')) {
            Schema::table('audit_items', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Remove softDeletes from data_requests
        if (Schema::hasColumn('data_requests', 'deleted_at')) {
            Schema::table('data_requests', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Update audit_id in data_requests to cascade on delete
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Update audit_id in audit_items to cascade on delete
        Schema::table('audit_items', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });

        // Update audit_id in file_attachments to cascade on delete
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
        // Add softDeletes back to audits
        Schema::table('audits', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add softDeletes back to audit_items
        Schema::table('audit_items', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add softDeletes back to data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Remove cascade on delete for audit_id in data_requests
        Schema::table('data_requests', function (Blueprint $table) {
            $table->dropForeign(['audit_id']);
            $table->foreign('audit_id')->references('id')->on('audits');
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
