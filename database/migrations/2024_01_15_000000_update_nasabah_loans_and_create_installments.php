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
            $table->decimal('interest_rate', 5, 2)->after('interest_type')->default(0);
            $table->integer('tenor')->after('interest_rate')->default(1);
            $table->decimal('admin_fee', 15, 2)->after('tenor')->default(0);
            $table->enum('disbursement_method', ['cash', 'transfer'])->after('admin_fee')->default('cash');

            $table->unsignedBigInteger('approved_by')->nullable()->after('notes');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('disbursed_at')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('disbursed_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });

        // Safer approach for status: Change to string (varchar 20)
        // This avoids Enum complexities and allows us to set any status we want (e.g. 'disbursed', 'closed', etc.)
        DB::statement("ALTER TABLE nasabah_loans MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");

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
            $table->decimal('penalty_amount', 15, 2)->default(0);
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

        // Reverting status column type is tricky, skipping for dev context.
    }
}
