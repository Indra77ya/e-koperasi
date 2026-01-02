@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <form action="{{ route('accounting.journals.store') }}" method="POST" class="card" id="journal-form">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Create Journal Entry</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="transaction_date" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Reference Number</label>
                            <input type="text" class="form-control" name="reference_number">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" required>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4">Journal Items</h4>
                <table class="table" id="items-table">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 2; $i++)
                        <tr>
                            <td>
                                <select class="form-control select2-account" name="items[{{$i}}][chart_of_account_id]" required style="width: 100%">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control input-debit" name="items[{{$i}}][debit]" placeholder="0">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control input-credit" name="items[{{$i}}][credit]" placeholder="0">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fe fe-trash"></i></button>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th><span id="total-debit">0.00</span></th>
                            <th><span id="total-credit">0.00</span></th>
                            <th><button type="button" class="btn btn-secondary btn-sm" id="add-row">Add Row</button></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Save Journal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
require(['jquery', 'select2'], function($) {

    function initSelect2() {
        $('.select2-account').select2();
    }

    initSelect2();

    let rowCount = 2;

    $('#add-row').click(function() {
        let html = `
            <tr>
                <td>
                    <select class="form-control select2-account" name="items[${rowCount}][chart_of_account_id]" required style="width: 100%">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control input-debit" name="items[${rowCount}][debit]" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control input-credit" name="items[${rowCount}][credit]" placeholder="0">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fe fe-trash"></i></button>
                </td>
            </tr>
        `;
        $('#items-table tbody').append(html);
        rowCount++;
        initSelect2();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calcTotal();
    });

    $(document).on('input', '.input-debit, .input-credit', function() {
        calcTotal();
    });

    function calcTotal() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('.input-debit').each(function() {
            totalDebit += parseFloat($(this).val() || 0);
        });
        $('.input-credit').each(function() {
            totalCredit += parseFloat($(this).val() || 0);
        });

        $('#total-debit').text(totalDebit.toFixed(2));
        $('#total-credit').text(totalCredit.toFixed(2));
    }
});
</script>
@endsection
