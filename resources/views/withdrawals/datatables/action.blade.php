<a class="btn btn-sm btn-info" href="{{ route('withdrawals.show', $withdrawal->id) }}">{{ __('detail') }}</a>
<form action="{{ route('withdrawals.destroy', $withdrawal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('action_confirm_destroy') }}');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">{{ __('destroy') }}</button>
</form>
