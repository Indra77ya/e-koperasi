<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNasabahLoansAndCreateInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update nasabah_loans table
        Schema::table('nasabah_loans', function (Blueprint $table) {
            $table->enum('loan_type', ['productive', 'consumptive'])->after('amount')->default('consumptive');
            $table->enum('interest_type', ['flat', 'effective', 'annuity'])->after('loan_type')->default('flat');
            $table->decimal('interest_rate', 5, 2)->after('interest_type')->default(0); // Yearly or Monthly? Usually yearly for calculation but let's assume yearly in logic.
            $table->integer('tenor')->after('interest_rate')->default(1); // In months
            $table->decimal('admin_fee', 15, 2)->after('tenor')->default(0);
            $table->enum('disbursement_method', ['cash', 'transfer'])->after('admin_fee')->default('cash');

            // Approval and Disbursement info
            $table->unsignedBigInteger('approved_by')->nullable()->after('notes');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('disbursed_at')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('disbursed_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            // Update status enum by modifying column (if DB supports or just rely on string)
            // SQLite/MySQL handling might differ. For safety in Laravel 6, we just let it be or try to modify.
            // Since `status` was enum ['pending', 'paid', 'overdue'], we want more statuses.
            // In MySQL we can use DB::statement, but for portability let's see.
            // If we can't easily change enum, we might drop and recreate or just use string.
            // Let's assume we can treat it as string or it accepts new values if not strictly validated by DB engine other than MySQL strict mode.
            // Or best practice: change to string or new enum.
        });

        // Since modifying ENUM is tricky across DBs, and to support new statuses:
        // 'pending', 'approved', 'rejected', 'disbursed', 'active', 'closed', 'cancelled'
        // We will try to alter it.
        DB::statement("ALTER TABLE nasabah_loans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'disbursed', 'active', 'closed', 'cancelled', 'paid', 'overdue') DEFAULT 'pending'");

        // Create nasabah_loan_installments table
        Schema::create('nasabah_loan_installments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nasabah_loan_id');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->enum('status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('penalty_amount', 15, 2)->default(0); // Denda
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('nasabah_loan_id')->references('id')->on('nasabah_loans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nasabah_loan_installments');

        Schema::table('nasabah_loans', function (Blueprint $table) {
            $table->dropColumn([
                'loan_type', 'interest_type', 'interest_rate', 'tenor',
                'admin_fee', 'disbursement_method',
                'approved_by', 'approved_at', 'disbursed_at', 'rejected_at', 'rejection_reason'
            ]);
        });

        // Reverting enum is complex, skipping for simplicity in this context as data might be incompatible.
    }
}
