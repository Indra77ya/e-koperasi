<a class="btn btn-sm btn-info" href="{{ route('nasabahs.show', $nasabah->id) }}">{{ __('detail') }}</a>
<a class="btn btn-sm btn-primary" href="{{ route('nasabahs.edit', $nasabah->id) }}">{{ __('edit') }}</a>
<form action="{{ route('nasabahs.destroy', $nasabah->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('action_confirm_destroy') }}')">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
    <button class="btn btn-sm btn-danger">{{ __('destroy') }}</button>
</form>
