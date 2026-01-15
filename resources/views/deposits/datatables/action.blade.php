<a class="btn btn-sm btn-info" href="{{ route('deposits.show', $deposit->id) }}">{{ __('detail') }}</a>
<form action="{{ route('deposits.destroy', $deposit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('action_confirm_destroy') }}');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">{{ __('destroy') }}</button>
</form>
